<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Todo;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // テストユーザー1を作成
        $testUser1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@example.com',
            'password' => 'password123',
        ]);

        // テストユーザー2を作成
        $testUser2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@example.com',
            'password' => 'password123',
        ]);

        // 残りの3つのユーザーを作成
        $users = User::factory()
            ->count(3)
            ->create();

        // すべてのユーザーをまとめる
        $allUsers = collect([$testUser1, $testUser2])->merge($users);

        // Todo1: テストユーザー1に関連付け
        Todo::factory()->create([
            'title' => 'タスク1',
            'deadline_date' => now()->addDays(7),
            'user_id' => $testUser1->id,
        ]);

        // Todo2: テストユーザー2に関連付け
        Todo::factory()->create([
            'title' => 'タスク2',
            'deadline_date' => now()->addDays(14),
            'user_id' => $testUser2->id,
        ]);

        // 残りの3つのTodoをランダムなユーザーに関連付け
        Todo::factory()
            ->count(3)
            ->recycle($allUsers)
            ->create();
    }
}
