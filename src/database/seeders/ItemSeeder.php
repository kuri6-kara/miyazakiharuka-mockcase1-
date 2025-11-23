<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Storage::disk('public')->deleteDirectory('item_images');

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'profile_updated' => true,
        ]);
        $user2 = User::create([
            'name' => 'Test2 User',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
            'profile_updated' => true,
        ]);

        $categories = Category::all();

        $items_data = [
            [
                'user_id' => $user->id,
                'name' => '腕時計',
                'price' => 15000,
                'brand_name' => 'Rolex',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_image_path' => 'Armani+Mens+Clock.jpg',
                'condition' => '良好',
            ],
            [
                'user_id' => $user2->id,
                'name' => 'HDD',
                'price' => 5000,
                'brand_name' => '東芝',
                'description' => '高速で信頼性の高いハードディスク',
                'item_image_path' => 'HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'user_id' => $user->id,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'item_image_path' => 'iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'user_id' => $user2->id,
                'name' => '革靴',
                'price' => 4000,
                'brand_name' => 'なし',
                'description' => 'クラシックなデザインの革靴',
                'item_image_path' => 'Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'user_id' => $user->id,
                'name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => '高性能なノートパソコン',
                'description' => '良好',
                'item_image_path' => 'Living+Room+Laptop.jpg',
                'condition' => '良好',
            ],
            [
                'user_id' => $user2->id,
                'name' => 'マイク',
                'price' => 8000,
                'brand_name' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'item_image_path' => 'Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'user_id' => $user->id,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => 'おしゃれなショルダーバッグ',
                'description' => 'やや傷や汚れあり',
                'item_image_path' => 'Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'user_id' => $user2->id,
                'name' => 'タンブラー',
                'price' => 500,
                'brand_name' => 'なし',
                'description' => '使いやすいタンブラー',
                'item_image_path' => 'Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'user_id' => $user->id,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbucks',
                'description' => '手動のコーヒーミル',
                'item_image_path' => 'Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
            ],
            [
                'user_id' => $user2->id,
                'name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => '便利なメイクアップセット',
                'description' => '目立った傷や汚れなし',
                'item_image_path' => 'makeup_set.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        $dummy_image_data = base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');


        foreach ($items_data as $data) {
            $filename = $data['item_image_path'];
            $path = 'item_images/' . $filename;

            Storage::disk('public')->put($path, $dummy_image_data);

            $item_data = [
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'price' => $data['price'],
                'brand_name' => $data['brand_name'],
                'description' => $data['description'],
                'item_image_path' => $path,
                'condition' => $data['condition'],
            ];

            DB::table('items')->insert($item_data);
        }
    }
}
