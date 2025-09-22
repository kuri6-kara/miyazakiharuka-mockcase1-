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
                'img_url' => 'Armani+Mens+Clock.jpg',
                'condition' => '良好',
                'category_name' => 'メンズ'
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand_name' => '東芝',
                'description' => '高速で信頼性の高いハードディスク',
                'img_url' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_2',
                'condition' => '目立った傷や汚れなし',
                'category_name' => '家電・スマホ・カメラ'
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'img_url' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_3',
                'condition' => 'やや傷や汚れあり',
                'category_name' => 'その他'
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand_name' => 'なし',
                'description' => 'クラシックなデザインの革靴',
                'img_url' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_4',
                'condition' => '状態が悪い',
                'category_name' => 'メンズ'
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => '高性能なノートパソコン',
                'description' => '良好',
                'img_url' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_5',
                'condition' => '良好',
                'category_name' => '家電・スマホ・カメラ'
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand_name' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'img_url' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_6',
                'condition' => '目立った傷や汚れなし',
                'category_name' => '家電・スマホ・カメラ'
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => 'おしゃれなショルダーバッグ',
                'description' => 'やや傷や汚れあり',
                'img_url' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_7',
                'condition' => 'やや傷や汚れあり',
                'category_name' => 'レディース'
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand_name' => 'なし',
                'description' => '使いやすいタンブラー',
                'img_url' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_8',
                'condition' => '状態が悪い',
                'category_name' => 'その他'
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbucks',
                'description' => '手動のコーヒーミル',
                'img_url' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_9',
                'condition' => '良好',
                'category_name' => 'その他'
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => '便利なメイクアップセット',
                'description' => '目立った傷や汚れなし',
                'img_url' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_10',
                'condition' => '目立った傷や汚れなし',
                'category_name' => 'レディース'
            ],
        ];

        foreach ($items_data as $data) {
            $contents = file_get_contents($data['img_url']);
            $filename = basename($data['img_url']);
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
