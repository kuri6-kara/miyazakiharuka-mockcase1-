<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * 出品商品情報登録機能の機能テスト
 */
class ItemRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;
    protected $conditionId = 1; // Conditionモデルがないため、テスト用にIDを固定

    /**
     * テスト実行前のセットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();
        // StorageのFake化
        Storage::fake('public');

        // ログインユーザーを作成
        $this->user = User::factory()->create();

        // 1. Categoryデータを作成
        $this->category = Category::create(['category' => 'テストカテゴリ']);
    }

    /**
     * 【テスト内容】正常な入力で商品情報が登録されること
     * @test
     */
    public function case_15_item_is_successfully_registered()
    {
        // 準備: 登録データ
        $itemImage = UploadedFile::fake()->create('test_item.jpg', 1000, 'image/jpeg');

        $itemData = [
            'name' => 'テスト商品名',
            'description' => 'これはテスト用の商品説明です。',
            'categories' => [$this->category->id],
            'condition' => 'これはテスト用の商品です。',
            'price' => 5000,
            'brand_name' => 'テストブランド',
            'item_image' => $itemImage,
        ];

        // 1. 実行: ログインユーザーとして商品登録リクエストを送信
        $response = $this->actingAs($this->user)->post(route('item.store'), $itemData);

        // 2. 期待挙動の検証: 正常にリダイレクトされ、セッションにエラーがないこと
        $response->assertRedirect(route('item.index'));
        $response->assertSessionHasNoErrors();

        // 3. 期待挙動の検証: データベースに商品情報が正しく保存されていること (itemsテーブル)
        $this->assertDatabaseHas('items', [
            'user_id' => $this->user->id,
            'name' => $itemData['name'],
            'description' => $itemData['description'],
            'condition' => $itemData['condition'],
            'price' => $itemData['price'],
            'brand_name' => $itemData['brand_name'],
        ]);

        // 保存された商品を取得し、IDを取得
        $item = DB::table('items')->latest()->first();

        // 4. 期待挙動の検証: カテゴリ情報が中間テーブルに正しく保存されていること (item_categoryテーブル)
        $this->assertDatabaseHas('item_category', [
            'item_id' => $item->id,
            'category_id' => $this->category->id,
        ]);


        // 5. 期待挙動の検証: 商品画像がStorageに保存されていること
        Storage::disk('public')->assertExists('item_images/' . $itemImage->hashName());
    }
}
