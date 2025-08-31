<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザー登録が成功する()
    {
        $response = $this->postJson('/api/signup', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'user' => ['id', 'name', 'email', 'created_at'],
                    'token'
                ]);

        // データベースに保存されているか確認
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function メール形式が間違っている場合はエラー()
    {
        $response = $this->postJson('/api/signup', [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function パスワードが短い場合はエラー()
    {
        $response = $this->postJson('/api/signup', [
            'email' => 'test@example.com',
            'password' => '123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function 重複メールアドレスの場合はエラー()
    {
        // 事前にユーザーを作成
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/signup', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function ログインが成功する()
    {
        // 事前にユーザーを作成
        $user = User::create([
            'name' => 'Test User',
            'email' => 'login@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/signin', [
            'email' => 'login@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'user' => ['id', 'name', 'email', 'created_at'],
                    'token'
                ]);
    }

    /** @test */
    public function 存在しないメールアドレスでログイン失敗()
    {
        $response = $this->postJson('/api/signin', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'メールアドレスまたはパスワードが間違っています。'
                ]);
    }

    /** @test */
    public function 間違ったパスワードでログイン失敗()
    {
        // 事前にユーザーを作成
        User::create([
            'name' => 'Test User',
            'email' => 'wrong@example.com',
            'password' => bcrypt('correctpassword')
        ]);

        $response = $this->postJson('/api/signin', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'メールアドレスまたはパスワードが間違っています。'
                ]);
    }

    /** @test */
    public function ログアウトが成功する()
    {
        // 事前にユーザーを作成してログイン
        $user = User::create([
            'name' => 'Test User',
            'email' => 'logout@example.com',
            'password' => bcrypt('password123')
        ]);
        
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/signout');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'ログアウトしました。'
                ]);
    }

    /** @test */
    public function 認証なしでは保護されたルートにアクセスできない()
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    /** @test */
    public function 無効なトークンでは保護されたルートにアクセスできない()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token',
            'Accept' => 'application/json'
        ])->getJson('/api/me');

        $response->assertStatus(401);
    }

    /** @test */
    public function 有効なトークンでは保護されたルートにアクセスできる()
    {
        // ユーザー作成とトークン発行
        $user = User::create([
            'name' => 'Test User',
            'email' => 'protected@example.com',
            'password' => bcrypt('password123')
        ]);
        
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('/api/me');

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]);
    }
}
