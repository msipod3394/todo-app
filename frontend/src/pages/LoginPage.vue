<template>
  <div>
    <AuthForm :is-register="false" @submit="handleLogin" />
    
    <!-- エラーメッセージ表示 -->
    <div v-if="authStore.hasErrors" class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-sm">
      <div v-for="error in authStore.errors" :key="error">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import { onMounted } from "vue";
import AuthForm from "@/components/AuthForm.vue";
import { useAuthStore } from "@/stores/useAuthStore";
import type { AuthFormData } from "@/lib/validation";

const router = useRouter();
const authStore = useAuthStore();

const handleLogin = async (formData: AuthFormData): Promise<void> => {
  const result = await authStore.login(formData);
  
  if (result.success) {
    // ログイン成功時はTodoページにリダイレクト
    router.push("/");
  }
  // エラーの場合はストア内でエラーメッセージがセットされ、テンプレートで表示される
};

// コンポーネント初期化時にエラーをクリア
onMounted(() => {
  authStore.clearErrors();
});
</script>
