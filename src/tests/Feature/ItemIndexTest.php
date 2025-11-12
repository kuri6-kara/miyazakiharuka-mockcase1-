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
}
