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

    /**
     * todoの登録API（POST /api/todos）
     */
    public function test_todoの登録APIでデータが登録できることを確認(): void
    {
        // APIを実行するユーザーを作成
        $user = User::factory()->create();

        // 送信データを定義
        $postData = [
            'title' => 'テストタイトル',
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

        // データの値確認
        $response->assertJsonFragment([
            'title' => $postData['title'],
            'user_id' => $user->id,
        ]);

        // todosテーブルにデータが登録できていることを確認
        $this->assertDatabaseHas('todos', [
            'title' => $postData['title'],
            'deadline_date' => $postData['deadline_date'] . ' 00:00:00',
            'user_id' => $user->id,
            'completed_at' => null,
        ]);
    }

    public function test_todoの登録APIで期限なしのデータが登録できることを確認()
    {
        // APIを実行するユーザーを作成
        $user = User::factory()->create();

        // 期限なしのデータ
        $postData = [
            'title' => '期限なしのタイトル',
        ];

        // API実行
        $response = $this->actingAs($user)->json(
            'POST',
            route('todos.store'),
            $postData
        );

        // レスポンスのアサート
        $response->assertStatus(201)
        ->assertJsonFragment([
            'title' => $postData['title'],
            'user_id' => $user->id,
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
            ->assertJsonValidationErrors(['title'])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'title' => []
                ]
            ]);

        // エラーメッセージの内容確認
        $responseData = $response->json();
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertArrayHasKey('title', $responseData['errors']);
        $this->assertNotEmpty($responseData['errors']['title']);

        // DB確認（データが作成されていないことを確認）
        $this->assertDatabaseCount('todos', 0);
        $this->assertDatabaseMissing('todos', [
            'deadline_date' => '2025-12-31 00:00:00',
            'user_id' => $user->id,
        ]);
    }

    /**
     * todoの一覧取得API（GET /api/todos）
     */
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

        // レスポンスをアサート
        $response->assertStatus(200)->assertJson([
            'data' => [
                ['user_id' => $user->id],
                ['user_id' => $user->id],
                ['user_id' => $user->id],
            ]
        ]);

        // DB確認（作成したTodoが存在することを確認）
        $this->assertDatabaseCount('todos', 3);
        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
        ]);
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

        // レスポンスをアサート
        $response->assertStatus(200)->assertJson([
            'data' => [
                ['user_id' => $user->id, 'completed_at' => null],
                ['user_id' => $user->id, 'completed_at' => null],
            ]
        ]);

        // DB確認（未完了のTodoのみが存在することを確認）
        $this->assertDatabaseCount('todos', 3);
        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'completed_at' => null,
        ]);
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

        // レスポンスをアサート
        $response->assertStatus(200)->assertJson([
            'data' => [
                ['user_id' => $user->id],
                ['user_id' => $user->id],
            ]
        ]);

        // DB確認（完了済みのTodoのみが存在することを確認）
        $this->assertDatabaseCount('todos', 3);
        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * todoの更新API（PUT /api/todos/{id}）
     */
    public function test_todoの更新APIでデータが更新できることを確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // Todoを作成
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
            ->assertJsonFragment(['title' => $updateData['title']]);

        // 日付のレスポンス確認
        $responseData = $response->json('data');
        $this->assertStringContainsString('2026-01-30', $responseData['deadline_date']);

        // DB確認（データが更新されていることを確認）
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => $updateData['title'],
            'deadline_date' => $updateData['deadline_date'] . ' 00:00:00',
        ]);
    }

    /**
     * todoの完了API（PATCH /api/todos/{id}/completed）
     */
    public function test_todoの完了APIでデータが更新できることを確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // Todoを作成
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed_at' => null
        ]);

        // API実行
        $response = $this->actingAs($user)->json(
            'PATCH',"/api/todos/{$todo->id}/completed"
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $todo->id,
                    'user_id' => $user->id,
                ]
            ]);

        // DBアサート
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * todoの未完了API（PATCH /api/todos/{id}/uncompleted）
     */
    public function test_todoの未完了APIでデータが更新できることを確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // Todoを作成（完了済み）
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now()
        ]);

        // API実行
        $response = $this->actingAs($user)->json(
            'PATCH',"/api/todos/{$todo->id}/uncompleted"
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $todo->id,
                    'user_id' => $user->id,
                    'completed_at' => null,
                ]
            ]);

        // DBアサート
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed_at' => null,
        ]);
    }

    /**
     * todoの削除API（DELETE /api/todos/{id}）
     */
    public function test_todoの削除APIでデータが削除できることを確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // Todoを作成
        $todo = Todo::factory()->create([
            'user_id' => $user->id
        ]);

        // API実行
        $response = $this->actingAs($user)->json(
            'DELETE',
            route('todos.destroy', $todo->id)
        );

        // レスポンスのアサート
        $response->assertStatus(200);

        // DBアサート
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }

    /**
     * 認可チェック
     */
    public function test_他のユーザーのtodoにアクセスできないことを確認()
    {
        // ユーザーを作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user2のTodoを作成
        $todo = Todo::factory()->create([
            'user_id' => $user2->id
        ]);

        // user1でuser2のTodoにアクセス
        $response = $this->actingAs($user1)->json(
            'GET',
            route('todos.index')
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'data' => []
            ])
            ->assertJsonCount(0, 'data');

        // user2のTodoが含まれていないことを確認
        $responseData = $response->json('data');
        foreach ($responseData as $todoData) {
            $this->assertNotEquals($todo->id, $todoData['id']);
        }
    }

    public function test_認証なしでAPIにアクセスできないことを確認()
    {
        // 認証なしでAPI実行
        $response = $this->json('GET', route('todos.index'));

        // レスポンスのアサート（401エラーを期待）
        $response->assertStatus(401)
            ->assertJsonStructure([
                'message'
            ]);

        // エラーメッセージの内容確認
        $responseData = $response->json();
        $this->assertArrayHasKey('message', $responseData);
        $this->assertStringContainsString('Unauthenticated', $responseData['message']);
    }

}
