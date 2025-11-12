<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

/**
 * 商品一覧機能の機能テスト
 * 1. 全ての商品を取得できること
 * 2. 購入済み商品は "Sold" と表示されること
 * 3. 自分の出品した商品は表示されないこと (未ログイン時またはおすすめタブ時)
 */
class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 【テストケース１】全ての商品を取得できることをテスト
     *
     * @return void
     */
    public function test_all_items_can_be_retrieved()
    {
        // 準備: 複数の商品を作成
        $items = Item::factory()->count(3)->create();

        // 実行: ホームページにアクセス
        $response = $this->get('/');

        // 検証:
        // 1. ステータスコードが200であることを確認
        $response->assertStatus(200);

        // 2. 作成したすべての商品名がレスポンスに含まれていることを確認
        foreach ($items as $item) {
            $response->assertSeeText($item->name);
        }
    }

    /**
     * 【テストケース２】購入済み商品は "SOLD" と表示されることをテスト
     *
     * @return void
     */
    public function test_sold_label_is_displayed_for_purchased_items()
    {
        // 準備:
        // 1. ユーザーを2人作成 (出品者と購入者)
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        // 2. 商品A (購入済み) と商品B (未購入) を作成
        $itemA = Item::factory()->create(['user_id' => $seller->id]);
        $itemB = Item::factory()->create(['user_id' => $seller->id]);

        // 3. 商品Aを購入済みにする (Purchaseレコードを作成)
        Purchase::factory()->create([
            'item_id' => $itemA->id,
            'user_id' => $buyer->id,
        ]);

        // 実行: ホームページにアクセス
        $response = $this->get('/');

        // 検証:
        // 1. 商品Aに対して 'SOLD' のテキストが表示されていることを確認
        $response->assertSeeText('SOLD');

        // 2. 商品Bの名前が表示されていることを確認 (商品が一覧にあること)
        $response->assertSeeText($itemB->name);
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

        // 2. 他のユーザーが出品した商品 (表示されるべき)
        // 表示されることを確実に検証するため、商品名を固定
        $otherItem = Item::factory()->create([
            'user_id' => User::factory(), // ログインユーザーとは異なるユーザー
            'name' => '他人の出品した商品',
        ]);

        // 3. ログインユーザーが出品した商品 (表示されないべき)
        // 表示されないことを確実に検証するため、商品名を固定
        $userItem = Item::factory()->create([
            'user_id' => $loggedInUser->id,
            'name' => '自分の出品した商品',
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
