<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * ユーザー情報取得
     */
    public function me(Request $request) :JsonResponse
    {
        // ユーザー情報を取得
        $user = $request->user();

        // 認証されていない場合
        if (!$user) {
            return response()->json([
                'message' => '認証が必要です',
                'error' => 'Unauthenticated'
            ], 401);
        }

        // ユーザー情報を返却
        return response()->json([
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "created_at" => $user->created_at,
            ]
        ], 200);
    }
}
