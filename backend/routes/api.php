<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 認証ルート
require __DIR__.'/auth.php';

// 認証が必要なAPI
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Todoのルート
    Route::apiResource('todos', \App\Http\Controllers\TodoController::class);

    // Todo一覧取得（未完了）
    Route::get('/todos/uncompleted', [\App\Http\Controllers\TodoController::class, 'uncompleted']);

    // Todo一覧取得（完了）
    Route::get('/todos/completed', [\App\Http\Controllers\TodoController::class, 'completed']);

});