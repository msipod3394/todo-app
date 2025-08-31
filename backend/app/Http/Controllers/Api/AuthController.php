<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        // バリデーション実行
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ], [
            // カスタムエラーメッセージ（日本語）
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => '正しいメールアドレス形式で入力してください。',
            'email.unique' => 'このメールアドレスは既に使用されています。',
            'password.required' => 'パスワードは必須です。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
        ]);

        // ユーザー作成（パスワードは自動でハッシュ化される）
        $user = User::create([
            'name' => $validatedData['email'], // nameフィールドにはemailを使用
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // 認証トークン生成
        $token = $user->createToken('auth_token')->plainTextToken;

        // 成功レスポンス
        return response()->json([
            'message' => 'ユーザー登録が完了しました。',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
            'token' => $token,
        ], 201);
    }
}