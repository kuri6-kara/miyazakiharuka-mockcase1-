<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * いいね機能の機能テスト (ID: 8)
 * - ログインユーザーによるいいねの登録・解除および数の検証を行います。
 */
class LikeTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $item;

    /**
     * 各テストメソッドの前に実行される共通セットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のユーザーを作成
        $this->user = User::factory()->create();

        // テスト用の商品を作成
        $this->item = Item::factory()->create([
            'user_id' => User::factory()->create()->id, // 出品者は別のユーザー
            'name' => 'いいね対象商品',
            'price' => 1000,
        ]);
    }

    /**
     * 【テスト内容１】いいねを登録できることと、いいね合計値が増加すること
     *
     * @return void
     */
    public function test_case_1_like_can_be_registered_and_total_count_increases()
    {
        // 事前確認: いいね数が0であること
        $initialLikeCount = $this->item->likes()->count();
        $this->assertEquals(0, $initialLikeCount);

        // 実行: ログインユーザーとして、いいね登録のエンドポイントにPOSTリクエストを送信
        $response = $this->actingAs($this->user)->postJson(route('like.store', ['item_id' => $this->item->id]));

        // 1. レスポンスの検証
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'action' => 'attached',
            'like_count' => 1
        ]);

        // 2. 期待挙動の検証: データベースにレコードが登録されたこと
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);

        // 3. 期待挙動の検証: いいね合計値が増加すること (DBの値で代替検証)
        $newLikeCount = $this->item->fresh()->likes()->count();
        $this->assertEquals(1, $newLikeCount);
    }

    /**
     * 【テスト内容２】いいねアイコンを押下後、商品詳細ページでアイコンが変化した状態で表示されること
     *
     * @return void
     */
    public function test_case_2_like_icon_changes_color_on_detail_page_after_registration()
    {
        // 1. 事前準備: いいねを登録する
        Like::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);

        // 2. 実行: ログインユーザーとして商品詳細ページにアクセス
        $response = $this->actingAs($this->user)->get(route('item.show', ['item_id' => $this->item->id]));

        // 3. 検証: 押下された状態を示すHTML要素またはCSSクラスが存在すること
        $response->assertStatus(200);
        $response->assertSee('alt="いいね済みアイコン"', false);
    }

    /**
     * 【テスト内容３】いいねを解除できることと、いいね合計値が減少表示されること
     *
     * @return void
     */
    public function test_case_3_like_can_be_removed_and_total_count_decreases()
    {
        // 1. 事前準備: いいねを登録する (初期状態はいいね済みとする)
        Like::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);

        // 事前確認: いいね数が1であること
        $initialLikeCount = $this->item->likes()->count();
        $this->assertEquals(1, $initialLikeCount);

        // 実行: ログインユーザーとして、いいね解除のエンドポイントにPOSTリクエストを送信
        $response = $this->actingAs($this->user)->postJson(route('like.store', ['item_id' => $this->item->id]));

        // 1. レスポンスの検証
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'action' => 'detached',
            'like_count' => 0
        ]);

        // 2. 期待挙動の検証: データベースからレコードが削除されたこと
        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
        ]);

        // 3. 期待挙動の検証: いいね合計値が減少すること
        $newLikeCount = $this->item->fresh()->likes()->count();
        $this->assertEquals(0, $newLikeCount);
    }
}
