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
     * TODOの作成
     */
    public function test_Todoを作成できる(): void
    {
        // 準備
        $user = User::factory()->create();

        // 実行
        $result = Todo::create([
            'title' => 'テストタスク',
            'deadline_date' => '2025-12-31',
            'user_id' => $user->id,
        ]);

        // 検証（Todoが作成されたか）
        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals('テストタスク', $result->title);
        $this->assertEquals('2025-12-31', $result->deadline_date->format('Y-m-d'));
        $this->assertEquals($user->id, $result->user_id);
    }

    /**
     * 一括代入で更新を許可したプロパティだけが更新される（fillableプロパティ）
     */
    public function test_一括代入で更新を許可したプロパティだけが更新される(): void
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
     * 日付が適切なフォーマットで保存される
     */
    public function test_日付が適切なフォーマットで保存される(): void
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
     * Userとリレーションされているか
     */
    public function test_Userとリレーションされているか(): void
    {
        // 準備
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // 実行
        $result = $todo->user;

        // 検証（Userとリレーションがあることを期待）
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    /**
     * 完了で登録されるか
     */
    public function test_完了で登録されるか(): void
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
     * バリデーションがされているか
     */
    public function test_バリデーションがされているか(): void
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
