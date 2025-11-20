<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    /**
     * テスト実行前のセットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();
        // StorageのFake化（画像アップロード処理をシミュレートするため）
        Storage::fake('public');

        // 初期プロフィール情報を持つユーザーを作成
        $this->user = User::factory()->create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'postcode' => '1234567',
            'address' => '東京都渋谷区',
            'building' => 'テストビル101',
            'profile_image_path' => 'profile_images/initial_image.jpg',
            'profile_updated' => false,
        ]);
    }

    /**
     * 【テストケース１４-１】プロフィール編集画面で初期値が正しく表示されること
     * 期待挙動: 各項目の初期値（ユーザー名、郵便番号、住所、建物名）がフォームに設定されている
     * @test
     */
    public function case_14_1_profile_edit_page_displays_initial_values()
    {
        // 1. 実行: ログインユーザーとしてプロフィール編集ページにアクセス
        $response = $this->actingAs($this->user)->get(route('profile.edit'));

        // 2. 期待挙動の検証: 正常にアクセスできること
        $response->assertStatus(200);
        $response->assertViewIs('users.edit');

        // 3. 期待挙動の検証: 各項目の初期値が正しく表示されていること

        // ユーザー名 (HTMLフォームのvalue属性などを確認)
        $response->assertSee($this->user->name);

        // 郵便番号
        $response->assertSee($this->user->postcode);

        // 住所
        $response->assertSee($this->user->address);

        // 建物名 (null許容)
        $response->assertSee($this->user->building);

        // プロフィール画像パス
        // Storage::url() を使用している場合、ファイル名の一部を確認
        $response->assertSee('initial_image.jpg');
    }

    /**
     * 【テストケース１４-２】正常な入力でプロフィール情報が更新されること
     * 期待挙動: 更新後の情報がデータベースに反映され、マイページにリダイレクトされる
     * @test
     */
    public function case_14_2_profile_is_successfully_updated()
    {
        // 準備: 更新データ
        $updatedName = '新テスト太郎';
        $updatedPostcode = '9876543';
        $updatedAddress = '大阪府大阪市';
        $updatedBuilding = '新テストマンション202';

        // GD拡張機能の依存を避けるため、画像MIMEタイプを指定したダミーファイルを生成
        // $newImage = UploadedFile::fake()->image('new_profile.jpg'); // 元のGD依存コード
        $newImage = UploadedFile::fake()->create('new_profile.jpg', 500, 'image/jpeg');

        // 1. 実行: プロフィール更新リクエストを送信
        $response = $this->actingAs($this->user)->post(route('profile.update'), [
            'name' => $updatedName,
            'postcode' => $updatedPostcode,
            'address' => $updatedAddress,
            'building' => $updatedBuilding,
            'profile_image' => $newImage,
        ]);

        // 2. 期待挙動の検証: マイページにリダイレクトされること
        // Controllerのコードに基づいて、正常時はuser.mypageにリダイレクトされることを確認
        $response->assertRedirect(route('user.mypage'));
        $response->assertSessionHasNoErrors();

        // 3. 期待挙動の検証: データベースの情報が更新されていること
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $updatedName,
            'postcode' => $updatedPostcode,
            'address' => $updatedAddress,
            'building' => $updatedBuilding,
            'profile_updated' => true,
        ]);

        // 4. 期待挙動の検証: 新しい画像が保存されていること
        // 新しい画像は 'profile_images/' ディレクトリに保存されているはず
        Storage::disk('public')->assertExists('profile_images/' . $newImage->hashName());
    }
}
