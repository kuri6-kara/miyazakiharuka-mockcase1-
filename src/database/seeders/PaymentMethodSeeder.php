<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $methods = [
            ['payment_method' => 'カード払い'],
            ['payment_method' => 'コンビニ払い'],
        ];

        PaymentMethod::truncate();

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
