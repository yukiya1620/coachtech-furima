<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Illuminate\Http\Exceptions\HttpResponseException;


class PurchaseController extends Controller
{
    public function create(Item $item)
    {
        $this->guardNotSold($item);

        $paymentMethods = [
            'convenience' => 'コンビニ払い',
        ];

        if (!empty(config('services.stripe.secret'))) {
            $paymentMethods['credit'] = 'クレジットカード';
        }

        $user = auth()->user();

        $shipping = session()->get("shipping.item.{$item->id}");
        
        if (!$shipping) {
            $shipping = [
                'postal_code' => $user->postcode ?? '',
                'address'     => $user->address ?? '',
                'building'    => $user->building ?? '',
            ];
        }

        return view('purchase.create', compact('item', 'paymentMethods', 'shipping'));
    }

    public function editAddress(Item $item)
    {
        $this->guardNotSold($item);

        $shipping = session()->get("shipping.item.{$item->id}", [
            'postal_code' => '',
            'address'     => '',
            'building'    => '',
        ]);

        return view('purchase.address', compact('item', 'shipping'));
    }

    public function updateAddress(AddressRequest $request, Item $item)
    {
        $this->guardNotSold($item);

        session()->put("shipping.item.{$item->id}", $request->validated());

        return redirect()->route('purchase.create', $item->id);
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        $this->guardNotSold($item);
        
        $user = auth()->user();
        
        $shipping = session()->get("shipping.item.{$item->id}");
        if (!$shipping) {
            $shipping = [
                'postal_code' => $user->postcode ?? '',
                'address'     => $user->address ?? '',
                'building'    => $user->building ?? '',
            ];
        }
        
        if (empty($shipping['postal_code']) || empty($shipping['address'])) {
            return redirect()
               ->route('purchase.address.edit', $item->id)
               ->with('error', '配送先住所を入力してください。');
        }
        
        $method = $request->validated()['payment_method'];
        
        //  コンビニ：Stripeなしで即完了
        if ($method === 'convenience') {
            DB::transaction(function () use ($item, $method, $shipping) {
                $lockedItem = Item::whereKey($item->id)->lockForUpdate()->firstOrFail();
                if ($lockedItem->is_sold) abort(403);
                
                Purchase::create([
                    'user_id' => auth()->id(),
                    'item_id' => $lockedItem->id,
                    'payment_method' => $method,
                    'shipping_postal_code' => $shipping['postal_code'],
                    'shipping_address'     => $shipping['address'],
                    'shipping_building'    => $shipping['building'] ?? null,
                ]);
                
                $lockedItem->update(['is_sold' => true]);
            });
            
            session()->forget("shipping.item.{$item->id}");
            
            return redirect()
               ->route('items.show', $item->id)
               ->with('success', '購入が完了しました！');
        }
        
        //  クレカ：Stripe Checkoutへ
        $stripeSecret = config('services.stripe.secret');

        if (empty($stripeSecret)) {
            return back()
               ->withInput()
               ->withErrors([
                   'payment_method' => 'クレジットカード決済(stripe)が未設定のため利用できません。コンビニ払いをご利用ください。'
               ]);
        }

        Stripe::setApikey($stripeSecret);
        
        try {
            $session = CheckoutSession::create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $item->name],
                        'unit_amount' => (int) $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'success_url' => route('purchase.success', $item->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('purchase.cancel', $item->id),
                'customer_email' => $user->email ?? null,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Stripe CheckoutSession create failed', ['error' => $e->getMessage()]);
            return back()
               ->withInput()
               ->withErrors([
                   'payment_method' => 'クレジット決済の準備に失敗しました。時間をおいて再度お試しください。',
               ]);
        }
        
        session()->put("stripe.session.item.{$item->id}", $session->id);
        
        return redirect($session->url);
    }
    
    public function success(\Illuminate\Http\Request $request, Item $item)
    {
        $sessionId = $request->query('session_id');
        $saved = session()->get("stripe.session.item.{$item->id}");
        
        if (!$sessionId || ($saved && $sessionId !== $saved)) {
            return redirect()->route('items.show', $item->id)
               ->with('error', '決済情報が確認できませんでした。');
        }
        
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = CheckoutSession::retrieve($sessionId);
        
        if (($session->payment_status ?? null) !== 'paid') {
            return redirect()->route('items.show', $item->id)
               ->with('error', '決済が完了していません。');
        }
        
        $user = auth()->user();
        $shipping = session()->get("shipping.item.{$item->id}") ?? [
            'postal_code' => $user->postcode ?? '',
            'address'     => $user->address ?? '',
            'building'    => $user->building ?? '',
        ];
        
        DB::transaction(function () use ($item, $shipping) {
            $lockedItem = Item::whereKey($item->id)->lockForUpdate()->firstOrFail();
            if ($lockedItem->is_sold) return;
            
            Purchase::create([
                'user_id' => auth()->id(),
                'item_id' => $lockedItem->id,
                'payment_method' => 'credit',
                'shipping_postal_code' => $shipping['postal_code'],
                'shipping_address'     => $shipping['address'],
                'shipping_building'    => $shipping['building'] ?? null,
            ]);
            
            $lockedItem->update(['is_sold' => true]);
        });
        
        session()->forget("shipping.item.{$item->id}");
        session()->forget("stripe.session.item.{$item->id}");
        
        return redirect()->route('items.show', $item->id)
           ->with('success', '購入が完了しました！');
    }
    
    public function cancel(Item $item)
    {
        session()->forget("stripe.session.item.{$item->id}");
        
        return redirect()->route('purchase.create', $item->id)
           ->with('error', '決済をキャンセルしました。');
    }

    private function guardNotSold(Item $item): void
    {
        if ($item->is_sold) {
            throw new HttpResponseException(
                redirect()
                   ->route('items.show', $item->id)
                   ->with('error', '売り切れの商品は購入できません。'));
        }
    }
}
