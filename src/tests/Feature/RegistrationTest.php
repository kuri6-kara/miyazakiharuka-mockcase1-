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
