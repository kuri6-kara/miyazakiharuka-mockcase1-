<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
// use Database\Factories\ItemFactory; <--- この行を削除/コメントアウトして、Laravelの自動検出に再度頼ります。
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 商品一覧機能の機能テスト (ID: 4)
 * ファクトリが見つからないエラーへの対応として、明示的なインポートを削除し、
 * Laravelのデフォルトのファクトリ解決に頼ります。
 */
class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    // 共通のテストユーザー
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        // テスト前に認証済みの有効なユーザーを作成
        // User::factory() も動作しない場合は、UserモデルにもHasFactoryトレイトがない可能性があります。
        $this->user = User::factory()->create();
    }

    /**
     * 【テストケース１】全ての商品を取得できる
     * テスト手順: 商品ページを開く
     * 期待挙動: すべての商品が表示される
     *
     * @return void
     */
    public function test_all_items_can_be_retrieved()
    {
        // 1. テスト用の商品を3つ作成
        $items = Item::factory()->count(3)->create();

        // 2. 商品一覧ページ（/）にアクセス（非認証状態でもアクセス可能と想定）
        $response = $this->get('/');

        // 3. レスポンスのHTTPステータスコードが200であることを確認
        $response->assertStatus(200);

        // 4. 作成した3つの商品すべてが表示されていることを確認
        foreach ($items as $item) {
            $response->assertSeeText($item->name);
        }
    }


    /**
     * 【テストケース２】購入済み商品は "Sold" と表示される
     * テスト手順: 1. 商品ページを開く 2. 購入済み以外の商品を表示する
     * 期待挙動: 購入済み商品に「Sold」のラベルが表示される
     *
     * @return void
     */
    public function test_sold_label_is_displayed_for_purchased_items()
    {
        // 1. テスト用の商品を2つ作成
        $itemA = Item::factory()->create(['name' => 'テスト商品A']);
        $itemB = Item::factory()->create(['name' => 'テスト商品B']);

        // 2. 商品Aを購入済みとする (Purchaseレコードを作成)
        // 別のユーザーを作成して購入者とする
        $buyer = User::factory()->create();
        Purchase::factory()->create([
            'item_id' => $itemA->id,
            'user_id' => $buyer->id,
        ]);

        // 3. 商品一覧ページ（/）にアクセス
        $response = $this->get('/');

        // 4. 商品Aの近くに「Sold」というテキストが表示されていることを確認
        $response->assertSee('Sold');
        $response->assertSeeText($itemA->name);
    }

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

        // 2. 他のユーザーが出品した商品 (表示されるべき) (Item::create()を使用し、必須IDを設定)
        $otherUser = User::factory()->create();
        $otherItem = Item::create([
            'user_id' => $otherUser->id,
            'name' => '他人の出品した商品',
            'price' => 500,
            'description' => '説明',
            'category_id' => 1,
            'condition_id' => 1 // 必須カラムを追加
        ]);

        // 3. ログインユーザーが出品した商品 (表示されないべき) (Item::create()を使用し、必須IDを設定)
        $userItem = Item::create([
            'user_id' => $loggedInUser->id,
            'name' => '自分の出品した商品',
            'price' => 1000,
            'description' => '説明',
            'category_id' => 1,
            'condition_id' => 1 // 必須カラムを追加
        ]);

        // 実行: ログインし、ホームページ (おすすめタブ) にアクセス
        $response = $this->actingAs($loggedInUser)->get('/');

        // 検証:
        // 1. 他のユーザーの商品が表示されていることを確認
        $response->assertSeeText('他人の出品した商品');

        // 2. ログインユーザー自身の商品が表示されていないことを確認
        $response->assertDontSeeText('自分の出品した商品');
    }
}
