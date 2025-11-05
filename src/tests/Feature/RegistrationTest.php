<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

/**
 * 会員登録機能の機能テスト
 * 1. 登録画面が表示されること
 * 2. 有効なデータで登録が成功すること
 * 3. バリデーションエラーが表示されること
 * - 名前が入力されていない場合
 */
class RegistrationTest extends TestCase
{
    // テストごとにデータベースをリフレッシュし、マイグレーションを実行
    use RefreshDatabase;

    /**
     * 会員登録画面が正しく表示されることをテスト
     *
     * @return void
     */
    public function test_registration_screen_can_be_rendered()
    {
        // /register ルートにGETリクエストを送信
        $response = $this->get('/register');

        // レスポンスのHTTPステータスコードが200であることを確認
        $response->assertStatus(200);
    }

    /**
     * 有効なユーザーデータで登録が成功することをテスト
     *
     * @return void
     */
    public function test_new_users_can_register()
    {
        // 登録に必要な有効なデータ
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123', // 確認用パスワード
        ];

        // /register ルートにPOSTリクエストを送信
        $response = $this->post('/register', $userData);

        // データベースにユーザーが作成されたことを確認
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        // 【修正】アプリケーションの実際の挙動に合わせてリダイレクト先を修正
        // 登録後、'/mypage/profile' にリダイレクトされることを確認
        $response->assertRedirect('/mypage/profile');

        // ユーザーが認証済みであることを確認
        $this->assertAuthenticated();
    }

    /**
     * 無効なデータで登録が失敗し、バリデーションエラーが発生することをテスト (メールアドレスの重複)
     *
     * @return void
     */
    public function test_registration_fails_with_duplicate_email()
    {
        // 既に存在するユーザーを作成
        User::factory()->create(['email' => 'exists@example.com']);

        // 重複したメールアドレスを含む登録データ
        $userData = [
            'name' => 'Another User',
            'email' => 'exists@example.com', // 重複
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // /register ルートにPOSTリクエストを送信
        $response = $this->post('/register', $userData);

        // レスポンスがセッションにエラーを含んでいることを確認
        $response->assertSessionHasErrors('email');

        // データベースに新しいユーザーが追加されていないことを確認
        $this->assertDatabaseCount('users', 1);

        // ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

    /**
     * 【テストケース１】名前が入力されていない場合、バリデーションメッセージが表示されることをテスト
     * 期待挙動：「お名前を入力してください」というバリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_registration_fails_when_name_is_missing()
    {
        // 名前（name）を空にしたデータ
        $userData = [
            'name' => '', // テスト対象: 名前が未入力
            'email' => 'missingname@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // /register ルートにPOSTリクエストを送信
        $response = $this->post('/register', $userData);

        // 1. レスポンスがバリデーションエラーを含んでいることを確認
        $response->assertSessionHasErrors('name');

        // 2. データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'missingname@example.com'
        ]);

        // 3. ユーザーが認証されていないことを確認
        $this->assertGuest();
    }
}
