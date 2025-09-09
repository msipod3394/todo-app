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

    // カスタムルートを先に定義
    Route::get('/todos/uncompleted', [\App\Http\Controllers\TodoController::class, 'uncompleted']);
    Route::get('/todos/completed', [\App\Http\Controllers\TodoController::class, 'completed']);

    // Todoのリソースルート
    Route::apiResource('todos', \App\Http\Controllers\TodoController::class)
        ->except(['show']);

});
