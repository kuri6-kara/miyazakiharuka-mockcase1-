<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like; // Likeモデルをuseに追加
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // セッションから配送先住所をクリア
        if ($request->session()->has('shipping_address')) {
            $request->session()->forget('shipping_address');
        }

        // --- ★★★ ここから初期タブ表示のロジックを変更 ★★★ ---
        $default_tab = 'recommend';
        if (! $request->filled('tab') && Auth::check()) {
            // tabパラメータがなく、かつログインしている場合、初期タブを 'mylist' にする
            $default_tab = 'mylist';
        }
        $tab = $request->input('tab', $default_tab);
        // --- ★★★ 初期タブ表示のロジック変更ここまで ★★★ ---


        $keyword = $request->input('keyword');
        $no_items_message = null; // メッセージを初期化

        // 商品のコレクションを初期化
        $items = collect();

        if ($tab == 'mylist') {
            if (Auth::check()) {
                // 認証済みの場合
                // 1. ユーザーがいいねしたLikeレコードを、いいね日時（created_at）が新しい順に取得
                $likedLikes = Auth::user()->likes()
                    ->orderBy('created_at', 'desc') // ★★★ いいねした日時で降順に並び替え ★★★
                    ->get();

                $likedItemIds = $likedLikes->pluck('item_id')->toArray();

                // いいねした商品IDが空の場合のメッセージ
                if (empty($likedItemIds)) {
                    $no_items_message = 'いいねした商品はありません';
                } else {
                    // 2. 取得したIDの商品をwhereInで絞り込む
                    $itemQuery = Item::whereIn('id', $likedItemIds);

                    // 3. キーワード検索（マイリストの絞り込み後、さらにキーワードで絞り込む）
                    if (!empty($keyword)) {
                        $itemQuery->where('name', 'LIKE', '%' . $keyword . '%');
                    }

                    // 4. クエリ実行して商品を取得
                    $items = $itemQuery->get();

                    // 5. 取得した商品をlikedItemIdsの順序（いいね順）に並び替える
                    // whereInで取得した順序は保証されないため、手動でソートする
                    $items = $items->sortBy(function ($item) use ($likedItemIds) {
                        return array_search($item->id, $likedItemIds);
                    })->values();

                    // キーワード検索またはいいね数が0で結果が0になった場合のメッセージをチェック
                    if ($items->isEmpty()) {
                        if (!empty($keyword)) {
                            // キーワード検索により結果が0になった場合
                            $no_items_message = '「' . $keyword . '」に一致する商品はありません';
                        }
                    }
                }
            } else {
                // 未認証の場合、空のコレクションを返す
                $items = collect();
                // 未認証でマイリストタブを選択した場合、おすすめタブの処理にフォールバックさせたい場合は、
                // ここで $tab = 'recommend'; として、以下の else { // おすすめタブの場合 } に処理を移すことも可能ですが、
                // 今回はシンプルに空コレクションとします。
            }
        } else {
            // おすすめタブの場合 (または未認証時にマイリストが選択されたが空だった場合のフォールバック)
            $itemQuery = Item::query(); // 新しいクエリインスタンスを作成

            if (Auth::check()) {
                // 自分の出品した商品は除外
                $itemQuery->where('user_id', '!=', Auth::id());
            }

            // ★★★ 出品日時で降順 (新しい順) に並び替える ★★★
            $itemQuery->orderBy('created_at', 'desc');

            // キーワード検索
            if (!empty($keyword)) {
                $itemQuery->where('name', 'LIKE', '%' . $keyword . '%');
            }

            // 商品の取得
            $items = $itemQuery->get();

            // 絞り込みを行った結果、アイテムがなかった場合のメッセージ
            if ($items->isEmpty() && !empty($keyword)) {
                $no_items_message = '「' . $keyword . '」に一致する商品はありません';
            }
        }

        // 商品の販売済みフラグの設定 (どのタブでも共通)
        $items = $items->map(function ($item) {
            $item->is_sold = $item->purchases->isNotEmpty();
            return $item;
        });

        // no_items_message と keyword, tab をビューに渡す
        return view('items.index', compact('items', 'no_items_message', 'keyword', 'tab'));
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

        // is_sold プロパティを追加
        $item->is_sold = $item->purchases->isNotEmpty();

        return view('items.show', compact('item'));
    }


    /**
     * 商品出品フォームを表示
     */
    public function create()
    {
        $categories = Category::all();

        $conditions = [
            '新品、未使用',
            '未使用に近い',
            '目立った傷や汚れなし',
            'やや傷や汚れあり',
            '傷や汚れあり',
            '全体的に状態が悪い',
        ];

        return view('items.create', compact('categories', 'conditions'));
    }

    /**
     * 商品出品情報をデータベースに保存
     */
    public function store(ItemRequest $request)
    {
        // ItemRequest が自動的にバリデーションを実行します。
        $validated = $request->validated(); // バリデーション済みのデータを取得

        // 1. 画像の保存
        // storage/app/public/items ディレクトリに保存
        $path = $request->file('item_image')->store('public/items');
        // DB保存用に 'public/' を除いたパスに変換
        $item_image_path = str_replace('public/', '', $path);


        try {
            DB::transaction(function () use ($validated, $item_image_path, $request) {
                // 2. Itemテーブルへの保存
                $item = Item::create([
                    'user_id' => Auth::id(), // 認証済みユーザー
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'price' => $validated['price'],
                    'condition' => $validated['condition'],
                    'item_image_path' => $item_image_path,
                    // 任意項目
                    'brand_name' => $request->input('brand_name'),
                ]);

                // 3. カテゴリの中間テーブルへの保存
                // attach() メソッドで中間テーブル(category_item)にデータを挿入
                $item->categories()->attach($validated['categories']);
            });
        } catch (\Exception $e) {
            // エラー時は保存した画像を削除
            Storage::delete($path);
            // ユーザーに分かりやすいエラーメッセージを表示
            // throw ValidationException::withMessages(['error' => '出品情報の保存中にエラーが発生しました。時間を置いて再度お試しください。']);
            // エラーを適切にログに記録し、汎用的なエラーメッセージを返す
            return redirect()->back()->withErrors(['error' => '出品情報の保存中にエラーが発生しました。時間を置いて再度お試しください。'])->withInput();
        }

        // 出品完了後、商品一覧画面にリダイレクト
        return redirect()->route('item.index')->with('success', '商品を出品しました！');
    }
}

