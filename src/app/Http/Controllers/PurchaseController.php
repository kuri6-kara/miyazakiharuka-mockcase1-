<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * @param  string
     * @return
     */
    public function create(string $item_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = Item::find($item_id);

        // 商品が存在しない場合は404エラー
        if (!$item) {
            abort(404);
        }

        return view('purchases.create', compact('item'));
    }

    /**
     * @param
     * @param  string
     * @return
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
