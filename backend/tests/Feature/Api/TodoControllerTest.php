<?php

namespace Tests\Feature\Api;

use App\Models\Todo; // Todoモデル
use App\Models\User; // Userモデル
use Illuminate\Foundation\Testing\RefreshDatabase; // データベースのリセット
use Tests\TestCase; // テストクラス

class TodoControllerTest extends TestCase
{

    // データベースのリセット
    use RefreshDatabase;

    public function test_todoの登録APIでデータが登録できることを確認(): void
    {
        // APIを実行するユーザーを作成
        $user = User::factory()->create();

        // 送信データを定義
        $postData = [
            'title' => 'テストTodoタイトル',
            'deadline_date' => '2025-12-31',
        ];

        // API実行
        $response = $this->actingAs($user)->json(
            'POST',
            route('todos.store'),
            $postData
        );

        // Todoが正常に作成されたことを確認
        $response->assertStatus(201);

        // メッセージの値確認
        $response->assertJsonFragment([
            'message' => 'TODOが正常に作成されました',
        ]);

        // データの値確認
        $response->assertJsonFragment([
            'title' => $postData['title'],
            'user_id' => $user->id,
        ]);

        // データの構造確認
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'deadline_date',
                'user_id',
                'created_at',
                'updated_at',
            ]
        ]);

        // todosテーブルにデータが登録できていることを確認
        $this->assertDatabaseHas('todos', [
            'title' => $postData['title'],
            'deadline_date' => $postData['deadline_date'],
            'user_id' => $user->id,
            'completed_at' => null,
        ]);
    }

    public function test_todoの登録APIで期限なしのデータが登録できることを確認()
    {
        // APIを実行するユーザーを作成
        $user = User::factory()->create();

        // 送信データを定義（期限なし）
        $postData = [
            'title' => '期限なしのTodoタイトル',
        ];

        // API実行
        $response = $this->actingAs($user)->json(
            'POST',
            route('todos.store'),
            $postData
        );

        // レスポンスのアサート
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'message' => 'TODOが正常に作成されました',
        ]);
        $response->assertJsonFragment([
            'title' => $postData['title'],
            'user_id' => $user->id,
        ]);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'title',
                'user_id',
                'created_at',
                'updated_at',
            ]
        ]);

        // DBアサート
        $this->assertDatabaseHas('todos', [
            'title' => $postData['title'],
            'deadline_date' => null,
            'user_id' => $user->id,
            'completed_at' => null,
        ]);
    }

    public function test_todoの登録APIでバリデーションエラーが発生することを確認()
    {
        // APIを実行するユーザーを作成
        $user = User::factory()->create();

        // 送信データを定義（タイトルが未入力）
        $postData = [
            'deadline_date' => '2025-12-31',
        ];

        // API実行
        $response = $this->actingAs($user)->json(
            'POST',
            route('todos.store'),
            $postData
        );

        // レスポンスのアサート
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_todoの一覧取得APIでデータが取得できることを確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // テスト用のTodoを3件作成
        $todos = Todo::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        // API実行
        $response = $this->actingAs($user)->json(
            'GET',
            route('todos.index')
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Todo一覧を取得しました',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_todoの未完了一覧取得APIでデータが取得できることを確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // 未完了のTodoを作成
        $uncompletedTodos = Todo::factory()->count(2)->create([
            'user_id' => $user->id,
            'completed_at' => null
        ]);

        // 完了済みのTodoを作成
        $completedTodos = Todo::factory()->count(1)->create([
            'user_id' => $user->id,
            'completed_at' => now()
        ]);

        // API実行
        $response = $this->actingAs($user)->json(
            'GET', '/api/todos/uncompleted'
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'message' => '未完了のTodo一覧を取得しました',
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_todoの完了一覧取得APIでデータが取得できることを確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // 未完了のTodoを作成
        $uncompletedTodos = Todo::factory()->count(1)->create([
            'user_id' => $user->id,
            'completed_at' => null
        ]);

        // 完了済みのTodoを作成
        $completedTodos = Todo::factory()->count(2)->create([
            'user_id' => $user->id,
            'completed_at' => now()
        ]);

        // API実行
        $response = $this->actingAs($user)->json(
            'GET','/api/todos/completed'
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'message' => '完了したTodo一覧を取得しました',
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_todoの更新APIでデータが更新できることを確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // テスト用のTodoを作成
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'title' => 'テストタイトル',
            'deadline_date' => '2025-12-31'
        ]);

        // 更新データを定義
        $updateData = [
            'title' => 'テストタイトルを更新',
            'deadline_date' => '2026-01-31'
        ];

        // API実行
        $response = $this->actingAs($user)->json(
            'PUT',
            route('todos.update', $todo->id),
            $updateData
        );

        // レスポンスの確認
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Todoを更新しました'])
            ->assertJsonFragment(['title' => $updateData['title']]);

        // 日付のレスポンス確認
        $responseData = $response->json('data');
        $this->assertStringContainsString('2026-01-30', $responseData['deadline_date']);

        // DBの確認
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => $updateData['title'],
            'deadline_date' => $updateData['deadline_date'],
        ]);
    }

}
