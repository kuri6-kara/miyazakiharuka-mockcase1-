<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 商品検索機能の機能テスト
 */
class Case06_ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    private $category;
    private $loggedInUser;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のユーザーとカテゴリを作成
        $this->loggedInUser = User::factory()->create();
        $this->category = Category::create(['category' => 'テストカテゴリ']);
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
            'user_id' => $this->loggedInUser->id,
            'item_image_path' => 'public/items/test_image.jpg',
            'is_sold' => false,
        ];

        $item = Item::factory()->create(array_merge($default, $attributes));
        $item->categories()->attach($this->category->id);

        return $item;
    }

    /**
     * 【テスト内容１】「商品名」で部分一致検索ができる
     *
     * @return void
     */
    public function test_can_search_items_by_partial_name_match()
    {
        // 1. 検索対象となる商品データを用意
        $hitItem1 = $this->createItem(['name' => '赤色のTシャツ']);
        $hitItem2 = $this->createItem(['name' => 'Tシャツ（青）']);
        $missItem = $this->createItem(['name' => 'ジーンズ']);

        $searchKeyword = 'Tシャツ';

        // 2. 実行: 未認証ユーザーとして、検索キーワード付きでアクセス
        // 未認証ユーザーはデフォルトで「おすすめ」タブ（全商品カタログ）になる
        $response = $this->get('/', [
            'keyword' => $searchKeyword,
            'tab' => 'recommend'
        ]);

        // 3. 検証:
        $response->assertStatus(200);
        $response->assertSeeText($hitItem1->name);
        $response->assertSeeText($hitItem2->name);
        $response->assertDontSeeText($missItem->name);
    }

    /**
     * 【テスト内容２】検索状態がマイリストでも保持されている (検索クエリの保持)
     *
     * @return void
     */
    public function test_search_state_is_maintained_when_switching_to_mylist()
    {
        // 1. 検索キーワードを設定
        $searchKeyword = '限定品';

        // 2. 検索実行: ログインし、検索キーワード付きでホームページにアクセス
        $this->actingAs($this->loggedInUser)->get('/', [
            'keyword' => $searchKeyword,
            'tab' => 'recommend',
        ]);

        // 3. マイリストタブへの遷移を実行 (クエリパラメータを保持したままタブ切り替え)
        $response = $this->actingAs($this->loggedInUser)->get('/?keyword=' . $searchKeyword . '&tab=mylist');

        // 4. 検証:
        $response->assertStatus(200);

        // - 検索キーワードが入力フィールドに保持されていることを確認
        $response->assertSeeInOrder([
            'name="keyword"',
            'value="' . $searchKeyword . '"'
        ], false);
    }
}
