<?php

namespace Tests\Unit;

use App\Models\Todo; // テスト対象のTodoモデル
use App\Models\User; // 関連するUserモデル
use App\Policies\TodoPolicy; // ポリシークラス
use Illuminate\Foundation\Testing\RefreshDatabase; // データベースのリセット
use Tests\TestCase; // テストクラス

class TodoPolicyTest extends TestCase
{
    // データベースのリセット
    use RefreshDatabase;

    private TodoPolicy $policy;

    // テストに必要なTodoPolicyインスタンスを作成
    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new TodoPolicy();
    }

    /**
     * ユーザーが自分のTodoを閲覧できることをテスト
     */
    public function test_user_can_view_own_todo(): void
    {
        // 準備
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // 実行
        $result = $this->policy->view($user, $todo);

        // 検証（trueになることを確認）
        $this->assertTrue($result);
    }

    /**
     * ユーザーが他人のTodoを閲覧できないことをテスト
     */
    public function test_user_cannot_view_other_users_todo(): void
    {
        // 準備
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user2->id]);

        // 実行
        $result = $this->policy->view($user1, $todo);

        // 検証（falseになることを確認）
        $this->assertFalse($result);
    }

    /**
     * ユーザーが自分のTodoを更新できることをテスト
     */
    public function test_user_can_update_own_todo(): void
    {
        // 準備
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // 実行
        $result = $this->policy->update($user, $todo);

        // 検証（trueになることを確認）
        $this->assertTrue($result);
    }

    /**
     * ユーザーが他人のTodoを更新できないことをテスト
     */
    public function test_user_cannot_update_other_users_todo(): void
    {
        // 準備
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user2->id]);

        // 実行
        $result = $this->policy->update($user1, $todo);

        // 検証（falseになることを確認）
        $this->assertFalse($result);
    }

    /**
     * ユーザーが自分のTodoを削除できることをテスト
     */
    public function test_user_can_delete_own_todo(): void
    {
        // 準備
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // 実行
        $result = $this->policy->delete($user, $todo);

        // 検証（trueになることを確認）
        $this->assertTrue($result);
    }

    /**
     * ユーザーが他人のTodoを削除できないことをテスト
     */
    public function test_user_cannot_delete_other_users_todo(): void
    {
        // 準備
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user2->id]);

        // 実行
        $result = $this->policy->delete($user1, $todo);

        // 検証（falseになることを確認）
        $this->assertFalse($result);
    }

    /**
     * 認証済みユーザーがTodoを作成できることをテスト
     */
    public function test_authenticated_user_can_create_todo(): void
    {
        // 準備
        $user = User::factory()->create();

        // 実行
        $result = $this->policy->create($user);

        // 検証（trueになることを確認）
        $this->assertTrue($result);
    }
}
