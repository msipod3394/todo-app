<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー登録API（POST /api/signup）
     */
    public function test_ユーザー登録APIで正常に登録できるか(): void
    {
        // 登録するデータ
        $userData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // ユーザー登録API実行
        $response = $this->json('POST', '/api/signup', $userData);

        // レスポンスをアサート
        $response->assertStatus(201)->assertJson([
            'user' => [
                'email' => $userData['email'],
            ],
        ]);

        // DB確認（データの存在とリレーションの確認）
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }

    public function test_ユーザー登録APIでバリデーションエラーが発生するか(): void
    {
        // エラーが発生するデータ
        $invalidData = [
            'email' => 'test',
            'password' => '123',
        ];

        // ユーザー登録API実行
        $response = $this->json('POST', '/api/signup', $invalidData);

        // レスポンスをアサート
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * サインインAPI（POST /api/signin）
     */
    public function test_サインインAPIで正常にログインできるか(): void
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // サインインデータ
        $signinData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // サインインAPI実行
        $response = $this->json('POST', '/api/signin', $signinData);

        // レスポンスをアサート
        $response->assertStatus(200)->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);

        // トークンが生成されているか
        $responseData = $response->json();
        $this->assertArrayHasKey('token', $responseData);
        $this->assertNotEmpty($responseData['token']);
    }

    public function test_サインインAPIで無効な認証情報でエラーが発生するか(): void
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $invalidSigninData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        // サインインAPI実行
        $response = $this->json('POST', '/api/signin', $invalidSigninData);

        // レスポンスをアサート
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_サインインAPIで存在しないユーザーでエラーが発生するか(): void
    {
        // 存在しないユーザーのデータ
        $notExistentSigninData = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        // サインインAPI実行
        $response = $this->json('POST', '/api/signin', $notExistentSigninData);

        // レスポンスをアサート
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_サインインAPIでバリデーションエラーが発生するか(): void
    {
        // 空のパスワードを設定
        $invalidData = [
            'email' => 'invalid-email',
            'password' => '', // 空のパスワード
        ];

        // サインインAPI実行
        $response = $this->json('POST', '/api/signin', $invalidData);

        // レスポンスをアサート
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * サインアウトAPI（POST /api/signout）
     */
    public function test_サインアウトAPIで正常にサインアウトできるか(): void
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // サインアウトAPI実行
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/signout');

        // レスポンスをアサート
        $response->assertStatus(200);

        // DB確認（トークンが削除されていることを確認）
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'test-token',
        ]);
    }

}
