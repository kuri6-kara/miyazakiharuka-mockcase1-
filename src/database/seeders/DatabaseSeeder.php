<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\PaymentMethodSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CategorySeeder::class);
        $this->call(ItemSeeder::class);
        $this->call(CategoryItemSeeder::class);
        $this->call(PaymentMethodSeeder::class);
    }
}
