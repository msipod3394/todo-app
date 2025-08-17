<template>
  <AuthForm :is-register="false" @submit="handleLogin" />
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import AuthForm from "@/components/AuthForm.vue";
import { useAuthStore } from "@/stores/useAuthStore";
import type { AuthFormData } from "@/lib/validation";

const router = useRouter();
const authStore = useAuthStore();

const handleLogin = async (formData: AuthFormData): Promise<void> => {
  try {
    const success = await authStore.login(formData);
    if (success) {
      // ログイン成功時はTodoページにリダイレクト
      router.push("/");
    }
  } catch (error) {
    console.error("ログインに失敗しました:", error);
    // エラーハンドリング（実際のプロジェクトではトーストやエラーメッセージ表示）
  }
};
</script>
