<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Purchase;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 'sell');
        $mode = in_array($page, ['buy', 'sell']) ? $page : 'sell';

        $user = Auth::user();
        $items = collect();

        if ($mode === 'sell') {
            $items = Item::where('user_id', $user->id)
                ->latest()
                ->get();
        }

        if ($mode === 'buy') {
            $itemIds = \App\Models\Purchase::where('user_id', $user->id)
                 ->pluck('item_id');

            $items = Item::whereIn('id', $itemIds)
                 ->latest()
                 ->get();
        }

        return view('mypage.index', compact('mode', 'items', 'user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'name.required' => 'ユーザー名を入力してください。',
            'profile_image.image' => '画像ファイルを選択してください。',
            'profile_image.mimes' => '画像はjpg/jpeg/png形式でアップロードしてください。',
            'profile_image.max' => '画像サイズは2MB以内にしてください。',
        ]);
        
        if ($request->hasFile('profile_image')) {
                    
            if (!empty($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
                    
            $path = $request->file('profile_image')->store('profiles', 'public');
                    
            $validated['profile_image'] = $path;
        }
                
        $user->update($validated);
                
        return redirect()
        ->route('items.index')
        ->with('success', 'プロフィールを更新しました。');
    }

}
