<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * @param string
     * @return
     */
    public static function index(string $tab): View
    {
        $user = Auth::user();
        $items = collect();

        if ($tab === 'sell') {
            $items = $user->items;
            return view('profile.sold_items', compact('items', 'tab'));
        } elseif ($tab === 'buy') {
            $items = $user->purchases;
            return view('profile.purchased_items', compact('items', 'tab'));
        }

        return view('profile.sold_items', compact('items', 'tab'));
    }
}
