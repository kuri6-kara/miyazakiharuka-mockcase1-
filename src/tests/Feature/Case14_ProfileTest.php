<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * ユーザー情報変更機能の機能テスト
 */
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
     * 【テスト内容】プロフィール編集画面で初期値が正しく表示されること
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
        // ユーザー名
        $response->assertSee($this->user->name);

        // 郵便番号
        $response->assertSee($this->user->postcode);

        // 住所
        $response->assertSee($this->user->address);

        // 建物名 (null許容)
        $response->assertSee($this->user->building);

        // プロフィール画像パス
        $response->assertSee('initial_image.jpg');
    }
}
