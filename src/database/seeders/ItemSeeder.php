<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;

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

        $items = [
            [
                'user_id' => $user->id,
                'name' => '腕時計',
                'price' => 15000,
                'brand_name' => 'Rolex',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_image_path' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_1',
                'condition' => '良好',
                'category_id' => $categories->where('category', 'メンズ')->first()->id,
            ],
            [
                'user_id' => $user->id,
                'name' => 'HDD',
                'price' => 5000,
                'brand_name' => '東芝',
                'description' => '高速で信頼性の高いハードディスク',
                'item_image_path' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_2',
                'condition' => '目立った傷や汚れなし',
                'category_id' => $categories->where('category', 'メンズ')->first()->id,
            ],
            [
                'user_id' => $user->id,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'item_image_path' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_3',
                'condition' => 'やや傷や汚れあり',
                'category_id' => $categories->where('category', 'メンズ')->first()->id,
            ],
            [
                'user_id' => $user->id,
                'name' => '革靴',
                'price' => 4000,
                'brand_name' => 'なし',
                'description' => 'クラシックなデザインの革靴',
                'item_image_path' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_4',
                'condition' => '状態が悪い',
                'category_id' => $categories->where('category', 'メンズ')->first()->id,
            ],
            [
                'user_id' => $user->id,
                'name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => '高性能なノートパソコン',
                'description' => '良好',
                'item_image_path' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_5',
                'condition' => '良好',
                'category_id' => $categories->where('category', 'メンズ')->first()->id,
            ],
            [
                'user_id' => $user->id,
                'name' => 'マイク',
                'price' => 8000,
                'brand_name' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'item_image_path' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_6',
                'condition' => '目立った傷や汚れなし',
                'category_id' => $categories->where('category', 'メンズ')->first()->id,
            ],
            [
                'user_id' => $user->id,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => 'おしゃれなショルダーバッグ',
                'description' => 'やや傷や汚れあり',
                'item_image_path' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_7',
                'condition' => 'やや傷や汚れあり',
                'category_id' => $categories->where('category', 'メンズ')->first()->id,
            ],
            [
                'user_id' => $user->id,
                'name' => 'タンブラー',
                'price' => 500,
                'brand_name' => 'なし',
                'description' => '使いやすいタンブラー',
                'item_image_path' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_8',
                'condition' => '状態が悪い',
                'category_id' => $categories->where('category', 'メンズ')->first()->id,
            ],
            [
                'user_id' => $user->id,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbucks',
                'description' => '手動のコーヒーミル',
                'item_image_path' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_9',
                'condition' => '良好',
                'category_id' => $categories->where('category', 'メンズ')->first()->id,
            ],
            [
                'user_id' => $user->id,
                'name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => '便利なメイクアップセット',
                'description' => '目立った傷や汚れなし',
                'item_image_path' => 'https://coach-tech-matter-s3.ap-northeast-1.amazonaws.com/image_url_10',
                'condition' => '目立った傷や汚れなし',
                'category_id' => $categories->where('category', 'メンズ')->first()->id,
            ],
        ];

        DB::table('items')->insert($items);
    }
}
