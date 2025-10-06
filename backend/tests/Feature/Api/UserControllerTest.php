<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    // データベースのリセット
    use RefreshDatabase;

    /**
     * 認証ユーザー情報取得API（GET /api/user）
     */
    public function test_認証ユーザー情報取得APIでデータが取得できることを確認(): void
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // API実行
        $response = $this->actingAs($user)->json('GET', '/api/user');

        // レスポンスをアサート
        $response->assertStatus(200)->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * 認証ユーザー情報取得API（GET /api/me）
     */
    public function test_UserControllerのmeメソッドでデータが取得できることを確認(): void
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // API実行
        $response = $this->actingAs($user)->json('GET', '/api/me');

        // レスポンスをアサート
        $response->assertStatus(200)->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * 認可チェック
     */
    public function test_認証なしでユーザー情報取得APIにアクセスできないことを確認(): void
    {
        // 認証なしでAPI実行
        $response = $this->json('GET', '/api/user');

        // レスポンスをアサート
        $response->assertStatus(401);
    }

    public function test_認証なしでmeメソッドにアクセスできないことを確認(): void
    {
        // 認証なしでAPI実行
        $response = $this->json('GET', '/api/me');

        // レスポンスをアサート
        $response->assertStatus(401);
    }

    public function test_他のユーザーの情報にアクセスできないことを確認(): void
    {
        // 2ユーザー作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user1でuser2の情報にアクセスできるか
        $response = $this->actingAs($user1)->json('GET', "/api/users/{$user2->id}");

        // レスポンスをアサート
        $response->assertStatus(404);
    }

}
