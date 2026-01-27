<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;

class SellController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();

        $conditions = [
            'good'  => '良好',
            'clean' => '目立った傷や汚れなし',
            'fair'  => 'やや傷や汚れあり',
            'bad'   => '状態が悪い',
        ];

        return view('sell.create', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        $path = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'brand' => $validated['brand'] ?? null,
            'price' => $validated['price'],
            'condition' => $validated['condition'],
            'description' => $validated['description'],
            'image_path' => $path,
            'is_sold' => false,
        ]);

        $item->categories()->sync($validated['categories']);

        return redirect()
            ->route('mypage.index', ['page' => 'sell'])
            ->with('success', '商品を出品しました。');
    }
}
