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

// TODO削除API
export const deleteTodo = async (token: string, id: number) => {
  // リクエストを送信
  const res = await fetch(API_ENDPOINTS.TODOS + "/" + id, {
    method: "DELETE",
    headers: createAuthenticatedRequest(token).headers,
  });

  // エラー時
  if (!res.ok) {
    throw new Error("Todo削除に失敗しました");
  }

  // レスポンスを返却
  return await res.json();
};

// TODO完了API
export const markTodoCompleted = async (token: string, id: number) => {
  const res = await fetch(`${API_ENDPOINTS.TODOS}/${id}/completed`, {
    method: "PATCH",
    headers: createAuthenticatedRequest(token).headers,
  });

  if (!res.ok) {
    throw new Error("Todoを完了に変更する処理に失敗しました");
  }

  return await res.json();
};

// TODO未完了API
export const markTodoUncompleted = async (token: string, id: number) => {
  const res = await fetch(`${API_ENDPOINTS.TODOS}/${id}/uncompleted`, {
    method: "PATCH",
    headers: createAuthenticatedRequest(token).headers,
  });

  if (!res.ok) {
    throw new Error("Todo未完了状態の更新に失敗しました");
  }

  return await res.json();
};

// TODO編集API
export const updateTodo = async (
  token: string,
  id: number,
  todoData: {
    title: string;
    deadline_date?: string | null;
  }
) => {
  console.log("Todo更新API呼び出し:", { id, todoData });

  const res = await fetch(`${API_ENDPOINTS.TODOS}/${id}`, {
    method: "PATCH",
    headers: createAuthenticatedRequest(token).headers,
    body: JSON.stringify(todoData),
  });

  console.log("Todo更新APIレスポンス:", { status: res.status, ok: res.ok });

  // エラー時
  if (!res.ok) {
    const errorText = await res.text();
    console.error("Todo更新APIエラー:", errorText);
    throw new Error("Todoの編集に失敗しました");
  }

  // レスポンスを返却
  return await res.json();
};

// 完了したTodo一括削除API
export const deleteAllCompletedTodos = async (token: string) => {
  console.log("API呼び出し: DELETE /api/todos/completed");
  console.log("認証トークン:", token ? "あり" : "なし");

  const res = await fetch(`${API_ENDPOINTS.TODOS}/completed`, {
    method: "DELETE",
    headers: createAuthenticatedRequest(token).headers,
  });

  console.log("APIレスポンスステータス:", res.status);
  console.log("APIレスポンスOK:", res.ok);

  // エラー時
  if (!res.ok) {
    const errorText = await res.text();
    console.error("APIエラーレスポンス:", errorText);
    throw new Error("完了したTodoの一括削除に失敗しました");
  }

  // レスポンスを返却
  const responseData = await res.json();
  console.log("APIレスポンスデータ:", responseData);
  return responseData;
};
