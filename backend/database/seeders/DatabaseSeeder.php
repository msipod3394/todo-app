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

        // テストユーザー3を作成
        $testUser3 = User::factory()->create([
            'name' => 'テストユーザー3',
            'email' => 'test3@example.com',
            'password' => 'password123',
        ]);

        // テストユーザー4を作成
        $testUser4 = User::factory()->create([
            'name' => 'テストユーザー4',
            'email' => 'test4@example.com',
            'password' => 'password123',
        ]);

        // テストユーザー5を作成
        $testUser5 = User::factory()->create([
            'name' => 'テストユーザー5',
            'email' => 'test5@example.com',
            'password' => 'password123',
        ]);

        // すべてのユーザーをまとめる
        $allUsers = collect([$testUser1, $testUser2, $testUser3, $testUser4, $testUser5]);

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
