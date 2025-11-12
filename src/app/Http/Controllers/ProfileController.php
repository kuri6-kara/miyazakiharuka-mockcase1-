<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * @param string $tab 'sell' または 'buy'
     * @return \Illuminate\View\View
     */
    public static function index(string $tab): View
    {
        $user = Auth::user();
        $items = collect();

        if ($tab === 'sell') {
            $items = $user->items()->orderBy('created_at', 'desc')->get();

            $items = $items->map(function ($item) {
                $item->is_sold = $item->purchases->isNotEmpty();
                return $item;
            });

            return view('profile.sold_items', compact('items', 'tab'));
        } elseif ($tab === 'buy') {
            $items = $user->purchases()->orderBy('created_at', 'desc')->get();

            return view('profile.purchased_items', compact('items', 'tab'));
        }

        $items = $user->items()->orderBy('created_at', 'desc')->get();
        return view('profile.sold_items', compact('items', 'tab'));
    }
}
