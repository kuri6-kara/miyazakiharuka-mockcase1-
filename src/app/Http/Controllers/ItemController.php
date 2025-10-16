<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use App\Models\Category;
use Illuminate\Http\Request;
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

        $tab = $request->input('tab', 'recommend');
        $query = Item::query();
        $no_items_message = null; // メッセージを初期化

        if ($tab == 'mylist') {
            if (Auth::check()) {
                // 認証済みの場合、いいねした商品のIDを取得
                $likedItems = Auth::user()->likes()->pluck('item_id');
                $query->whereIn('id', $likedItems);

                // いいねした商品がない場合のメッセージ
                if ($likedItems->isEmpty()) {
                    $no_items_message = 'いいねした商品はありません';
                }
            } else {
                // 未認証の場合、空のコレクションを返し、ビューでログインを促す
                $items = collect();
                // メッセージはビュー側で処理されます
            }
        } else {
            // おすすめタブの場合
            if (Auth::check()) {
                // 自分の出品した商品は除外
                $query->where('user_id', '!=', Auth::id());
            }
        }

        // 商品の取得と販売済みフラグの設定
        if (!isset($items)) {
            $items = $query->get();
        }

        $items = $items->map(function ($item) {
            $item->is_sold = $item->purchases->isNotEmpty();
            return $item;
        });

        // no_items_message をビューに渡す
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

        // is_sold プロパティを追加
        $item->is_sold = $item->purchases->isNotEmpty();

        return view('items.show', compact('item'));
    }


    /**
     * 商品出品フォームを表示
     */
    public function create()
    {
        // 実際にはDBから取得すべきカテゴリと商品の状態のリスト
        $categories = [
            ['id' => 1, 'category' => 'ファッション'],
            ['id' => 2, 'category' => '家電'],
            ['id' => 3, 'category' => 'インテリア'],
            ['id' => 4, 'category' => 'レディース'],
            ['id' => 5, 'category' => 'メンズ'],
            ['id' => 6, 'category' => 'コスメ'],
            ['id' => 7, 'category' => '本'],
            ['id' => 8, 'category' => 'ゲーム'],
            ['id' => 9, 'category' => 'スポーツ'],
            ['id' => 10, 'category' => 'キッチン'],
            ['id' => 11, 'category' => 'ハンドメイド'],
            ['id' => 12, 'category' => 'アクセサリー'],
            ['id' => 13, 'category' => 'おもちゃ'],
            ['id' => 14, 'category' => 'ベビー・キッズ'],
        ];

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
    public function store(Request $request)
    {
        // 1. バリデーション
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'integer', 'min:100', 'max:9999999'],
            'condition' => ['required', 'string'],
            'categories' => ['required', 'array', 'min:1'], // 複数カテゴリのID
            // 'categories.*' => ['integer', 'exists:categories,id'], // DB連携時はこのexistsバリデーションが必要
            'item_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 画像のバリデーション
            'brand_name' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => '商品名は必須です。',
            'description.required' => '商品の説明は必須です。',
            'price.required' => '販売価格は必須です。',
            'price.integer' => '販売価格は数値で入力してください。',
            'price.min' => '販売価格は100円以上で入力してください。',
            'condition.required' => '商品の状態は必須です。',
            'categories.required' => 'カテゴリは1つ以上選択してください。',
            'item_image.required' => '商品画像は必須です。',
            'item_image.image' => 'ファイルは画像である必要があります。',
            'item_image.max' => '画像サイズは2MB以内である必要があります。',
        ]);

        // 2. 画像の保存
        // storage/app/public/items ディレクトリに保存
        $path = $request->file('item_image')->store('public/items');
        // DB保存用に 'public/' を除いたパスに変換
        $item_image_path = str_replace('public/', '', $path);


        try {
            DB::transaction(function () use ($validated, $item_image_path, $request) {
                // 3. Itemテーブルへの保存
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

                // 4. カテゴリの中間テーブルへの保存
                // attach() メソッドで中間テーブル(category_item)にデータを挿入
                $item->categories()->attach($validated['categories']);
            });
        } catch (\Exception $e) {
            // エラー時は保存した画像を削除
            Storage::delete($path);
            // ユーザーに分かりやすいエラーメッセージを表示
            throw ValidationException::withMessages(['error' => '出品情報の保存中にエラーが発生しました。時間を置いて再度お試しください。']);
        }

        // 出品完了後、商品一覧画面にリダイレクト
        return redirect()->route('item.index')->with('success', '商品を出品しました！');
    }
}
