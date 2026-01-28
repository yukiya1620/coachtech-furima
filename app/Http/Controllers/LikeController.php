<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;

class LikeController extends Controller
{
    public function toggle(Item $item)
    {
        $userId = auth()->id();

        $query = Like::where('user_id', $userId)->where('item_id', $item->id);

        if ($query->exists()) {
            $query->delete();
        } else {
            Like::create([
                'user_id' => $userId,
                'item_id' => $item->id,
            ]);
        }

        return redirect()->route('items.show', $item->id);
    }
}
