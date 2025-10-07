<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 認証ルート
require __DIR__.'/auth.php';

// 認証が必要なAPI
Route::middleware(['auth:sanctum'])->group(function () {
    // ★この中のルートは認証が必要

    // 認証ユーザー情報取得
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 認証ユーザー情報取得
    Route::get('/me', [\App\Http\Controllers\UserController::class, 'me']);

    // ★カスタムルートを先に定義（apiResourceより前に定義する必要がある）
    // 未完了のTodo一覧取得
    Route::get('/todos/uncompleted', [\App\Http\Controllers\TodoController::class, 'uncompleted']);
    // 完了のTodo一覧取得
    Route::get('/todos/completed', [\App\Http\Controllers\TodoController::class, 'completed']);
    // 完了したTodoを一括削除
    Route::delete('/todos/completed', [\App\Http\Controllers\TodoController::class, 'deleteAllCompleted']);

    // Todoのリソースルート
    Route::apiResource('todos', \App\Http\Controllers\TodoController::class)
        ->except(['show']);

    // Todoを完了にする
    Route::patch('/todos/{id}/completed', [\App\Http\Controllers\TodoController::class, 'markCompleted']);

    // Todoを未完了にする
    Route::patch('/todos/{id}/uncompleted', [\App\Http\Controllers\TodoController::class, 'markUncompleted']);
});