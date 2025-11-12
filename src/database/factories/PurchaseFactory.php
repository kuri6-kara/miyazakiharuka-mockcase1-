<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use App\Models\PaymentMethod; // PaymentMethodモデルをインポート
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * 対応するモデル
     *
     * @var string
     */
    protected $model = Purchase::class;

    /**
     * モデルの定義を生成
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // 購入に必要なすべての必須フィールドを埋める
        return [
            // 外部キーの自動解決（関連するファクトリが呼ばれる）
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            // PaymentMethodの外部キーとしてPaymentMethodFactoryを呼び出すのがベスト
            'payment_method_id' => PaymentMethod::factory(),

            // 住所関連の必須フィールド（fakerを使用してダミーデータを生成）
            'postcode' => $this->faker->postcode(),
            'address' => $this->faker->city() . $this->faker->streetAddress(),
            'building' => $this->faker->secondaryAddress(), // buildingはNULL許容の可能性もあるが、埋めておく
        ];
    }
}
