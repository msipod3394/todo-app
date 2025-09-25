<?php

namespace Tests\Feature\Api;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoControllerTest extends TestCase
{

    // データベースのリセット
    use RefreshDatabase;

    public function todoの登録APIの正常系を確認()
    {
        // APIを実行するユーザーを作成
        $user = User::factory()->create();

        // 送信データを定義
        $postData = [
            'title' => 'テスト用のTodoタイトル',
            'deadline_date' => '2024-12-31',
        ];

        // API実行
        $response = $this->actingAs($user)->json(
            'POST',
            route('api.todos.store'),
            $postData
        );

        // レスポンスのアサート
        $response->assertStatus(201)->assertJson([
            'message' => 'TODOが正常に作成されました',
            'data' => [
                'title' => $postData['title'],
                'deadline_date' => $postData['deadline_date'],
                'user_id' => $user->id,
                'completed_at' => null,
            ]
        ]);

        // DBアサート
        // todosテーブルにデータ登録ができていることを確認
        $this->assertDatabaseHas('todos', [
            'title' => $postData['title'],
            'deadline_date' => $postData['deadline_date'],
            'user_id' => $user->id,
            'completed_at' => null,
        ]);
    }

    public function todoの登録APIで期限なしの正常系を確認()
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
            route('api.todos.store'),
            $postData
        );

        // レスポンスのアサート
        $response->assertStatus(201)->assertJson([
            'message' => 'TODOが正常に作成されました',
            'data' => [
                'title' => $postData['title'],
                'deadline_date' => null,
                'user_id' => $user->id,
                'completed_at' => null,
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

    public function todoの登録APIでバリデーションエラーを確認()
    {
        // APIを実行するユーザーを作成
        $user = User::factory()->create();

        // 送信データを定義（タイトルなし）
        $postData = [
            'deadline_date' => '2024-12-31',
        ];

        // API実行
        $response = $this->actingAs($user)->json(
            'POST',
            route('api.todos.store'),
            $postData
        );

        // レスポンスのアサート
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function todoの一覧取得APIの正常系を確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // テスト用のTodoを作成
        $todos = Todo::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        // API実行
        $response = $this->actingAs($user)->json(
            'GET',
            route('api.todos.index')
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Todo一覧を取得しました',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function todoの未完了一覧取得APIの正常系を確認()
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
            'GET',
            route('api.todos.uncompleted')
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'message' => '未完了のTodo一覧を取得しました',
            ])
            ->assertJsonCount(2, 'data');
    }

    public function todoの完了一覧取得APIの正常系を確認()
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
            'GET',
            route('api.todos.completed')
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'message' => '完了したTodo一覧を取得しました',
            ])
            ->assertJsonCount(2, 'data');
    }

    public function todoの更新APIの正常系を確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // テスト用のTodoを作成
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'title' => '元のタイトル',
            'deadline_date' => '2024-01-01'
        ]);

        // 更新データを定義
        $updateData = [
            'title' => '更新されたタイトル',
            'deadline_date' => '2024-12-31',
        ];

        // API実行
        $response = $this->actingAs($user)->json(
            'PUT',
            route('api.todos.update', $todo->id),
            $updateData
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Todoを更新しました',
                'data' => [
                    'id' => $todo->id,
                    'title' => $updateData['title'],
                    'deadline_date' => $updateData['deadline_date'],
                ]
            ]);

        // DBアサート
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => $updateData['title'],
            'deadline_date' => $updateData['deadline_date'],
        ]);
    }

    public function todoの完了APIの正常系を確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // テスト用のTodoを作成
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed_at' => null
        ]);

        // API実行
        $response = $this->actingAs($user)->json(
            'PATCH',
            route('api.todos.completed', $todo->id)
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Todoを完了にしました',
            ]);

        // DBアサート
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function todoの未完了APIの正常系を確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // テスト用のTodoを作成（完了済み）
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now()
        ]);

        // API実行
        $response = $this->actingAs($user)->json(
            'PATCH',
            route('api.todos.uncompleted', $todo->id)
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Todoを未完了にしました',
            ]);

        // DBアサート
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed_at' => null,
        ]);
    }

    public function todoの削除APIの正常系を確認()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // テスト用のTodoを作成
        $todo = Todo::factory()->create([
            'user_id' => $user->id
        ]);

        // API実行
        $response = $this->actingAs($user)->json(
            'DELETE',
            route('api.todos.destroy', $todo->id)
        );

        // レスポンスのアサート
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Todoが正常に削除されました',
            ]);

        // DBアサート
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }

    public function 他のユーザーのtodoにアクセスできないことを確認()
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
            route('api.todos.index')
        );

        // レスポンスをアサート（user1のTodoは0件）
        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function 認証なしでAPIにアクセスできないことを確認()
    {
        // 認証なしでAPI実行
        $response = $this->json('GET', route('api.todos.index'));

        // レスポンスのアサート
        $response->assertStatus(401);
    }
}