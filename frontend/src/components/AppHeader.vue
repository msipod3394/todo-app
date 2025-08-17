<template>
  <header class="bg-slate-900 text-white">
    <div class="px-10 py-4 flex justify-end">
      <div class="flex items-center gap-4">
        <!-- User Info -->
        <div class="flex items-center gap-1">
          <span class="text-sm font-light">{{ userEmail }}</span>
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
import { Button } from "@/components/ui/button";

interface Props {
  userEmail?: string;
}

const props = withDefaults(defineProps<Props>(), {
  userEmail: "test@sample.com",
});

const emit = defineEmits<{
  logout: [];
}>();

const handleLogout = (): void => {
  emit("logout");
};
</script>
