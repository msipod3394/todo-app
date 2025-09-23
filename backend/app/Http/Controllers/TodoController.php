<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TodoStoreRequest;
use App\Http\Requests\Api\TodoUpdateRequest;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;

use function Illuminate\Log\log;

class TodoController extends Controller
{
    /**
     * Todo登録API
     */
    public function store(TodoStoreRequest $request): JsonResponse
    {
        try {
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

    /**
     * Todoを完了にする
     */
    public function markCompleted(Request $request, int $id): JsonResponse{

        // ユーザーIDを取得
        $userId = $request->user()->id;

        // 完了にするtodoを取得
        $todo = Todo::where('user_id', $userId) // ユーザーIDで絞り込み
        ->findOrFail($id); // 指定されたIDのTodoを取得

        // todoが存在する場合
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
    public function markUncompleted(Request $request, int $id): JsonResponse{
        try{
            // ユーザーIDを取得
            $userId = $request->user()->id;

            // 完了にするtodoを取得
            $todo = Todo::where('user_id', $userId) // ユーザーIDで絞り込み
            ->find($id); // 指定されたIDのTodoを取得

            if(!$todo){
                // todoが存在しない場合
                return response()->json([
                    'message' => 'Todoが存在しません',
                ], 400);
            } else {
                // todoが存在する場合
                // 完了日時をnullに更新
                $todo->update(['completed_at' => null]);

                return response()->json([
                    'message' => 'Todoを未完了にしました',
                    'data' => $todo
                ]);
            }
         } catch(\Exception $e){
            return response()->json([
                'message' => 'Todoの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
         }
    }

    /**
     * Todoを削除する
     */
     public function destroy(Request $request, int $id): JsonResponse{
        try{
            // ユーザーIDを取得
            $userId = $request->user()->id;

            // todoを取得
            $todo = Todo::where('user_id', $userId) // ユーザーIDで絞り込み
            ->find($id); // 指定されたIDのTodoを取得

            if(!$todo){
                // todoが存在しない場合
                return response()->json([
                    'message' => 'Todoが存在しません',
                ], 400);
            } else {
                // todoが存在する場合
                // todoを削除
                $todo->delete();
                return response()->json([
                    'message' => 'Todoが正常に削除されました',
                ]);
            }
        } catch(\Exception $e){
            return response()->json([
                'message' => 'Todoの削除に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Todoを更新する
     */
    public function update(TodoUpdateRequest $request, int $id): JsonResponse{
        try{
            // バリデーション済みデータを取得
            $validated = $request->validated();

            // ユーザーIDを取得
            $userId = $request->user()->id;

            // 更新するtodoを取得
            $todo = Todo::where('user_id', $userId)
                ->find($id);

            if(!$todo){
                return response()->json([
                    'message' => 'Todoが存在しません',
                ], 404);
            }

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

         } catch(\Exception $e){
            return response()->json([
                'message' => 'Todoの更新に失敗しました',
                'error' => $e->getMessage()
            ], 500);
         }
    }

}
