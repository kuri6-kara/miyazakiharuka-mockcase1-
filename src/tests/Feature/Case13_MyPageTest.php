<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PaymentMethod;

/**
 * ユーザー情報取得機能の機能テスト
 */
class Case13_MyPageTest extends TestCase
{
    use RefreshDatabase;

    protected $buyer;
    protected $seller;
    protected $itemSold;
    protected $itemBought;

    /**
     * テスト実行前のセットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. ユーザーの作成
        $this->buyer = User::factory()->create([
            'name' => 'テスト購入者',
            'profile_image_path' => 'storage/user_profiles/test_buyer_photo.jpg',
        ]);
        $this->seller = User::factory()->create([
            'name' => 'テスト出品者',
        ]);

        // 支払い方法の作成 (購入時に必要)
        $paymentMethod = PaymentMethod::factory()->create();

        // 2. 出品した商品（ログインユーザーが出品した商品）
        // ログインユーザーが出品し、まだ売れていない商品
        $this->itemSold = Item::factory()->create([
            'user_id' => $this->buyer->id,
            'name' => '私の出品商品',
            'price' => 2000,
            'is_sold' => false,
        ]);

        // 3. 購入した商品（ログインユーザーが購入した商品）
        $this->itemBought = Item::factory()->create([
            'user_id' => $this->seller->id,
            'name' => '私が購入した商品',
            'price' => 1000,
            'is_sold' => true,
        ]);

        // 購入履歴の作成
        Purchase::factory()->create([
            'user_id' => $this->buyer->id,
            'item_id' => $this->itemBought->id,
            'payment_method_id' => $paymentMethod->id,
        ]);
    }

    /**
     * 【テスト内容】マイページに必要な情報が正しく表示されること
     * 期待挙動: プロフィール画像、ユーザー名、出品/購入商品一覧が正しく表示される
     * @test
     */
    public function case_13_user_info_and_item_lists_are_displayed_on_mypage()
    {
        // 1. 実行: ログインユーザーとしてマイページにアクセス
        $response = $this->actingAs($this->buyer)->get(route('user.mypage'));

        // 2. 期待挙動の検証: 正常にアクセスできること
        $response->assertStatus(200);

        // 3. 期待挙動の検証: ユーザー情報が表示されていること

        // ユーザー名
        $response->assertSeeText($this->buyer->name);

        // プロフィール画像 (画像パス自体またはHTML要素の属性を検証)
        $response->assertSee($this->buyer->profile_image_path, false);

        // 4. 期待挙動の検証: 出品した商品一覧が表示されていること

        // ログインユーザーが出品した商品名が表示されていることを確認
        $response->assertSeeText($this->itemSold->name);

        // 5. 期待挙動の検証: 購入した商品一覧が表示されていること

        // ログインユーザーが購入した商品名が表示されていることを確認
        $response->assertSeeText($this->itemBought->name);
    }
}
