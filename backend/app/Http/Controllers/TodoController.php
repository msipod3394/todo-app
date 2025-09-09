<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;

use function Illuminate\Log\log;

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

    /**
     * Todo一覧取得（全件）
     */
    public function index(Request $request): JsonResponse{
        try{
            // ログ出力：どのメソッドが呼ばれたか確認
            \Log::info('index() メソッドが呼ばれました');

            // ユーザーIDを取得
            $userId = $request->user()->id;

            // todo取得
            $todos = Todo::where('user_id', $userId) // ユーザーIDで絞り込み
            ->orderBy('created_at', 'desc') // 作成日時で降順
            ->get();

            // レスポンス
            return response()->json([
                'message' => 'Todo一覧を取得しました',
                'data' => $todos
            ]);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Todo一覧の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Todo一覧取得（未完了）
     */
    public function uncompleted(Request $request): JsonResponse{
        try{
            // ログ出力：どのメソッドが呼ばれたか確認
            \Log::info('uncompleted() メソッドが呼ばれました');

            // ユーザーIDを取得
            $userId = $request->user()->id;

            // todo取得
            $todos = Todo::where('user_id', $userId) // ユーザーIDで絞り込み
            ->whereNull('completed_at') // 完了日時がnull
            ->orderBy('created_at', 'desc') // 作成日時で降順
            ->get();

            // レスポンス
            return response()->json([
                'message' => '未完了のTodo一覧を取得しました',
                'data' => $todos
            ]);

        } catch(\Exception $e){
            return response()->json([
                'message' => '未完了のTodo一覧の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Todo一覧取得（完了）
     */
    public function completed(Request $request): JsonResponse{
        try{
            // ユーザーIDを取得
            $userId = $request->user()->id;

            // todo取得
            $todos = Todo::where('user_id', $userId) // ユーザーIDで絞り込み
            ->whereNotNull('completed_at') // 完了日時がnullではない
            ->orderBy('created_at', 'desc') // 作成日時で降順
            ->get();

            // レスポンス
            return response()->json([
                'message' => '完了したTodo一覧を取得しました',
                'data' => $todos
            ]);

        } catch(\Exception $e){
            return response()->json([
                'message' => '完了したTodo一覧の取得に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}