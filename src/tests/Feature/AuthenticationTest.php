<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ログイン機能の機能テスト
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // 共通のテストユーザー
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        // テスト前に認証済みの有効なユーザーを作成
        $this->user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password123'),
        ]);
    }


    /**
     * ログイン画面が正しく表示されることをテスト
     *
     * @return void
     */
    public function test_login_screen_can_be_rendered()
    {
        // /login ルートにGETリクエストを送信
        $response = $this->get('/login');

        // レスポンスのHTTPステータスコードが200であることを確認
        $response->assertStatus(200);
    }

    /**
     * 【テストケース１】メールアドレスが入力されていない場合、バリデーションメッセージが表示されることをテスト
     * 期待挙動：「メールアドレスを入力してください」というバリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_login_fails_when_email_is_missing()
    {
        // メールアドレスを空にしたデータ
        $credentials = [
            'email' => '', // テスト対象: メールアドレス未入力
            'password' => 'password123',
        ];

        // /login ルートにPOSTリクエストを送信
        $response = $this->post('/login', $credentials);

        // 1. レスポンスがバリデーションエラーを含んでいることを確認
        $response->assertSessionHasErrors('email');

        // 2. ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

    /**
     * 【テストケース２】パスワードが入力されていない場合、バリデーションメッセージが表示されることをテスト
     * 期待挙動：「パスワードを入力してください」というバリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_login_fails_when_password_is_missing()
    {
        // パスワードを空にしたデータ
        $credentials = [
            'email' => 'login@example.com',
            'password' => '', // テスト対象: パスワード未入力
        ];

        // /login ルートにPOSTリクエストを送信
        $response = $this->post('/login', $credentials);

        // 1. レスポンスがバリデーションエラーを含んでいることを確認
        $response->assertSessionHasErrors('password');

        // 2. ユーザーが認証されていないことを確認
        $this->assertGuest();
    }
}
