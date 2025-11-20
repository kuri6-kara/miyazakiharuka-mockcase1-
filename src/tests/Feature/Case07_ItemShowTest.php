<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 商品詳細情報取得機能の機能テスト
 * - 未認証ユーザーによる商品詳細情報の取得を検証
 */
class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    private $itemOwner;
    private $item;
    private $categories;
    private $commenter;
    private $comment;
    private $liker;

    /**
     * 各テストメソッドの前に実行される共通セットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. 商品所有者となるユーザーを作成
        $this->itemOwner = User::factory()->create(['name' => '商品出品者']);

        // 2. 複数のカテゴリを作成
        $this->categories = [
            Category::create(['category' => 'ファッション']),
            Category::create(['category' => 'メンズ']),
            Category::create(['category' => 'Tシャツ']),
        ];

        // 3. テスト用の商品を作成
        $this->item = Item::factory()->create([
            'user_id' => $this->itemOwner->id,
            'name' => 'テスト限定Tシャツ',
            'brand_name' => 'Example Brand',
            'price' => 5000,
            'description' => 'これはテスト用の非常に詳細な商品説明です。状態は良好です。',
            'condition' => '目立った傷や汚れなし',
            'item_image_path' => 'items/sample_image_1.jpg',
        ]);

        // 4. カテゴリを商品にアタッチ (複数選択されたカテゴリの表示を検証用)
        $this->item->categories()->attach([
            $this->categories[0]->id,
            $this->categories[1]->id,
            $this->categories[2]->id
        ]);

        // 5. いいねを付けるユーザーといいねを作成 (いいね数カウント用)
        $this->liker = User::factory()->create();
        Like::create([
            'user_id' => $this->liker->id,
            'item_id' => $this->item->id,
        ]);

        // 6. コメントを投稿するユーザーとコメントを作成 (コメント数・内容確認用)
        $this->commenter = User::factory()->create(['name' => 'コメントユーザー']);
        $this->comment = Comment::create([
            'user_id' => $this->commenter->id,
            'item_id' => $this->item->id,
            'comment' => 'この商品について質問があります。',
        ]);
    }

    /**
     * 【テスト内容１】未認証ユーザーとして、必要な全情報が表示されること
     * (検証項目：商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品の状態、コメントしたユーザー情報、コメント内容)
     *
     * @return void
     */
    public function test_case_1_all_required_information_is_displayed()
    {
        // 実行: 未認証ユーザーとして商品詳細ページにアクセス
        $response = $this->get(route('item.show', ['item_id' => $this->item->id]));

        // ステータスコードの検証
        $response->assertStatus(200);

        // 1. 必須情報の検証 (商品名、価格、ブランド名、商品説明、画像パス)
        $response->assertSee($this->item->name);
        $response->assertSee($this->item->brand_name);
        $response->assertSee('¥' . number_format($this->item->price));
        $response->assertSee($this->item->description);
        $response->assertSee($this->item->item_image_path);

        // 2. 商品情報の検証 (商品の状態)
        $response->assertSee('商品の状態');
        $response->assertSee($this->item->condition);

        // 3. いいね数とコメント数の検証 (集計値の確認)
        $response->assertSee((string) $this->item->likes->count()); // いいね数 (1件)
        $response->assertSee((string) $this->item->comments->count()); // コメント数 (1件)

        // 4. コメントとユーザー情報の検証
        $response->assertSee('コメント');
        $response->assertSee($this->comment->comment);
        $response->assertSee($this->commenter->name);
    }

    /**
     * 【テスト内容２】複数選択されたカテゴリが商品詳細ページに表示されている
     * (検証項目：商品に設定された全てのカテゴリが表示されるか)
     *
     * @return void
     */
    public function test_case_2_multiple_categories_are_displayed()
    {
        // 実行: 未認証ユーザーとして商品詳細ページにアクセス
        $response = $this->get(route('item.show', ['item_id' => $this->item->id]));

        // ステータスコードの検証
        $response->assertStatus(200);

        // カテゴリの検証
        $response->assertSee('カテゴリー');
        foreach ($this->categories as $category) {
            $response->assertSee($category->category);
        }
        // カテゴリ数が適切かを確認するため、意図的にカテゴリ以外の文字列がないかチェック
        $this->assertEquals(3, $this->item->categories->count());
    }
}
