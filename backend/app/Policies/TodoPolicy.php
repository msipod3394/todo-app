<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;

class TodoPolicy
{
    /**
     * ユーザーがTodoを閲覧できるかチェック
     */
    public function view(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    /**
     * ユーザーがTodoを更新できるかチェック
     */
    public function update(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    /**
     * ユーザーがTodoを削除できるかチェック
     */
    public function delete(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    /**
     * ユーザーがTodoを作成できるかチェック
     */
    public function create(User $user): bool
    {
        return true; // 認証済みユーザーは作成可能
    }
}
