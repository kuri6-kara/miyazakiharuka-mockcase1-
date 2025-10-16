<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->has('shipping_address')) {
            $request->session()->forget('shipping_address');
        }

        $tab = $request->input('tab', 'recommend');
        $query = Item::query();
        $no_items_message = null;

        if ($tab == 'mylist') {
            if (Auth::check()) {
                $likedItems = Auth::user()->likes()->pluck('item_id');
                $query->whereIn('id', $likedItems);

                if ($likedItems->isEmpty()) {
                    $no_items_message = 'いいねした商品はありません';
                }
            } else {
                $items = collect();
                $no_items_message = 'ログインまたは新規会員登録してください';
                return view('items.index', compact('items', 'no_items_message'));
            }
        } else {
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
        }

        $items = $query->get();

        $items = $items->map(function ($item) {
            $item->is_sold = $item->purchases->isNotEmpty();
            return $item;
        });

        return view('items.index', compact('items', 'no_items_message'));
    }

    public function show(string $item_id)
    {
        $item = Item::with([
            'user',
            'categories',
            'likes',
            'comments' => function ($query) {
                $query->orderBy('id', 'desc');
            }
        ])->find($item_id);
        if (!$item) {
            abort(404);
        }

        return view('items.show', compact('item'));
    }

    /**
     * 商品出品フォームを表示する
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // 実際にはここでカテゴリや商品の状態などのマスタデータを取得する
        return view('items.create');
    }

    /**
     * 商品の出品処理（POST）
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // TODO: バリデーションと画像のアップロード、DBへの保存ロジックを実装

        // 仮の実装として、商品一覧へリダイレクト
        return redirect()->route('item.index')
            ->with('status', '【出品処理待ち】出品機能を実装中です。');
    }
}
