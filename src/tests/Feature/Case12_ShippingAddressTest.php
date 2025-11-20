<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\PaymentMethod;

/**
 * 配送先変更機能の機能テスト
 */
class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    protected $buyer;
    protected $item;
    protected $paymentMethod;

    // 変更後の新しい配送先情報
    protected $newAddress = [
        'postcode' => '999-8888',
        'address' => '大阪府大阪市北区梅田',
        'building' => '大阪ビル 301号室',
    ];

    /**
     * テスト実行前のセットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. ユーザーの作成 (初期住所を持つ)
        $this->buyer = User::factory()->create([
            'postcode' => '123-4567',
            'address' => '東京都千代田区',
            'building' => 'コーポ101'
        ]);

        // 2. 商品の作成
        $this->item = Item::factory()->create([
            'name' => 'テスト販売商品',
            'price' => 1000,
            'is_sold' => false,
        ]);

        // 3. 支払い方法の作成 (購入時に必須)
        $this->paymentMethod = PaymentMethod::factory()->create(['payment_method' => 'カード払い']);
    }

    /**
     * 【テスト内容１】配送先変更画面で登録した住所が画面に反映されている
     * 期待挙動: 登録した配送先情報が購入確認画面に正しく反映される
     * @test
     */
    public function case_12_1_new_shipping_address_is_reflected_on_purchase_screen()
    {
        // 1. 実行: ログインユーザーとして、新しい配送先情報で更新処理を実行
        $response = $this->actingAs($this->buyer)->post(route('purchase.update', ['item_id' => $this->item->id]), [
            'postcode' => $this->newAddress['postcode'],
            'address' => $this->newAddress['address'],
            'building' => $this->newAddress['building'],
            'payment_method_id' => $this->paymentMethod->id,
        ]);

        // 2. 期待挙動の検証: 購入確認画面にリダイレクトされたこと
        $response->assertRedirect(route('purchase.create', ['item_id' => $this->item->id]));

        // 3. 期待挙動の検証: リダイレクト後の購入確認画面に、変更後の配送先が反映されていること
        // リダイレクト先の画面を取得
        $confirmation_response = $this->actingAs($this->buyer)->get(route('purchase.create', ['item_id' => $this->item->id]));

        // 期待挙動: 新しい郵便番号、住所、建物名が表示されていることを確認
        $confirmation_response->assertSee('〒 ' . $this->newAddress['postcode']);
        $confirmation_response->assertSee($this->newAddress['address']);
        $confirmation_response->assertSee('(' . $this->newAddress['building'] . ')');

        // 変更前の住所が表示されていないことを確認
        $confirmation_response->assertDontSee($this->buyer->address);
    }

    /**
     * 【テスト内容２】購入した際、配送先が紐づいて登録される
     * 期待挙動: Purchaseレコードに正しい配送先が紐づいて登録される
     * @test
     */
    public function case_12_2_purchase_is_stored_with_correct_shipping_address()
    {
        // 1. 準備: ログインユーザーとして、新しい配送先情報で更新処理を実行し、セッションに情報をセット
        $this->actingAs($this->buyer)->post(route('purchase.update', ['item_id' => $this->item->id]), [
            'postcode' => $this->newAddress['postcode'],
            'address' => $this->newAddress['address'],
            'building' => $this->newAddress['building'],
            'payment_method_id' => $this->paymentMethod->id,
        ]);

        // 2. 実行: 購入処理を実行
        $response = $this->actingAs($this->buyer)->post(route('purchase.store', ['item_id' => $this->item->id]), [
            'payment_method_id' => $this->paymentMethod->id,
        ]);

        // 3. 期待挙動の検証: 正常にリダイレクトされたこと（購入完了）
        $response->assertRedirect();

        // 4. 期待挙動の検証: PurchaseレコードがDBに作成され、新しい配送先情報が紐づいていること
        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->buyer->id,
            'item_id' => $this->item->id,
            'postcode' => $this->newAddress['postcode'],
            'address' => $this->newAddress['address'],
            'building' => $this->newAddress['building'],
        ]);
    }
}
