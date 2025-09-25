<?php

namespace Tests\Unit;

use App\Models\Todo; // テスト対象のTodoモデル
use App\Models\User; // 関連するUserモデル
use Illuminate\Foundation\Testing\RefreshDatabase; // データベースのリセット
use Tests\TestCase; // テストクラス

class TodoTest extends TestCase
{
    // データベースのリセット
    use RefreshDatabase;

    /**
     * Todoモデルの基本作成テスト
     */
    public function test_todo_can_be_created(): void
    {
        // 準備
        $user = User::factory()->create();

        // 実行
        $result = Todo::create([
            'title' => 'テストタスク',
            'deadline_date' => '2024-12-31',
            'user_id' => $user->id,
        ]);

        // 検証（Todoモデルが作成されたことを期待）
        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals('テストタスク', $result->title);
        $this->assertEquals('2024-12-31', $result->deadline_date->format('Y-m-d'));
        $this->assertEquals($user->id, $result->user_id);
    }

    /**
     * fillableプロパティのテスト
     */
    public function test_todo_fillable_attributes(): void
    {
        // 準備
        $todo = new Todo();
        $expectedFillable = ['title', 'deadline_date', 'completed_at', 'user_id'];

        // 実行
        $result = $todo->getFillable();

        // 検証（fillableプロパティがあることを期待）
        $this->assertEquals($expectedFillable, $result);
    }

    /**
     * castsプロパティのテスト
     */
    public function test_todo_casts_attributes(): void
    {
        // 準備
        $todo = new Todo();

        // 実行
        $result = $todo->getCasts();

        // 検証（deadline_dateとcompleted_atがあることを期待）
        $this->assertArrayHasKey('deadline_date', $result);
        $this->assertArrayHasKey('completed_at', $result);
        $this->assertEquals('date', $result['deadline_date']);
        $this->assertEquals('datetime', $result['completed_at']);
    }

    /**
     * Userとのリレーションのテスト
     */
    public function test_todo_belongs_to_user(): void
    {
        // 準備
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // 実行
        $result = $todo->user;

        // 検証（Userとのリレーションがあることを期待）
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    /**
     * 完了状態のテスト
     */
    public function test_todo_completion(): void
    {
        // 準備
        $user = User::factory()->create();
        $todo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed_at' => null,
        ]);

        // 実行
        $isCompleted = $todo->completed_at !== null;

        // 検証
        $this->assertFalse($isCompleted);
        $this->assertNull($todo->completed_at);

        // 完了状態に変更
        $todo->update(['completed_at' => now()]);
        $todo->refresh();

        // 検証（完了状態になることを期待）
        $this->assertNotNull($todo->completed_at);
        $this->assertTrue($todo->completed_at !== null);
    }

    /**
     * バリデーションのテスト（モデルレベル）
     */
    public function test_todo_requires_title(): void
    {
        // 準備
        $user = User::factory()->create();
        $this->expectException(\Illuminate\Database\QueryException::class);

        // 実行（titleが未設定なので、エラーになることを期待）
        Todo::create([
            'deadline_date' => '2024-12-31',
            'user_id' => $user->id,
        ]);
    }
}
