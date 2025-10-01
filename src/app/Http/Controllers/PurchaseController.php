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
        // 認証チェックはルーティングのミドルウェアで行われているが、念のため残す
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = Item::find($item_id);

        // 商品が存在しない場合は404エラー
        if (!$item) {
            abort(404);
        }

        // ★★★ 修正箇所: ログインユーザーの情報を取得 ★★★
        $user = Auth::user();

        // 商品情報とユーザー情報をビューに渡す
        return view('purchases.create', compact('item', 'user'));
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @param  string $item_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, string $item_id)
    {
        // TODO: 支払い方法や配送先などのバリデーション

        // ここに決済処理とデータベースへの購入記録の保存ロジックが入る
        // 例: Purchase::create(['user_id' => Auth::id(), 'item_id' => $item_id, ...]);

        // 処理が成功したら、完了画面などにリダイレクト
        return redirect()->route('item.show', ['item_id' => $item_id])
            ->with('status', '商品を購入しました！');
    }
}
