<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * マイリスト機能の機能テスト
 */
class MylistTest extends TestCase
{
    use RefreshDatabase;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        // Item作成前にCategoryレコードが存在することを保証
        $this->category = Category::create(['category' => 'テストカテゴリ']);
    }

    /**
     * ヘルパー関数: Itemを作成し、Categoryリレーションをアタッチ
     *
     * @param array $attributes
     * @return \App\Models\Item
     */
    private function createItem(array $attributes = [])
    {
        // データベースの NOT NULL である 'condition' に文字列値を設定
        $default = [
            'condition' => '新品、未使用',
            'name' => 'デフォルト商品',
            'price' => 100,
            'description' => '説明',
        ];

        // Item::factory()を使用してItemレコードを作成
        $item = Item::factory()->create(array_merge($default, $attributes));

        // Categoryの多対多リレーションをアタッチ
        // Itemが表示されるためには、Categoryが紐づいていることが必須
        $item->categories()->attach($this->category->id);

        return $item;
    }

    /**
     * 【テストケース１】マイリスト: いいねした他人の商品のみが表示される
     *
     * @return void
     */
    public function test_mylist_shows_only_liked_items_by_others()
    {
        // 1. ログインユーザーを作成
        $loggedInUser = User::factory()->create();

        // 2. 他のユーザーが出品した商品 (いいねして表示されるべき)
        $otherItemLiked = $this->createItem([
            'user_id' => User::factory()->create()->id,
            'name' => 'マイリストに表示される商品',
        ]);
        // いいねを登録
        Like::create([
            'user_id' => $loggedInUser->id,
            'item_id' => $otherItemLiked->id,
        ]);

        // 3. 他のユーザーが出品した商品 (いいねしていないので表示されないべき)
        $otherItemNotLiked = $this->createItem([
            'user_id' => User::factory()->create()->id,
            'name' => '表示されない他の商品',
        ]);

        // 4. 自分の出品した商品 (いいねしていても表示されないべき)
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

}
