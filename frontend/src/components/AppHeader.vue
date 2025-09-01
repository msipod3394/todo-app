<template>
  <header class="bg-slate-900 text-white">
    <div class="px-10 py-4 flex justify-end">
      <div class="flex items-center gap-4">
        <!-- User Info -->
        <div class="flex items-center gap-1">
          <span class="text-sm font-light">{{ authStore.userName }}</span>
          <span class="text-sm font-light">さん</span>
        </div>

        <!-- Logout Button -->
        <Button
          variant="outline"
          size="sm"
          class="border-slate-300 text-slate-900 hover:bg-slate-100"
          @click="handleLogout"
        >
          ログアウト
        </Button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import { Button } from "@/components/ui/button";
import { useAuthStore } from "@/stores/useAuthStore";

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = async (): Promise<void> => {
  const result = await authStore.logout();
  
  if (result.success) {
    // ログアウト成功時はログインページにリダイレクト
    router.push("/login");
  }
  // エラーが発生してもローカル状態はクリアされるため、ログインページにリダイレクト
  else {
    router.push("/login");
  }
};
</script>
