<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        if (!$item || $item->is_sold) {
            abort(404);
        }

        $payment_methods = PaymentMethod::all();

        $user = Auth::user();

        $address_data = session('shipping_address') ?? [
            'postcode' => $user->postcode,
            'address' => $user->address,
            'building' => $user->building,
        ];

        $selected_payment_method_id = session('selected_payment_method_id');

        if ($selected_payment_method_id) {
            session()->forget('selected_payment_method_id');
        }

        return view('purchases.create', compact('item', 'user', 'address_data', 'payment_methods', 'selected_payment_method_id'));
    }

    /**
     * @param  string $item_id
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function edit(string $item_id, Request $request)
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

        $selected_payment_method_id = $request->query('payment_method_id');

        return view('purchases.edit', compact('item', 'address_data', 'selected_payment_method_id'));
    }

    /**
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $item_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $item_id)
    {
        $request->validate([
            'postcode' => 'required|string|regex:/^\d{3}-?\d{4}$/',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        $request->session()->put('shipping_address', $request->only(['postcode', 'address', 'building']));

        if ($request->filled('payment_method_id')) {
            $request->session()->put('selected_payment_method_id', $request->input('payment_method_id'));
        }

        return redirect()->route('purchase.create', ['item_id' => $item_id])
            ->with('status', '配送先情報を更新しました。');
    }

    /**
     * @param  \App\Http\Requests\PurchaseRequest $request
     * @param  string $item_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PurchaseRequest $request, string $item_id)
    {
        $item = Item::find($item_id);

        if (!$item || $item->is_sold) {
            return redirect()->back()->withErrors(['item' => 'この商品はすでに売り切れました。']);
        }

        $address_data = $request->session()->get('shipping_address', []);
        $payment_method_id = $request->input('payment_method_id');

        $paymentMethod = PaymentMethod::find($payment_method_id);

        DB::beginTransaction();

        try {
            Purchase::create([
                'user_id' => Auth::id(),
                'item_id' => $item_id,
                'payment_method_id' => $payment_method_id,
                'postcode' => $address_data['postcode'] ?? Auth::user()->postcode,
                'address' => $address_data['address'] ?? Auth::user()->address,
                'building' => $address_data['building'] ?? Auth::user()->building,
            ]);

            $item->is_sold = true;
            $item->update();

            $request->session()->forget('shipping_address');
            $request->session()->forget('selected_payment_method_id');


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['db' => '購入処理中にエラーが発生しました。']);
        }

        if ($paymentMethod && ($paymentMethod->payment_method === 'カード払い' || $paymentMethod->payment_method === 'コンビニ払い')) {
            return redirect()->route('item.index')
                ->with('status', $paymentMethod->payment_method . 'の決済画面へ接続しました。（デモのため、一覧画面へ戻ります）');
        }

        return redirect()->route('item.index')
            ->with('status', '商品を購入し、SOLDステータスに更新しました！');
    }
}
