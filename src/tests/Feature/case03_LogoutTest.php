<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ログアウト機能の機能テスト
 */
class Case03_LogoutTest extends TestCase
{
    use RefreshDatabase;

    // 共通のテストユーザー
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        // テスト前に認証済みの有効なユーザーを作成
        $this->user = User::factory()->create();
    }

    /**
     * 【テスト内容】ログアウト処理が実行され、ユーザーが未認証状態になることをテスト
     *
     * @return void
     */
    public function test_users_can_logout()
    {
        // 1. ユーザーを認証済みにする
        $this->actingAs($this->user);

        // 2. /logout ルートにPOSTリクエストを送信
        $response = $this->post('/logout');

        // 3. ユーザーが未認証状態になったことを確認
        $this->assertGuest();

        // 4. リダイレクト先がトップページ（/）であることを確認
        $response->assertRedirect('/');

        // 【追加の確認】ステータスコードが302 (リダイレクト) であることを確認
        $response->assertStatus(302);
    }
}
