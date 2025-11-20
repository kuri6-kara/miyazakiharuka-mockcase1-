<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\PaymentMethod;

/**
 * 支払い方法選択機能の機能テスト
 */
class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected $buyer;
    protected $item;
    protected $paymentMethodDefault;
    protected $paymentMethodChanged;

    /**
     * テスト実行前のセットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. ユーザーの作成 (購入者)
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

        // 3. 支払い方法の作成（2種類）
        $this->paymentMethodDefault = PaymentMethod::factory()->create(['payment_method' => 'カード払い']);
        $this->paymentMethodChanged = PaymentMethod::factory()->create(['payment_method' => '銀行振込']); // 変更後の支払い方法
    }

    /**
     * 【テスト内容】支払い方法が変更され、決済画面に反映されること
     * 期待挙動: 選択した支払い方法が購入確認画面に反映される
     * * @test
     */
    public function case_11_payment_method_is_changed_and_reflected()
    {
        // 1. 実行: ログインユーザーとして、支払い方法変更/配送先更新処理を実行
        // 購入確認画面（purchase.create）でデフォルトの支払い方法が使われていることを前提とする

        // POSTリクエストで支払い方法を $this->paymentMethodChanged (銀行振込) に変更
        $response = $this->actingAs($this->buyer)->post(route('purchase.update', ['item_id' => $this->item->id]), [
            'payment_method_id' => $this->paymentMethodChanged->id,
            'postcode' => $this->buyer->postcode,
            'address' => $this->buyer->address,
            'building' => $this->buyer->building,
        ]);

        // 2. 期待挙動の検証: 購入確認画面にリダイレクトされたこと
        $response->assertRedirect(route('purchase.create', ['item_id' => $this->item->id]));

        // 3. 期待挙動の検証: リダイレクト後の購入確認画面に、変更後の支払い方法が反映されていること
        // リダイレクト先の画面を取得
        $confirmation_response = $this->actingAs($this->buyer)->get(route('purchase.create', ['item_id' => $this->item->id]));

        // 期待挙動の検証: ページ上に、変更後の支払い方法の名前が表示されていること
        $confirmation_response->assertSee($this->paymentMethodChanged->payment_method);

        // 変更前の支払い方法が表示されていないことを確認
        $confirmation_response->assertDontSee($this->paymentMethodDefault->payment_method);
    }
}
