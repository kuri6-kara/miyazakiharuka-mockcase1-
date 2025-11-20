<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PaymentMethod; // 追加: PaymentMethodモデルをインポート
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 商品購入機能の機能テスト (ID: 10)
 * - ログインユーザーによる商品の購入と、それに伴う状態の変化を検証します。
 */
class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private $buyer;
    private $seller;
    private $item;
    private $paymentMethod;

    /**
     * 各テストメソッドの前に実行される共通セットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 共通で利用する購入者（バイヤー）と出品者（セラー）を作成
        $this->buyer = User::factory()->create([
            'name' => 'Buyer User',
            'postcode' => '123-4567', // 配送先情報として使用するため追加
            'address' => '東京都港区赤坂',
            'building' => 'テストビル',
        ]);
        $this->seller = User::factory()->create(['name' => 'Seller User']);

        // 共通で利用する未購入の商品を作成
        $this->item = Item::factory()->create([
            'user_id' => $this->seller->id, // 出品者はセラーユーザー
            'name' => 'テスト販売商品',
            'price' => 5000,
            'is_sold' => false, // 未購入状態
        ]);

        // 共通で利用する支払い方法を作成（コントローラで参照されるため必須）
        $this->paymentMethod = PaymentMethod::create([
            'payment_method' => '銀行振込' // 決済画面遷移がない方法を選択
        ]);
    }

    // /**
    //  * 【テストケース１】「購入する」ボタンを押下すると購入が完了すること
    //  * Purchaseレコードが作成され、Itemのis_soldフラグがtrueになることを検証する。
    //  *
    //  * @return void
    //  */
    // public function test_case_1_purchase_is_completed_on_button_press()
    // {
    //     // 事前確認: purchasesテーブルにレコードがないこと
    //     $this->assertDatabaseCount('purchases', 0);
    //     // 事前確認: 商品が未購入状態であること
    //     $this->assertFalse($this->item->is_sold);

    //     // 実行: ログインユーザーとして購入処理のエンドポイントにPOSTリクエストを送信
    //     // ★コントローラがセッションの配送先情報とPaymentMethodIDを要求するため、ダミーデータを準備
    //     // ユーザーの初期情報がセッションにない場合は使われるため、ここではセッション操作は省略し、
    //     // 必須のpayment_method_idのみを送信。
    //     $response = $this->actingAs($this->buyer)->post(route('purchase.store', ['item_id' => $this->item->id]), [
    //         'payment_method_id' => $this->paymentMethod->id,
    //         // PurchaseRequestでバリデーションされている配送先情報（postcode, address）は
    //         // セッションかAuth::user()から取得されるため、Userファクトリで設定済み
    //     ]);

    //     // 1. レスポンスの検証: 購入完了後、商品一覧ページ（ルート /）にリダイレクトされること
    //     // コントローラ: return redirect()->route('item.index') に合わせる
    //     $response->assertRedirect(route('item.index'));

    //     // 2. 期待挙動の検証: purchasesテーブルにレコードが登録されたこと (購入が完了)
    //     $this->assertDatabaseHas('purchases', [
    //         'item_id' => $this->item->id,
    //         'user_id' => $this->buyer->id,
    //         'payment_method_id' => $this->paymentMethod->id,
    //     ]);

    //     // 3. 期待挙動の検証: Itemのis_soldフラグがtrueに更新されたこと
    //     $this->assertTrue($this->item->fresh()->is_sold);
    // }

    /**
     * 【テストケース２】購入した商品は商品詳細で「SOLD」と表示されること
     * is_soldフラグがtrueになった後、商品詳細ページで「SOLD OUT」ボタンが表示されることを検証する。
     *
     * @return void
     */
    public function test_case_2_purchased_item_shows_sold_out_on_detail_page()
    {
        // 事前準備: ユーザーに購入させる
        // Purchase::create と Item::update が実行され、is_soldがtrueになる
        $this->actingAs($this->buyer)->post(route('purchase.store', ['item_id' => $this->item->id]), [
            'payment_method_id' => $this->paymentMethod->id,
        ]);

        // 1. 実行: 商品詳細ページを開く
        $response = $this->get(route('item.show', ['item_id' => $this->item->id]));

        // 2. 期待挙動の検証: ページ上に「SOLD OUT」に関連するテキストが表示されていること
        // 実際の実装に合わせて「SOLD OUT」または「SOLD」の表示を確認
        $response->assertSee('SOLD'); // 画像のテスト内容「sold」と表示される に合わせる

        // 3. 期待挙動の検証: 「購入手続きへ」ボタンなどが表示されていないこと
        $response->assertDontSee('購入手続きへ');
        $response->assertDontSee('購入する');
    }

    // /**
    //  * 【テストケース３】購入した商品が「プロフィール（購入した商品一覧）」に追加されていること
    //  * プロフィールページの購入履歴一覧に商品名が表示されていることを検証する。
    //  *
    //  * @return void
    //  */
    // public function test_case_3_purchased_item_is_added_to_buyer_profile_list()
    // {
    //     // 事前準備: ユーザーに購入させる
    //     $this->actingAs($this->buyer)->post(route('purchase.store', ['item_id' => $this->item->id]), [
    //         'payment_method_id' => $this->paymentMethod->id,
    //     ]);

    //     // 1. 実行: 購入者（$this->buyer）としてプロフィールページ（購入履歴）を開く
    //     // ※ここではプロフィールページのルートを 'profile.purchases' と仮定します。
    //     $response = $this->actingAs($this->buyer)->get(route('user.mypage'));

    //     // 2. 期待挙動の検証: ページ上に購入した商品の名前が表示されていること
    //     $response->assertSee($this->item->name);

    //     // 3. 期待挙動の検証: 購入履歴ページで商品名が表示されていること
    //     $response->assertSee('テスト販売商品');
    // }
}
