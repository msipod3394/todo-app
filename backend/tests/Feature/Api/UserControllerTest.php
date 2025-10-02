<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    // データベースのリセット
    use RefreshDatabase;

    public function test_認証ユーザー情報取得APIでデータが取得できることを確認(): void
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // API実行
        $response = $this->actingAs($user)->json('GET', '/api/user');

        // レスポンス（データが取得できるか）
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_UserControllerのmeメソッドでデータが取得できることを確認(): void
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // API実行
        $response = $this->actingAs($user)->json('GET', '/api/me');

        // レスポンス（meメソッドでデータが取得できるか）
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_認証なしでユーザー情報取得APIにアクセスできないことを確認(): void
    {
        // 認証なしでAPI実行
        $response = $this->json('GET', '/api/user');

        // レスポンス（401エラーを期待）
        $response->assertStatus(401);
    }

    public function test_認証なしでmeメソッドにアクセスできないことを確認(): void
    {
        // 認証なしでAPI実行
        $response = $this->json('GET', '/api/me');

        // レスポンス（401エラーを期待）
        $response->assertStatus(401);
    }

    public function test_異なるユーザーでアクセスしても自分の情報のみ取得できることを確認(): void
    {
        // 2ユーザー作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user1で自分の情報を取得
        $response = $this->actingAs($user1)->json('GET', '/api/user');

        // user1の情報のみが返されることを確認
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user1->id,
                'name' => $user1->name,
                'email' => $user1->email,
            ])
            ->assertJsonMissing([
                'id' => $user2->id,
                'email' => $user2->email,
            ]);
    }

    public function test_他のユーザーの情報に直接アクセスできないことを確認(): void
    {
        // 2ユーザー作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user1でuser2の情報に直接アクセスを試行
        $response = $this->actingAs($user1)->json('GET', "/api/users/{$user2->id}");

        // レスポンス（404エラーを期待）
        $response->assertStatus(404);
    }

}