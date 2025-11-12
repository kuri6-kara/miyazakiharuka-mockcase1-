<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * 対応するモデル
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * モデルの定義を生成
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Item.php の $fillable に合わせて定義
        return [
            // UserFactoryが存在し、User::factory()が動作していることを前提に、
            // 外部キーとして存在するUser IDを割り当てる
            'user_id' => User::factory(),
            'name' => $this->faker->realText(20),
            'description' => $this->faker->realText(100),
            'price' => $this->faker->numberBetween(300, 100000), // 300円から10万円
            'condition' => $this->faker->randomElement(['新品、未使用', '未使用に近い', '目立った傷や汚れなし']),
            'item_image_path' => 'https://placehold.co/600x400/000000/FFFFFF/png?text=Item+Image',
            'is_sold' => false,
        ];
    }
}
