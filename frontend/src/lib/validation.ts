import { z } from "zod";

// 認証フォーム用のバリデーションスキーマ
export const authSchema = z.object({
  email: z
    .string({ required_error: "メールアドレスを入力してください。" })
    .min(1, "メールアドレスを入力してください。")
    .email("有効な形式で入力してください。"),
  password: z
    .string({ required_error: "パスワードを入力してください。" })
    .min(1, "パスワードを入力してください。")
    .min(8, "パスワードは8文字以上で入力してください。"),
});

// 新規登録用（将来的に確認パスワードなどを追加予定）
export const registerSchema = authSchema;

// ログイン用
export const loginSchema = authSchema;

// 型定義
export type AuthFormData = z.infer<typeof authSchema>;
export type RegisterFormData = z.infer<typeof registerSchema>;
export type LoginFormData = z.infer<typeof loginSchema>;
