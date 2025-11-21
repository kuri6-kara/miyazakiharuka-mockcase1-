<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Like;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * マイリスト機能の機能テスト
 */
class Case05_MylistTest extends TestCase
{
    use RefreshDatabase;

    private $category;
    private $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create(['category' => 'テストカテゴリ']);

        $this->paymentMethod = PaymentMethod::create([
            'payment_method' => 'テスト決済方法',
        ]);
    }

    /**
     * @param array $attributes
     * @return \App\Models\Item
     */
    private function createItem(array $attributes = [])
    {
        $default = [
            'condition' => '新品、未使用',
            'name' => 'デフォルト商品',
            'price' => 100,
            'description' => '説明',
        ];

        $item = Item::factory()->create(array_merge($default, $attributes));

        $item->categories()->attach($this->category->id);

        return $item;
    }

    /**
     * 【テスト内容１】マイリスト: いいねした他人の商品のみが表示される
     *
     * @return void
     */
    public function test_mylist_shows_only_liked_items_by_others()
    {
        // 1. ログインユーザーを作成
        $loggedInUser = User::factory()->create();

        // 2. 他のユーザーが出品した商品 (いいねして表示される)
        $otherItemLiked = $this->createItem([
            'user_id' => User::factory()->create()->id,
            'name' => 'マイリストに表示される商品',
        ]);
        // いいねを登録
        Like::create([
            'user_id' => $loggedInUser->id,
            'item_id' => $otherItemLiked->id,
        ]);

        // 3. 他のユーザーが出品した商品 (いいねしていないので表示されない)
        $otherItemNotLiked = $this->createItem([
            'user_id' => User::factory()->create()->id,
            'name' => '表示されない他の商品',
        ]);

        // 4. 自分の出品した商品 (いいねしていても表示されない)
        $userItemLiked = $this->createItem([
            'user_id' => $loggedInUser->id,
            'name' => '自分の商品で表示されない',
        ]);
        // 自分の商品にいいねを登録
        Like::create([
            'user_id' => $loggedInUser->id,
            'item_id' => $userItemLiked->id,
        ]);


        // 実行: ログインし、マイリストタブにアクセス
        $response = $this->actingAs($loggedInUser)->get('/?tab=mylist');

        // 検証:
        // 1. いいねした他のユーザーの商品が表示されていることを確認
        $response->assertSeeText('マイリストに表示される商品');

        // 2. いいねしていない他のユーザーの商品が表示されていないことを確認
        $response->assertDontSeeText('表示されない他の商品');

        // 3. 自分の商品（いいね済みでも）が表示されていないことを確認
        $response->assertDontSeeText('自分の商品で表示されない');
    }

    /**
     * 【テスト内容２】マイリスト: 購入済み商品に "Sold" ラベルが表示される
     *
     * @return void
     */
    public function test_mylist_shows_sold_label_for_purchased_items()
    {
        // 1. ログインユーザーを作成
        $loggedInUser = User::factory()->create();
        $otherUser = User::factory()->create();

        // 2. 他のユーザーが出品した商品 (購入済み＋いいね済み)
        $itemSoldAndLiked = $this->createItem([
            'user_id' => $otherUser->id,
            'name' => 'Soldが表示される購入済み商品',
        ]);

        // 3. 購入済みとする (Purchaseレコードを作成)
        $buyer = User::factory()->create();
        Purchase::create([
            'item_id' => $itemSoldAndLiked->id,
            'user_id' => $buyer->id,
            'payment_method_id' => $this->paymentMethod->id,
        ]);

        // 4. いいねを登録
        Like::create([
            'user_id' => $loggedInUser->id,
            'item_id' => $itemSoldAndLiked->id,
        ]);

        // 実行: ログインし、マイリストタブにアクセス
        $response = $this->actingAs($loggedInUser)->get('/?tab=mylist');

        // 検証:
        // 1. 商品名が表示されていることを確認
        $response->assertSeeText('Soldが表示される購入済み商品');
        // 2. 「Sold」ラベルが表示されていることを確認
        $response->assertSee('Sold');
    }

    /**
     * 【テスト内容３】マイリスト: いいねした商品がない場合、何も表示されない
     *
     * @return void
     */
    public function test_mylist_shows_no_items_when_not_liked()
    {
        // 1. ログインユーザーを作成
        $loggedInUser = User::factory()->create();

        // 2. 他のユーザーが出品した商品 (いいねしない)
        $this->createItem([
            'user_id' => User::factory()->create()->id,
            'name' => 'いいねしていない商品',
        ]);

        // 実行: ログインし、マイリストタブにアクセス
        $response = $this->actingAs($loggedInUser)->get('/?tab=mylist');

        // 検証:
        // 1. 商品グリッド内に商品名が表示されていないことを確認
        $response->assertDontSeeText('いいねしていない商品');
        // 2. 「いいねした商品はありません」のメッセージが表示されていることを確認
        $response->assertSeeText('いいねした商品はありません');
    }
}
