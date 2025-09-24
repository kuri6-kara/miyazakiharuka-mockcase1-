<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'profile_updated' => true,
            ]);
        }

        $categories = Category::all();

        $items_data = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand_name' => 'Rolex',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_image_path' => 'Armani+Mens+Clock.jpg',
                'condition' => '良好',
                'category_name' => 'メンズ'
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand_name' => '東芝',
                'description' => '高速で信頼性の高いハードディスク',
                'item_image_path' => 'HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
                'category_name' => '家電・スマホ・カメラ'
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'item_image_path' => 'iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
                'category_name' => 'その他'
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand_name' => 'なし',
                'description' => 'クラシックなデザインの革靴',
                'item_image_path' => 'Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
                'category_name' => 'メンズ'
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => '高性能なノートパソコン',
                'description' => '良好',
                'item_image_path' => 'Living+Room+Laptop.jpg',
                'condition' => '良好',
                'category_name' => '家電・スマホ・カメラ'
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand_name' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'item_image_path' => 'Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
                'category_name' => '家電・スマホ・カメラ'
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => 'おしゃれなショルダーバッグ',
                'description' => 'やや傷や汚れあり',
                'item_image_path' => 'Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
                'category_name' => 'レディース'
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand_name' => 'なし',
                'description' => '使いやすいタンブラー',
                'item_image_path' => 'Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
                'category_name' => 'その他'
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbucks',
                'description' => '手動のコーヒーミル',
                'item_image_path' => 'Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
                'category_name' => 'その他'
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => '便利なメイクアップセット',
                'description' => '目立った傷や汚れなし',
                'item_image_path' => '外出メイクアップセット.jpg',
                'condition' => '目立った傷や汚れなし',
                'category_name' => 'レディース'
            ],
        ];

        foreach ($items_data as $data) {
            $contents = file_get_contents($data['item_image_path']);
            $filename = basename($data['item_image_path']);
            $path = 'item_images/' . $filename;

            Storage::disk('public')->put($path, $contents);

            $item_data = [
                'user_id' => $user->id,
                'name' => $data['name'],
                'price' => $data['price'],
                'brand_name' => $data['brand_name'],
                'description' => $data['description'],
                'item_image_path' => $path,
                'condition' => $data['condition'],
                'category_id' => $categories->where('category', $data['category_name'])->first()->id,
            ];

            DB::table('items')->insert($item_data);
        }
    }
}
