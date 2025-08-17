import { defineStore } from "pinia";
import { ref, computed } from "vue";
import type { AuthFormData } from "@/lib/validation";

interface User {
  id: number;
  email: string;
  createdAt: string;
}

export const useAuthStore = defineStore("auth", () => {
  // State
  const currentUser = ref<User | null>(null);
  const isLoading = ref<boolean>(false);
  const isAuthenticated = computed(() => !!currentUser.value);

  // Actions
  const login = async (formData: AuthFormData): Promise<boolean> => {
    isLoading.value = true;

    try {
      // バリデーション通過時は即座にログイン成功（開発用）
      // 実際のAPI呼び出しをシミュレート
      await new Promise((resolve) => setTimeout(resolve, 500));

      // バリデーションが通ったメールアドレスでユーザーを作成
      currentUser.value = {
        id: Date.now(),
        email: formData.email,
        createdAt: new Date().toISOString(),
      };

      // ローカルストレージに保存
      localStorage.setItem("authUser", JSON.stringify(currentUser.value));

      return true;

      /* TODO: 後で実際の認証APIに置き換え予定
      // 実際のAPI呼び出し
      const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      });

      if (!response.ok) {
        throw new Error('認証に失敗しました。');
      }

      const userData = await response.json();
      currentUser.value = userData.user;
      
      // JWTトークンをローカルストレージに保存
      localStorage.setItem('authToken', userData.token);
      localStorage.setItem("authUser", JSON.stringify(userData.user));

      return true;
      */
    } catch (error) {
      console.error("ログインエラー:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const register = async (formData: AuthFormData): Promise<boolean> => {
    isLoading.value = true;

    try {
      // バリデーション通過時は即座に登録成功（開発用）
      // 実際のAPI呼び出しをシミュレート
      await new Promise((resolve) => setTimeout(resolve, 500));

      // バリデーションが通ったメールアドレスで新規ユーザーを作成
      const newUser: User = {
        id: Date.now(),
        email: formData.email,
        createdAt: new Date().toISOString(),
      };

      currentUser.value = newUser;

      // ローカルストレージに保存
      localStorage.setItem("authUser", JSON.stringify(currentUser.value));

      return true;

      /* TODO: 後で実際の新規登録APIに置き換え予定
      // 実際のAPI呼び出し
      const response = await fetch('/api/auth/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || '新規登録に失敗しました。');
      }

      const userData = await response.json();
      currentUser.value = userData.user;
      
      // JWTトークンをローカルストレージに保存
      localStorage.setItem('authToken', userData.token);
      localStorage.setItem("authUser", JSON.stringify(userData.user));

      return true;
      */
    } catch (error) {
      console.error("新規登録エラー:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const logout = async (): Promise<void> => {
    try {
      // 開発用: 即座にログアウト
      currentUser.value = null;
      localStorage.removeItem("authUser");

      /* TODO: 後で実際のログアウトAPIに置き換え予定
      // 実際のAPI呼び出し
      const token = localStorage.getItem('authToken');
      if (token) {
        await fetch('/api/auth/logout', {
          method: 'POST',
          headers: { 
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        });
      }
      
      localStorage.removeItem('authToken');
      localStorage.removeItem("authUser");
      */
    } catch (error) {
      console.error("ログアウトエラー:", error);
      throw error;
    }
  };

  const initializeAuth = (): void => {
    // ページリロード時に認証状態を復元
    const storedUser = localStorage.getItem("authUser");
    if (storedUser) {
      try {
        currentUser.value = JSON.parse(storedUser);
      } catch (error) {
        console.error("認証状態の復元に失敗:", error);
        localStorage.removeItem("authUser");
      }
    }
  };

  return {
    // State
    currentUser,
    isLoading,
    isAuthenticated,

    // Actions
    login,
    register,
    logout,
    initializeAuth,
  };
});
