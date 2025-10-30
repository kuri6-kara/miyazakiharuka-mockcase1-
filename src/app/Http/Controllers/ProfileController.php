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
            // ★ 修正箇所: 出品した商品を新しい順 (created_at desc) に並び替え ★
            // Userモデルのitemsリレーションに対してorderByを適用
            $items = $user->items()->orderBy('created_at', 'desc')->get();

            // Bladeでの SOLD バッジ表示のために is_sold フラグを設定
            $items = $items->map(function ($item) {
                $item->is_sold = $item->purchases->isNotEmpty();
                return $item;
            });

            return view('profile.sold_items', compact('items', 'tab'));
        } elseif ($tab === 'buy') {
            // ★ 修正箇所: 購入した商品を新しい順 (purchasesテーブルの created_at desc) に並び替え ★
            // Userモデルのpurchasesリレーションに対してorderByを適用
            $items = $user->purchases()->orderBy('created_at', 'desc')->get();

            return view('profile.purchased_items', compact('items', 'tab'));
        }

        // デフォルトとして 'sell' を表示（並び替え済みの $items を使用）
        $items = $user->items()->orderBy('created_at', 'desc')->get();
        return view('profile.sold_items', compact('items', 'tab'));
    }
}
