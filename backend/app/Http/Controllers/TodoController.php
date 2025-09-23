<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TodoStoreRequest;
use App\Http\Requests\Api\TodoUpdateRequest;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;


class TodoController extends Controller
{
    /**
     * Todo登録API
     */
    public function store(TodoStoreRequest $request): JsonResponse
    {
        // バリデーション済みデータを取得
        $validated = $request->validated();

        // 認証ユーザーのIDを追加
        $validated['user_id'] = $request->user()->id;

        // TODOを作成
        $todo = Todo::create($validated);

        // 作成されたTODOを返却
        return response()->json([
            'message' => 'TODOが正常に作成されました',
            'data' => $todo
        ], 201);
    }

    /**
     * Todo一覧取得（全件）
     */
    public function index(Request $request): JsonResponse{
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
    }

    /**
     * Todo一覧取得（未完了）
     */
    public function uncompleted(Request $request): JsonResponse{
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
    }

    /**
     * Todo一覧取得（完了）
     */
    public function completed(Request $request): JsonResponse{
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
    }

    /**
     * Todoを完了にする
     */
    public function markCompleted(Request $request, int $id): JsonResponse
    {
        // Todoを取得
        $todo = Todo::findOrFail($id);

        // 認可チェック
        $this->authorize('update', $todo);

        // 完了日時を更新
        $todo->update(['completed_at' => now()]);

        return response()->json([
            'message' => 'Todoを完了にしました',
            'data' => $todo
        ]);
    }

    /**
     * Todoを未完了にする
     */
    public function markUncompleted(Request $request, int $id): JsonResponse
    {
        // Todoを取得
        $todo = Todo::findOrFail($id);

        // 認可チェック
        $this->authorize('update', $todo);

        // 完了日時をnullに更新
        $todo->update(['completed_at' => null]);

        return response()->json([
            'message' => 'Todoを未完了にしました',
            'data' => $todo
        ]);
    }

    /**
     * Todoを削除する
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        // Todoを取得
        $todo = Todo::findOrFail($id);

        // 認可チェック
        $this->authorize('delete', $todo);

        // todoを削除
        $todo->delete();

        return response()->json([
            'message' => 'Todoが正常に削除されました',
        ]);
    }

    /**
     * Todoを更新する
     */
    public function update(TodoUpdateRequest $request, int $id): JsonResponse
    {
        // バリデーション済みデータを取得
        $validated = $request->validated();

        // Todoを取得
        $todo = Todo::findOrFail($id);

        // 認可チェック
        $this->authorize('update', $todo);

        // todoを更新
        $todo->update([
            'title' => $validated['title'],
            'deadline_date' => $validated['deadline_date'],
        ]);

        // 更新後のデータを取得
        $todo->refresh();

        return response()->json([
            'message' => 'Todoを更新しました',
            'data' => $todo
        ]);
    }

}