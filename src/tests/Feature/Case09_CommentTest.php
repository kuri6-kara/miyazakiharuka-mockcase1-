<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * コメント機能の機能テスト
 */
class Case09_CommentTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $item;

    /**
     * 各テストメソッドの前に実行される共通セットアップ
     */
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のユーザーを作成
        $this->user = User::factory()->create();

        // テスト用の商品を作成
        $this->item = Item::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'テスト商品',
            'price' => 1000,
        ]);
    }

    /**
     * 【テスト内容１】ログイン済みユーザーはコメントを送信できる
     * コメントが保存され、商品詳細ページでコメント数が更新されることを検証
     *
     * @return void
     */
    public function test_case_1_logged_in_user_can_submit_a_comment()
    {
        // 事前確認: コメント数が0であること
        $initialCommentCount = $this->item->comments()->count();
        $this->assertEquals(0, $initialCommentCount);

        $commentText = 'これはテストコメントです。';

        // 実行: ログインユーザーとして、コメント投稿のエンドポイントにPOSTリクエストを送信
        $response = $this->actingAs($this->user)->post(route('comment.store', ['item_id' => $this->item->id]), [
            'comment' => $commentText,
        ]);

        // 1. レスポンスの検証: 商品詳細ページにリダイレクトされること
        $response->assertRedirect(route('item.show', ['item_id' => $this->item->id]));

        // 2. 期待挙動の検証: データベースにコメントレコードが登録されたこと
        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'comment' => $commentText,
        ]);

        // 3. 期待挙動の検証: コメント合計値が増加したこと
        $newCommentCount = $this->item->fresh()->comments()->count();
        $this->assertEquals(1, $newCommentCount);

        // 4. 商品詳細ページでコメントが表示されていることを確認
        $response = $this->get(route('item.show', ['item_id' => $this->item->id]));
        $response->assertSee('コメント(1)');
        $response->assertSee($commentText);
    }

    /**
     * 【テスト内容２】ログイン前のユーザーはコメントを送信できないこと
     * コメントが保存されず、ログインページへリダイレクトされることを検証する。
     *
     * @return void
     */
    public function test_case_2_guest_user_cannot_submit_a_comment()
    {
        $commentText = '非ログインユーザーのテストコメント';

        // 実行: ゲストとして、コメント投稿のエンドポイントにPOSTリクエストを送信
        $response = $this->post(route('comment.store', ['item_id' => $this->item->id]), [
            'comment' => $commentText,
        ]);

        // 1. レスポンスの検証: ログインページにリダイレクトされること
        $response->assertRedirect(route('login'));

        // 2. 期待挙動の検証: データベースにコメントレコードが登録されないこと
        $this->assertDatabaseMissing('comments', [
            'item_id' => $this->item->id,
            'comment' => $commentText,
        ]);
    }

    /**
     * 【テスト内容３】コメントが入力されていない場合、バリデーションメッセージが表示されること
     *
     * @return void
     */
    public function test_case_3_comment_is_required()
    {
        // 実行: ログインユーザーとして、空のコメントでPOSTリクエストを送信
        $response = $this->actingAs($this->user)->post(route('comment.store', ['item_id' => $this->item->id]), [
            'comment' => '', // 空欄
        ]);

        // 1. レスポンスの検証: バリデーションエラーで戻り、エラーメッセージが含まれていること
        $response->assertSessionHasErrors(['comment' => 'コメントは必ず入力してください。']);
        $response->assertRedirect(); // 前のページに戻る（リダイレクト）

        // 2. 期待挙動の検証: データベースにコメントレコードが登録されないこと
        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * 【テスト内容４】コメントが255文字を超えた場合、バリデーションメッセージが表示されること
     * 256文字以上で検証
     *
     * @return void
     */
    public function test_case_4_comment_has_max_length_of_255()
    {
        // 256文字の文字列を作成
        $longComment = str_repeat('あ', 256);

        // 実行: ログインユーザーとして、256文字のコメントでPOSTリクエストを送信
        $response = $this->actingAs($this->user)->post(route('comment.store', ['item_id' => $this->item->id]), [
            'comment' => $longComment,
        ]);

        // 1. レスポンスの検証: バリデーションエラーで戻り、エラーメッセージが含まれていること
        $response->assertSessionHasErrors(['comment' => 'コメントは255文字以内で入力してください。']);
        $response->assertRedirect();

        // 2. 期待挙動の検証: データベースにコメントレコードが登録されないこと
        $this->assertDatabaseCount('comments', 0);
    }
}
