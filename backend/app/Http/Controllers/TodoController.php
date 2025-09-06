<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;

class TodoController extends Controller
{
    /**
     * Todo登録API
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // バリデーションチェック
            $validated = $request->validate([
                'title' => 'required|string|max:100', // タイトルは100文字以内
                'deadline_date' => 'nullable|date|after_or_equal:today', // 期限は今日以降の日付
            ]);

            // 認証ユーザーのIDを追加
            $validated['user_id'] = $request->user()->id;

            // TODOを作成
            $todo = Todo::create($validated);

            // 作成されたTODOを返却
            return response()->json([
                'message' => 'TODOが正常に作成されました',
                'data' => $todo
            ], 201);

        } catch (\Exception $e) {
            // エラー時
            return response()->json([
                'message' => 'TODOの作成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
