<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->has('shipping_address')) {
            $request->session()->forget('shipping_address');
        }

        $tab = $request->input('tab', 'recommend');
        $query = Item::query();

        if ($tab == 'mylist') {
            if (Auth::check()) {
                $likedItems = Auth::user()->likes()->pluck('item_id');
                $query->whereIn('id', $likedItems);
            } else {
                $items = collect();
                return view('items.index', compact('items'));
            }
        } else {
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
        }

        $items = $query->get();

        $items = $items->map(function ($item) {
            $item->is_sold = $item->purchases->isNotEmpty();
            return $item;
        });


        return view('items.index', compact('items'));
    }

    public function show(string $item_id)
    {
        $item = Item::with(['user', 'categories', 'likes', 'comments'])->find($item_id);
        if (!$item) {
            abort(404);
        }

        return view('items.show', compact('item'));
    }
}
