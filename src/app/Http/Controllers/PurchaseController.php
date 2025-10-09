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

        if (!$item || $item->is_sold) { // SOLDアウト商品は購入不可
            abort(404);
        }

        $payment_methods = PaymentMethod::all();

        $user = Auth::user();

        $address_data = session('shipping_address') ?? [
            'postcode' => $user->postcode,
            'address' => $user->address,
            'building' => $user->building,
        ];

        return view('purchases.create', compact('item', 'user', 'address_data', 'payment_methods'));
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
     * 購入確定処理。Stripe決済への接続もここで行う。
     *
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

        // セッションから配送先情報を取得。セッションになければユーザーの登録情報を使用
        $address_data = $request->session()->get('shipping_address', []);
        $payment_method_id = $request->input('payment_method_id');

        // 支払い方法のIDを取得
        $paymentMethod = PaymentMethod::find($payment_method_id);

        // トランザクション開始
        DB::beginTransaction();

        try {
            // 1. 購入データの保存
            Purchase::create([
                'user_id' => Auth::id(),
                'item_id' => $item_id,
                'payment_method_id' => $payment_method_id,
                // 配送先はセッションデータまたはユーザーデータから取得
                'postcode' => $address_data['postcode'] ?? Auth::user()->postcode,
                'address' => $address_data['address'] ?? Auth::user()->address,
                'building' => $address_data['building'] ?? Auth::user()->building,
            ]);

            // 2. 商品ステータスをSOLDに変更
            $item->update(['is_sold' => true]);

            // 3. セッションの配送先情報をクリア
            $request->session()->forget('shipping_address');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // エラーログの記録（開発用）
            // \Log::error("Purchase failed: " . $e->getMessage()); 
            return redirect()->back()->withErrors(['db' => '購入処理中にエラーが発生しました。']);
        }

        // 4. 支払い方法に応じた処理 (Stripe連携)
        if ($paymentMethod && ($paymentMethod->payment_method === 'カード払い' || $paymentMethod->payment_method === 'コンビニ払い')) {
            // 実際にはStripe Checkoutセッションを作成し、リダイレクトURLを返す
            return redirect()->route('item.index')
                ->with('status', $paymentMethod->payment_method . 'の決済画面へ接続しました。（デモのため、一覧画面へ戻ります）');
        }

        // その他の支払い方法（通常は発生しない想定だが、念のため一覧へ）
        return redirect()->route('item.index')
            ->with('status', '商品を購入し、SOLDステータスに更新しました！');
    }
}
