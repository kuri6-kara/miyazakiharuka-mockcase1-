<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
// use App\Http\Requests\AddressUpdateRequest; // バリデーションリクエストは後で作成

class PurchaseController extends Controller
{
    /**
     * 購入画面を表示
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

        // 変更された住所がセッションにあるか確認し、なければユーザーのデフォルト住所を使用
        $address_data = session('shipping_address') ?? [
            'postal_code' => $user->postal_code,
            'address' => $user->address,
            'building' => $user->building,
        ];

        return view('purchases.create', compact('item', 'user', 'address_data'));
    }

    /**
     * 配送先変更画面を表示 (GET /purchase/address/{item_id})
     * アクション名: edit
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

        // セッションに保存されている一時的な住所があれば取得、なければユーザーのデフォルトを使用
        // ビュー側でフォームの初期値として利用
        $address_data = session('shipping_address') ?? [
            'postal_code' => $user->postal_code,
            'address' => $user->address,
            'building' => $user->building,
        ];

        // ビューファイル名はそのまま address_edit を使用します
        return view('purchases.edit', compact('item', 'address_data'));
    }

    /**
     * 配送先情報をセッションに一時保存し、購入画面に戻る (POST /purchase/address/{item_id})
     * アクション名: update
     * @param  \Illuminate\Http\Request $request
     * @param  string $item_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $item_id)
    {

        // ユーザーテーブルの住所は変更せず、セッションに一時的に保存する
        $request->session()->put('shipping_address', $validated);

        // 購入画面に戻る
        return redirect()->route('purchase.create', ['item_id' => $item_id])
            ->with('status', '配送先情報を更新しました。');
    }

    /**
     * 注文を確定する
     * @param  \Illuminate\Http\Request $request
     * @param  string $item_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, string $item_id)
    {
        // TODO: 支払い方法や配送先などのバリデーション

        // ここでセッションに保存された住所情報も取得して一緒に保存する

        // 処理が成功したら、完了画面などにリダイレクト
        return redirect()->route('item.show', ['item_id' => $item_id])
            ->with('status', '商品を購入しました！');
    }
}
