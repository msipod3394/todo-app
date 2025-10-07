import { defineStore } from "pinia";
import { ref, computed } from "vue";
import type { AuthFormData } from "@/lib/validation";
import {
  API_ENDPOINTS,
  createAuthenticatedRequest,
  handleApiError,
} from "@/lib/api";

interface User {
  id: number;
  name: string;
  email: string;
  created_at: string;
}

interface SigninResponse {
  message: string;
  user: User;
  token: string;
}

interface SignupResponse {
  message: string;
  user: User;
  token: string;
}

export const useAuthStore = defineStore("auth", () => {
  // State
  const currentUser = ref<User | null>(null);
  const token = ref<string | null>(null);
  const isLoading = ref<boolean>(false);
  const errors = ref<string[]>([]);

  // Getters
  const isAuthenticated = computed(() => !!(currentUser.value && token.value));
  const userName = computed(() => currentUser.value?.name || "ゲスト");
  const hasErrors = computed(() => errors.value.length > 0);

  // Actions
  const signin = async (
    formData: AuthFormData
  ): Promise<{ success: boolean; message?: string }> => {
    isLoading.value = true;
    errors.value = [];

    try {
      // バックエンドAPI呼び出し
      const response = await fetch(API_ENDPOINTS.SIGNIN, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          email: formData.email,
          password: formData.password,
        }),
      });

      if (response.ok) {
        const data: SigninResponse = await response.json();

        // ストア更新
        currentUser.value = data.user;
        token.value = data.token;

        // ローカルストレージ保存
        localStorage.setItem("authToken", data.token);
        localStorage.setItem("authUser", JSON.stringify(data.user));

        return { success: true, message: data.message };
      } else {
        const errorMessages = await handleApiError(response);
        errors.value = errorMessages;
        return { success: false, message: errorMessages.join(" ") };
      }
    } catch (error) {
      const errorMessage = "ネットワークエラーが発生しました。";
      errors.value = [errorMessage];
      console.error("ログインエラー:", error);
      return { success: false, message: errorMessage };
    } finally {
      isLoading.value = false;
    }
  };

  const signup = async (
    formData: AuthFormData
  ): Promise<{ success: boolean; message?: string }> => {
    isLoading.value = true;
    errors.value = [];

    try {
      // バックエンドAPI呼び出し
      const response = await fetch(API_ENDPOINTS.SIGNUP, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          email: formData.email,
          password: formData.password,
        }),
      });

      if (response.ok) {
        const data: SignupResponse = await response.json();

        // ストア更新
        currentUser.value = data.user;
        token.value = data.token;

        // ローカルストレージ保存
        localStorage.setItem("authToken", data.token);
        localStorage.setItem("authUser", JSON.stringify(data.user));

        return { success: true, message: data.message };
      } else {
        const errorMessages = await handleApiError(response);
        errors.value = errorMessages;
        return { success: false, message: errorMessages.join(" ") };
      }
    } catch (error) {
      const errorMessage = "ネットワークエラーが発生しました。";
      errors.value = [errorMessage];
      console.error("ユーザー登録エラー:", error);
      return { success: false, message: errorMessage };
    } finally {
      isLoading.value = false;
    }
  };

  const logout = async (): Promise<{ success: boolean; message?: string }> => {
    try {
      // バックエンドAPI呼び出し
      const authToken = token.value || localStorage.getItem("authToken");

      if (authToken) {
        await fetch(API_ENDPOINTS.SIGNOUT, {
          method: "POST",
          ...createAuthenticatedRequest(authToken),
        });
      }

      // ストアリセット
      currentUser.value = null;
      token.value = null;
      errors.value = [];

      // ローカルストレージクリア
      localStorage.removeItem("authToken");
      localStorage.removeItem("authUser");

      return { success: true, message: "ログアウトしました。" };
    } catch (error) {
      console.error("ログアウトエラー:", error);

      // エラーが発生してもローカルの状態はクリア
      currentUser.value = null;
      token.value = null;
      errors.value = [];
      localStorage.removeItem("authToken");
      localStorage.removeItem("authUser");

      return {
        success: false,
        message:
          "ログアウト処理でエラーが発生しましたが、ローカル状態はクリアされました。",
      };
    }
  };

  const initializeAuth = (): void => {
    // ページリロード時に認証状態を復元
    const storedUser = localStorage.getItem("authUser");
    const storedToken = localStorage.getItem("authToken");

    if (storedUser && storedToken) {
      try {
        currentUser.value = JSON.parse(storedUser);
        token.value = storedToken;
      } catch (error) {
        console.error("認証状態の復元に失敗:", error);
        localStorage.removeItem("authUser");
        localStorage.removeItem("authToken");
      }
    }
  };

  const clearErrors = (): void => {
    errors.value = [];
  };

  return {
    // State
    currentUser,
    token,
    isLoading,
    errors,

    // Getters
    isAuthenticated,
    userName,
    hasErrors,

    // Actions
    signin,
    signup,
    logout,
    initializeAuth,
    clearErrors,
  };
});
