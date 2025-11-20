<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // DBファサードを追加

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

        // 2. Conditionデータが存在することを保証するためのダミー処理（必要に応じて）
        // 暫定的に、conditionsテーブルが存在し、ID:1のデータがあると仮定してテストを進めます。
        // （本番環境ではConditionモデルのFactoryやSeederを使うべきです）
    }

    /**
     * 【テストケース１５】正常な入力で商品情報が登録されること
     * @test
     */
    public function case_15_item_is_successfully_registered()
    {
        // 準備: 登録データ
        $itemImage = UploadedFile::fake()->create('test_item.jpg', 1000, 'image/jpeg');

        // FormRequestのルールに合わせてデータを準備
        $itemData = [
            'name' => 'テスト商品名',
            'description' => 'これはテスト用の商品説明です。',

            // リクエストキー: 'categories' (値はIDの配列)
            'categories' => [$this->category->id],

            // リクエストキー: 'condition' (値はDBに保存されるIDを想定)
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

            // 【修正点】ItemShowTestの構造から、itemsテーブルにはcondition_idが保存されると推測し検証
            'condition' => $itemData['condition'],

            'price' => $itemData['price'],
            'brand_name' => $itemData['brand_name'],
            // item_image_pathは動的に生成されるため、ここでは検証を省略
        ]);

        // 保存された商品を取得し、IDを取得
        $item = DB::table('items')->latest()->first();

        // 4. 期待挙動の検証: カテゴリ情報が中間テーブルに正しく保存されていること (item_categoryテーブル)
        // 【修正点】ItemShowTestと同様に、多対多リレーションシップとして検証
        $this->assertDatabaseHas('item_category', [
            'item_id' => $item->id,
            'category_id' => $this->category->id,
        ]);


        // 5. 期待挙動の検証: 商品画像がStorageに保存されていること
        Storage::disk('public')->assertExists('item_images/' . $itemImage->hashName());
    }

    /**
     * 【補助テスト】商品出品画面にアクセスできること
     * @test
     */
    public function case_15_1_item_create_page_is_accessible()
    {
        $response = $this->actingAs($this->user)->get(route('item.create'));
        $response->assertStatus(200);
        $response->assertViewIs('items.create');
    }
}
