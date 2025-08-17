<template>
  <div class="bg-gray-100 p-3 rounded-lg space-y-2">
    <div class="text-sm text-gray-600">
      進捗: {{ completedCount }}/{{ totalCount }} 完了
    </div>
    <Progress :model-value="progressPercentage" class="h-2" />
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { Progress } from "@/components/ui/progress";

interface Props {
  completedCount: number;
  totalCount: number;
}

const props = defineProps<Props>();

const progressPercentage = computed((): number => {
  if (props.totalCount === 0) return 0;
  return Math.round((props.completedCount / props.totalCount) * 100);
});
</script>
