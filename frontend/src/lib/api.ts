// API設定とヘルパー関数

const API_BASE_URL = "http://127.0.0.1:8000/api";

// 認証付きAPIリクエストのヘルパー
export const createAuthenticatedRequest = (token: string) => {
  return {
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  };
};

// APIエンドポイント
export const API_ENDPOINTS = {
  SIGNUP: `${API_BASE_URL}/signup`,
  SIGNIN: `${API_BASE_URL}/signin`,
  SIGNOUT: `${API_BASE_URL}/signout`,
  ME: `${API_BASE_URL}/me`,
  TODOS: `${API_BASE_URL}/todos`,
} as const;

// エラーレスポンスの型定義
export interface ApiErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
}

// APIエラーハンドリング
export const handleApiError = async (response: Response): Promise<string[]> => {
  try {
    const errorData: ApiErrorResponse = await response.json();

    if (errorData.errors) {
      // バリデーションエラーの場合
      const messages: string[] = [];
      Object.values(errorData.errors).forEach((errorArray) => {
        if (Array.isArray(errorArray)) {
          messages.push(...errorArray);
        }
      });
      return messages;
    } else if (errorData.message) {
      return [errorData.message];
    }
  } catch (e) {
    console.error("Error parsing API response:", e);
  }

  // デフォルトエラーメッセージ
  return ["サーバーエラーが発生しました。"];
};

// TODO登録API
export const createTodo = async (
  token: string,
  todoData: {
    title: string;
    deadline_date?: string | null;
  }
) => {
  // APIリクエスト
  const response = await fetch(API_ENDPOINTS.TODOS, {
    method: "POST",
    headers: createAuthenticatedRequest(token).headers,
    body: JSON.stringify(todoData),
  });

  // 成功時
  if (!response.ok) {
    throw new Error("Todo作成に失敗しました");
  }

  return await response.json();
};

// TODO一覧取得API
export const fetchTodos = async (token: string) => {
  // リクエストを送信
  const res = await fetch(API_ENDPOINTS.TODOS, {
    method: "GET",
    headers: createAuthenticatedRequest(token).headers,
  });

  // エラー時
  if (!res.ok) {
    throw new Error("Todo一覧取得に失敗しました");
  }

  // レスポンスを返却
  return await res.json();
};


