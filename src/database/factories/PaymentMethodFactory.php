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
     * @var string
     */
    protected $model = PaymentMethod::class;

    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'payment_method' => $this->faker->randomElement([
                'クレジットカード',
                '銀行振込',
                'コンビニ決済'
            ]),
        ];
    }
}
