<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->has('shipping_address')) {
            $request->session()->forget('shipping_address');
        }

        $default_tab = 'recommend';
        if (! $request->filled('tab') && Auth::check()) {
            $default_tab = 'mylist';
        }
        $tab = $request->input('tab', $default_tab);

        $keyword = $request->input('keyword');
        $no_items_message = null;

        $items = collect();

        if ($tab == 'mylist') {
            if (Auth::check()) {
                $user_id = Auth::id();

                $likedLikes = Auth::user()->likes()
                    ->orderBy('created_at', 'desc')
                    ->get();

                $likedItemIds = $likedLikes->pluck('item_id')->toArray();

                if (empty($likedItemIds)) {
                    $no_items_message = 'いいねした商品はありません';
                } else {
                    $itemQuery = Item::whereIn('id', $likedItemIds);

                    $itemQuery->where('user_id', '!=', $user_id);

                    if (!empty($keyword)) {
                        $itemQuery->where('name', 'LIKE', '%' . $keyword . '%');
                    }

                    $items = $itemQuery->get();

                    $items = $items->sortBy(function ($item) use ($likedItemIds) {
                        return array_search($item->id, $likedItemIds);
                    })->values();

                    if ($items->isEmpty()) {
                        if (!empty($keyword)) {
                            $no_items_message = '「' . $keyword . '」に一致する商品はありません';
                        } elseif (count($likedItemIds) > 0) {
                            $no_items_message = 'いいねした商品はありますが、自分の出品した商品はマイリストには表示されません。';
                        } else {
                            $no_items_message = 'いいねした商品はありません';
                        }
                    }
                }
            } else {
                $items = collect();
            }
        } else {
            $itemQuery = Item::query();

            if (Auth::check()) {
                $itemQuery->where('user_id', '!=', Auth::id());
            }

            $itemQuery->orderBy('created_at', 'desc');

            if (!empty($keyword)) {
                $itemQuery->where('name', 'LIKE', '%' . $keyword . '%');
            }

            $items = $itemQuery->get();

            if ($items->isEmpty() && !empty($keyword)) {
                $no_items_message = '「' . $keyword . '」に一致する商品はありません';
            }
        }

        $items = $items->map(function ($item) {
            $item->is_sold = $item->purchases->isNotEmpty();
            return $item;
        });

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

        $item->is_sold = $item->purchases->isNotEmpty();

        return view('items.show', compact('item'));
    }

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

    public function store(ItemRequest $request)
    {
        $validated = $request->validated();

        $path = $request->file('item_image')->store('public/items');
        $item_image_path = str_replace('public/', '', $path);


        try {
            DB::transaction(function () use ($validated, $item_image_path, $request) {
                $item = Item::create([
                    'user_id' => Auth::id(),
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'price' => $validated['price'],
                    'condition' => $validated['condition'],
                    'item_image_path' => $item_image_path,
                    'brand_name' => $request->input('brand_name'),
                ]);

                $item->categories()->attach($validated['categories']);
            });
        } catch (\Exception $e) {
            Storage::delete($path);
            return redirect()->back()->withErrors(['error' => '出品情報の保存中にエラーが発生しました。時間を置いて再度お試しください。'])->withInput();
        }

        return redirect()->route('item.index')->with('success', '商品を出品しました！');
    }
}
