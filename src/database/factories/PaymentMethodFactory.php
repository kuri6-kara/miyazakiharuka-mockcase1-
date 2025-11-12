<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * 対応するモデル
     *
     * @var string
     */
    protected $model = PaymentMethod::class;

    /**
     * モデルの定義を生成
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // payment_method カラムにダミーの支払い方法名を設定
        return [
            'payment_method' => $this->faker->randomElement([
                'クレジットカード',
                '銀行振込',
                'コンビニ決済'
            ]),
        ];
    }
}
