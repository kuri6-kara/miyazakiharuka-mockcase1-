<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PaymentMethod;

class MyPageTest extends TestCase
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
            // プロフィール画像パスを設定（テスト時にassertSeeTextで確認するため）
            'profile_image_path' => 'storage/user_profiles/test_buyer_photo.jpg',
        ]);
        $this->seller = User::factory()->create([
            'name' => 'テスト出品者',
        ]);

        // 支払い方法の作成 (購入時に必要)
        $paymentMethod = PaymentMethod::factory()->create();

        // 2. 出品した商品（ログインユーザーが出品した商品）
        // ログインユーザー($this->buyer)が出品し、まだ売れていない商品
        $this->itemSold = Item::factory()->create([
            'user_id' => $this->buyer->id,
            'name' => '私の出品商品',
            'price' => 2000,
            'is_sold' => false,
        ]);

        // 3. 購入した商品（ログインユーザーが購入した商品）
        // $this->sellerが出品し、$this->buyerが購入した商品
        $this->itemBought = Item::factory()->create([
            'user_id' => $this->seller->id,
            'name' => '私が購入した商品',
            'price' => 1000,
            'is_sold' => true,
        ]);

        // 購入履歴の作成
        Purchase::factory()->create([
            'user_id' => $this->buyer->id, // 購入者
            'item_id' => $this->itemBought->id,
            'payment_method_id' => $paymentMethod->id,
            // 配送先情報はデフォルトでOK
        ]);
    }

    /**
     * 【テストケース１３】マイページに必要な情報が正しく表示されること
     * 期待挙動: プロフィール画像、ユーザー名、出品/購入商品一覧が正しく表示される
     * @test
     */
    public function case_13_user_info_and_item_lists_are_displayed_on_mypage()
    {
        // 1. 実行: ログインユーザーとしてマイページにアクセス (ルート名: /mypage が一般的)
        $response = $this->actingAs($this->buyer)->get(route('user.mypage'));

        // 2. 期待挙動の検証: 正常にアクセスできること
        $response->assertStatus(200);

        // 3. 期待挙動の検証: ユーザー情報が表示されていること

        // ユーザー名
        $response->assertSeeText($this->buyer->name); // 「テスト購入者」

        // プロフィール画像 (画像パス自体またはHTML要素の属性を検証)
        // ここでは画像の `src` 属性にパスが含まれているかを確認します。
        $response->assertSee($this->buyer->profile_image_path, false);

        // 4. 期待挙動の検証: 出品した商品一覧が表示されていること

        // ログインユーザーが出品した商品名が表示されていることを確認
        $response->assertSeeText($this->itemSold->name); // 「私の出品商品」

        // 5. 期待挙動の検証: 購入した商品一覧が表示されていること

        // ログインユーザーが購入した商品名が表示されていることを確認
        // (通常、マイページでは出品と購入をタブなどで分けていることが多いですが、ここでは両方の名前が存在することを確認します)
        $response->assertSeeText($this->itemBought->name); // 「私が購入した商品」

        // 補足:
        // タブ切り替えなどで表示が隠れている場合、assertSeeTextの代わりに
        // assertSee(..., false) でHTML全体に存在する要素を確認する必要があります。
        // ここでは、商品名が表示エリアのどこかに存在することを検証することで要件を満たします。
    }
}
