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

        // レスポンス
        $response->assertStatus(201)
            ->assertJsonFragment([
                'email' => $userData['email'],
            ]);

        // データが登録されているか
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

        // バリデーションエラーが発生するか
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

        // レスポンス
        $response->assertStatus(200);

        // レスポンス内容を確認
        $responseData = $response->json();
        $this->assertArrayHasKey('user', $responseData); // ユーザー情報が存在するか
        $this->assertArrayHasKey('token', $responseData); // トークンが存在するか

        // DBのユーザー情報と一致するか
        $this->assertEquals($user->id, $responseData['user']['id']);
        $this->assertEquals($user->name, $responseData['user']['name']);
        $this->assertEquals($user->email, $responseData['user']['email']);

        // トークンが生成されているか
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

        // 認証エラーを期待
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

        // 認証エラーを期待
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

        // バリデーションエラーが発生するか
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * サインアウトAPI（POST /api/signout）
     */
    public function test_サインアウトAPIで正常にログアウトできるか(): void
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // サインアウトAPI実行
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/signout');

        // レスポンス
        $response->assertStatus(200);

        // トークンが無効になっているか
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'test-token',
        ]);
    }


}
