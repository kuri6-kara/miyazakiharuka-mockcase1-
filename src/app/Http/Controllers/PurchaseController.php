<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * @param  string $item_id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(string $item_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = Item::find($item_id);

        if (!$item) {
            abort(404);
        }

        $user = Auth::user();

        $address_data = session('shipping_address') ?? [
            'postcode' => $user->postcode,
            'address' => $user->address,
            'building' => $user->building,
        ];

        return view('purchases.create', compact('item', 'user', 'address_data'));
    }

    /**
     * @param  string $item_id
     * @return \Illuminate\View\View
     */
    public function edit(string $item_id)
    {
        $item = Item::find($item_id);
        if (!$item) {
            abort(404);
        }

        $user = Auth::user();

        $address_data = session('shipping_address') ?? [
            'postcode' => $user->postcode,
            'address' => $user->address,
            'building' => $user->building,
        ];

        return view('purchases.edit', compact('item', 'address_data'));
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @param  string $item_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $item_id)
    {
        $request->session()->put('shipping_address', $request->all());

        return redirect()->route('purchase.create', ['item_id' => $item_id])
            ->with('status', '配送先情報を更新しました。');
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @param  string $item_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, string $item_id)
    {
        return redirect()->route('item.show', ['item_id' => $item_id])
            ->with('status', '商品を購入しました！');
    }
}
