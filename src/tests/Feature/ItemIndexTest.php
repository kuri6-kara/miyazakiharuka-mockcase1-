<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category; // Categoryモデルのインポートを追加
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 商品一覧機能の機能テスト (ID: 4)
 * - 必須カラム 'condition' の文字列値をテストデータに含めるよう修正
 * - Category の多対多リレーションに対応するため attach を追加
 */
class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $category; // Categoryモデルのプロパティ

    protected function setUp(): void
    {
        parent::setUp();
        // 認証済みの有効なユーザーを作成
        $this->user = User::factory()->create();

        // テストに必要な Category データを作成 (多対多リレーションのテストに必須)
        // Itemの作成前にCategoryレコードが存在することを保証
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
        // conditionカラムが必須なので、デフォルト値を設定
        // user_idは上書きされなければUser::factory()によって生成される
        $default = [
            'condition' => '新品、未使用', // NOT NULLである 'condition' に文字列値を設定
            'name' => 'デフォルト商品',
            'price' => 100,
            'description' => '説明',
        ];

        // Item::factory()を使用してItemレコードを作成
        $item = Item::factory()->create(array_merge($default, $attributes));

        // Categoryの多対多リレーションをアタッチ
        $item->categories()->attach($this->category->id);

        return $item;
    }

    // /**
    //  * 【テストケース１】全ての商品を取得できる
    //  *
    //  * @return void
    //  */
    // public function test_all_items_can_be_retrieved()
    // {
    //     // 1. テスト用の商品を3つ作成（ヘルパー関数を使用）
    //     $item1 = $this->createItem(['name' => 'テスト商品A']);
    //     $item2 = $this->createItem(['name' => 'テスト商品B']);
    //     $item3 = $this->createItem(['name' => 'テスト商品C']);

    //     $items = [$item1, $item2, $item3];

    //     // 2. 商品一覧ページ（/）にアクセス
    //     $response = $this->get('/');

    //     // 3. レスポンスのHTTPステータスコードが200であることを確認
    //     $response->assertStatus(200);

    //     // 4. 作成した3つの商品すべてが表示されていることを確認
    //     foreach ($items as $item) {
    //         $response->assertSeeText($item->name);
    //     }
    // }


    // /**
    //  * 【テストケース２】購入済み商品は "Sold" と表示される
    //  *
    //  * @return void
    //  */
    // public function test_sold_label_is_displayed_for_purchased_items()
    // {
    //     // 1. テスト用の商品を2つ作成（ヘルパー関数を使用）
    //     $itemA = $this->createItem(['name' => '購入済み商品']);
    //     $itemB = $this->createItem(['name' => '未購入商品']);

    //     // 2. 商品Aを購入済みとする (Purchaseレコードを作成)
    //     $buyer = User::factory()->create();
    //     Purchase::factory()->create([
    //         'item_id' => $itemA->id,
    //         'user_id' => $buyer->id,
    //     ]);

    //     // 3. 商品一覧ページ（/）にアクセス
    //     $response = $this->get('/');

    //     // 4. 商品Aの近くに「Sold」というテキストが表示されていることを確認
    //     $response->assertSee('Sold');
    //     $response->assertSeeText($itemA->name);
    // }

    /**
     * 【テストケース３】自分の出品した商品はデフォルトで表示されないことをテスト (おすすめタブ)
     *
     * @return void
     */
    public function test_user_does_not_see_their_own_items_by_default()
    {
        // 準備:
        // 1. ログインユーザーを作成
        $loggedInUser = User::factory()->create();

        // 2. 他のユーザーが出品した商品 (表示されるべき)
        $otherUser = User::factory()->create();
        $otherItem = $this->createItem([
            'user_id' => $otherUser->id,
            'name' => '他人の出品した商品',
        ]);

        // 3. ログインユーザーが出品した商品 (表示されないべき)
        // ユーザーIDを指定して商品を作成
        $userItem = $this->createItem([
            'user_id' => $loggedInUser->id,
            'name' => '自分の出品した商品',
        ]);

        // 実行: ログインし、ホームページ (おすすめタブ) にアクセス
        $response = $this->actingAs($loggedInUser)->get('/?tab=recommend');

        // 検証:
        // 1. 他のユーザーの商品が表示されていることを確認
        $response->assertSeeText('他人の出品した商品');

        // 2. ログインユーザー自身の商品が表示されていないことを確認
        $response->assertDontSeeText('自分の出品した商品');
    }
}
