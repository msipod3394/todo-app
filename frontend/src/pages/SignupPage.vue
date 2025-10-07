<template>
  <div>
    <!-- 登録完了モーダル -->
    <div
      v-if="showSuccessModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="text-center space-y-4">
          <h2 class="text-lg font-semibold text-slate-900">
            新規ユーザー登録完了
          </h2>
          <p class="text-sm text-black">ユーザー登録が完了しました。</p>
          <Button
            @click="redirectToSignin"
            class="w-full bg-slate-900 text-white hover:bg-slate-800"
          >
            ログインする
          </Button>
        </div>
      </div>
    </div>

    <!-- 登録フォーム -->
    <AuthForm
      v-if="!showSuccessModal"
      :is-signup="true"
      @submit="handleSignup"
    />

    <!-- エラーメッセージ表示 -->
    <div
      v-if="authStore.hasErrors && !showSuccessModal"
      class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-sm"
    >
      <div v-for="error in authStore.errors" :key="error">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import AuthForm from "@/components/AuthForm.vue";
import { Button } from "@/components/ui/button";
import { useAuthStore } from "@/stores/useAuthStore";
import type { AuthFormData } from "@/lib/validation";

const router = useRouter();
const authStore = useAuthStore();
const showSuccessModal = ref<boolean>(false);

const handleSignup = async (formData: AuthFormData): Promise<void> => {
  const result = await authStore.signup(formData);

  if (result.success) {
    // 登録成功時は完了モーダルを表示
    showSuccessModal.value = true;
  }
  // エラーの場合はストア内でエラーメッセージがセットされる
};

const redirectToSignin = (): void => {
  router.push("/signin");
};

// コンポーネント初期化時にエラーをクリア
onMounted(() => {
  authStore.clearErrors();
});
</script>
