<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ログイン機能の機能テスト
 */
class LoginTest extends TestCase
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

    /**
     * 【テストケース3】無効な認証情報ではログインに失敗することをテスト
     * 期待挙動：エラーメッセージが表示され、認証されない
     *
     * @return void
     */
    public function test_users_cannot_authenticate_with_invalid_credentials()
    {
        // 無効な認証情報 (間違ったパスワード)
        $credentials = [
            'email' => 'login@example.com',
            'password' => 'wrong-password',
        ];

        // /login ルートにPOSTリクエストを送信
        $response = $this->post('/login', $credentials);

        // 1. ユーザーが認証されていないことを確認
        $this->assertGuest();

        // 2. エラーメッセージがセッションにあることを確認
        // Laravel Breeze/Fortifyのデフォルト設定では、'email'フィールドに認証エラーが紐づけられます
        $response->assertSessionHasErrors('email');
    }

    /**
     * 【テストケース4】有効な認証情報でログインできることをテスト
     * 期待挙動：ログイン成功後、ダッシュボードにリダイレクトされ、認証済みとなる
     *
     * @return void
     */
    public function test_users_can_authenticate_with_valid_credentials()
    {
        // 有効な認証情報
        $credentials = [
            'email' => 'login@example.com',
            'password' => 'password123',
        ];

        // /login ルートにPOSTリクエストを送信
        $response = $this->post('/login', $credentials);

        // 1. ユーザーが認証済みであることを確認
        // assertAuthenticated() は、現在のリクエストが認証されたユーザーを持っていることを確認
        $this->assertAuthenticated();

        // 2. リダイレクト先が /dashboard であることを確認
        // デフォルトではログイン成功時に /dashboard にリダイレクトされます
        $response->assertRedirect('/');
    }
}
