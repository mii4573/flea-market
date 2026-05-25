<?php

namespace Tests\Feature; // 💡 修正：Featureの重複を直しました

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * ID 1: 会員登録機能 - 名前が入力されていない場合
     */
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '', // 空っぽ
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * ID 1: 会員登録機能 - メールアドレスが入力されていない場合
     */
    public function test_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => '', // 空っぽ
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * ID 1: 会員登録機能 - パスワードが入力されていない場合
     */
    public function test_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '', // 空っぽ
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * ID 1: 会員登録機能 - パスワードが7文字以下の場合
     */
    public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'pass123', // 7文字
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * ID 1: 会員登録機能 - パスワードが確認用と一致しない場合
     */
    public function test_password_must_match_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password', // 一致しない
        ]);

        $response->assertSessionHasErrors(['password_confirmation']);
    }

    /**
     * ID 1: 会員登録機能 - 全ての項目が正しく入力されている場合（正常系）
     */
    public function test_user_can_register_and_redirect_to_profile_setting()
    {
        // 1. フォームの送信シミュレーション
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 2. 期待挙動：会員情報がデータベースに登録されていること
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        // 3. 期待挙動：メール認証誘導画面（今回は設定された /mypage ）にリダイレクトされること
        $response->assertRedirect('/mypage/profile'); 
    }

    /**
     * ID 2: ログイン機能 - メールアドレスが入力されていない場合
     */
    public function test_login_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '', // 空っぽ
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * ID 2: ログイン機能 - パスワードが入力されていない場合
     */
    public function test_login_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '', // 空っぽ
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * ID 2: ログイン機能 - 入力情報が間違っている場合
     */
    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com', // 存在しないメール
            'password' => 'wrong_password',
        ]);

        // 💡 一般的なLaravel認証では、ログイン失敗時は 'email' キーにエラーが入ります
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * ID 2: ログイン機能 - 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function test_user_can_login_with_correct_credentials()
    {
        // 先にテスト用のユーザーをデータベースに1件作っておく
        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'login-test@example.com',
            'password' => bcrypt('password123'), // 暗号化して保存
        ]);

        $response = $this->post('/login', [
            'email' => 'login-test@example.com',
            'password' => 'password123',
        ]);

        // 💡 期待挙動：指定したユーザーが「ログイン状態」になっていること
        $this->assertAuthenticatedAs($user);

        // 💡 ログイン後のリダイレクト先（トップページ '/' やダッシュボード等）に合わせて調整してください
        $response->assertRedirect('/'); 
    }

    /**
     * ID 3: ログアウト機能 - ログアウトができる
     */
    public function test_user_can_logout()
    {
        // 1. テストユーザーを作成
        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'logout-test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 2. そのユーザーとしてログイン状態からスタートする命令
        $this->actingAs($user);

        // 3. ログアウトボタン（通常は POST で /logout）を押す
        $response = $this->post('/logout');

        // 4. 期待挙動：ユーザーが「未ログイン状態（ゲスト）」になっていること
        $this->assertGuest();

        // ログアウト後のリダイレクト先（通常はトップ '/' などの確認）
        $response->assertRedirect('/');
    }
}