<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index()
    {
        $query = Item::query();

        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        $items = $query->get();

        return view('items.index', compact('items'));
    }

    public function show(string $item_id)
    {
        $item = Item::find($item_id);
        if (!$item) {
            abort(404);
        }

        return view('items.show', compact('item'));
    }
}
