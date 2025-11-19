<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 商品検索機能の機能テスト (ID: 6)
 */
class ItemSearchTest extends TestCase
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
     * ヘルパー関数: Itemを作成し、Categoryリレーションをアタッチ
     *
     * @param array $attributes
     * @return \App\Models\Item
     */
    private function createItem(array $attributes = [])
    {
        // is_sold: false を設定して、商品が一覧に表示されるようにする
        $default = [
            'condition' => '新品、未使用',
            'name' => 'デフォルト商品',
            'price' => 100,
            'description' => '説明',
            // 商品の所有者はログインユーザーで問題ない
            'user_id' => $this->loggedInUser->id,
            'item_image_path' => 'public/items/test_image.jpg',
            'is_sold' => false,
        ];

        $item = Item::factory()->create(array_merge($default, $attributes));
        $item->categories()->attach($this->category->id);

        return $item;
    }

    /**
     * 【テストケース１】「商品名」で部分一致検索ができる
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
        // 未認証ユーザーはデフォルトで「おすすめ」タブ（全商品カタログ）になるため、tabパラメータは不要
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
     * 【テストケース２】検索状態がマイリストでも保持されている (検索クエリの保持)
     *
     * @return void
     */
    public function test_search_state_is_maintained_when_switching_to_mylist()
    {
        // 1. 検索キーワードを設定
        $searchKeyword = '限定品';

        // 2. 検索実行: ログインし、検索キーワード付きでホームページにアクセス
        // 全商品カタログでの検索をシミュレートするため tab=recommend を指定
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
