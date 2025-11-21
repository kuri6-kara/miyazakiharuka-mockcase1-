<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 会員登録機能の機能テスト
 */
class Case01_RegistrationTest extends TestCase
{
    // テストごとにデータベースをリフレッシュし、マイグレーションを実行
    use RefreshDatabase;

    /**
     * 【テスト内容１】名前が入力されていない場合、バリデーションメッセージが表示されることをテスト
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

    /**
     * 【テスト内容２】メールアドレスが入力されていない場合、バリデーションメッセージが表示されることをテスト
     * 期待挙動：「メールアドレスを入力してください」というバリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_registration_fails_when_email_is_missing()
    {
        // メールアドレス（email）を空にしたデータ
        $userData = [
            'name' => 'User Without Email',
            'email' => '', // テスト対象: メールアドレスが未入力
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // /register ルートにPOSTリクエストを送信
        $response = $this->post('/register', $userData);

        // 1. レスポンスがバリデーションエラーを含んでいることを確認
        $response->assertSessionHasErrors('email');

        // 2. データベースにユーザーが作成されていないことを確認 (メールアドレスが空のユーザーは存在しないはず)
        // 今回のPOSTデータに合わせて、メールアドレスが空のレコードがないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => ''
        ]);

        // 3. ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

    /**
     * 【テスト内容３】パスワードが入力されていない場合、バリデーションメッセージが表示されることをテスト
     * 期待挙動：「パスワードを入力してください」というバリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_registration_fails_when_password_is_missing()
    {
        // パスワード（password）を空にしたデータ
        $userData = [
            'name' => 'User Without Password',
            'email' => 'missingpass@example.com',
            'password' => '', // テスト対象: パスワード未入力
            'password_confirmation' => '', // 確認用パスワードも空にする
        ];

        // /register ルートにPOSTリクエストを送信
        $response = $this->post('/register', $userData);

        // 1. レスポンスがバリデーションエラーを含んでいることを確認
        $response->assertSessionHasErrors('password');

        // 2. データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'missingpass@example.com'
        ]);

        // 3. ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

    /**
     * 【テスト内容４】パスワードが７文字以下の場合、バリデーションメッセージが表示されることをテスト
     * 期待挙動：「パスワードは８文字以上で入力してください」というバリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_registration_fails_when_password_is_too_short()
    {
        // パスワード（password）を7文字（最小文字数8未満）にしたデータ
        $userData = [
            'name' => 'User With Short Password',
            'email' => 'shortpass@example.com',
            'password' => 'pass123', // 7文字
            'password_confirmation' => 'pass123',
        ];

        // /register ルートにPOSTリクエストを送信
        $response = $this->post('/register', $userData);

        // 1. レスポンスがバリデーションエラーを含んでいることを確認
        $response->assertSessionHasErrors('password');

        // 2. データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'shortpass@example.com'
        ]);

        // 3. ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

    /**
     * 【テスト内容５】パスワードが確認用パスワードと一致しない場合、バリデーションエラーが表示されることをテスト
     * 期待挙動：「パスワードと一致しません」というバリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_registration_fails_when_password_and_confirmation_do_not_match()
    {
        // パスワードと確認用パスワードを異なる値にしたデータ
        $userData = [
            'name' => 'User With Mismatch Password',
            'email' => 'mismatch@example.com',
            'password' => 'correctpassword',
            'password_confirmation' => 'wrongpassword',
        ];

        // /register ルートにPOSTリクエストを送信
        $response = $this->post('/register', $userData);

        // 1. レスポンスがバリデーションエラーを含んでいることを確認
        $response->assertSessionHasErrors('password');

        // 2. データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'mismatch@example.com'
        ]);

        // 3. ユーザーが認証されていないことを確認
        $this->assertGuest();
    }

    /**
     * 【テスト内容６】全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移されることをテスト
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
            'password_confirmation' => 'password123',
        ];

        // /register ルートにPOSTリクエストを送信
        $response = $this->post('/register', $userData);

        // 1. データベースにユーザーが作成されたことを確認
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        // 2. 登録後、プロフィール設定画面('/mypage/profile') にリダイレクトされることを確認 (期待挙動: 画面遷移)
        $response->assertRedirect('/register/profile');

        // 3. ユーザーが認証済みであることを確認 (期待挙動: 会員情報が登録された)
        $this->assertAuthenticated();
    }
}
