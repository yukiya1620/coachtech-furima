<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request) {
        $tab = $request->query('tab', 'recommend');
        $tab = in_array($tab, ['recommend', 'mylist']) ? $tab : 'recommend';

        $keyword = $request->query('keyword');
        
        if ($tab === 'mylist') {
            if (auth()->check()) {
                $query = Item::whereHas('likes', function ($q) {
                    $q->where('user_id', auth()->id());
                });
            } else {
                $items = collect();
                return view('items.index', compact('items', 'tab', 'keyword'));
            }
        } else {
            $query = Item::query()->latest();
            $tab = 'recommend';
        }

        if(!empty($keyword)) {
            $escaped = addcslashes($keyword, '%_\\');
            $query->where('name', 'like', "%{$escaped}%");
        }

        $items = $query->latest()->get();
        
        return view('items.index', compact('items', 'tab', 'keyword'));
    }

    public function show(Item $item)
    {
        $item->load([
            'user',
            'categories',
            'comments.user',
        ])->loadCount([
            'likes',
            'comments',
        ]);

        $liked = auth()->check()
            ? $item->isLikedBy(auth()->user())
            : false;

        return view('items.show', compact('item', 'liked'));
    }
}
