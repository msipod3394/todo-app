<template>
  <div class="space-y-4">
    <form @submit.prevent="handleSubmit" class="flex gap-2">
      <div class="flex-1 relative">
        <!-- Todoの入力 -->
        <Input
          v-model="taskName"
          placeholder="新しいタスクを入力..."
          class="h-10"
          :class="{ 'border-red-500': errors.name }"
        />
        <div v-if="errors.name" class="text-red-500 text-xs mt-1">
          {{ errors.name }}
        </div>
      </div>
      <!-- 期限の入力 -->
      <Popover v-model:open="isCalendarOpen">
        <PopoverTrigger as-child>
          <Button variant="outline" size="icon" class="h-10 w-10" type="button">
            <CalendarIcon class="h-4 w-4" />
          </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0" align="start">
          <Calendar
            v-model="selectedDate"
            @update:model-value="handleDateSelect"
          />
        </PopoverContent>
      </Popover>
      <!-- 追加ボタン -->
      <Button
        type="submit"
        class="h-10 px-4"
        :disabled="!taskName.trim() || isSubmitting"
        :class="{ 'opacity-50': !taskName.trim() }"
      >
        <PlusIcon class="h-4 w-4" />
      </Button>
    </form>
    <!-- 選択した期限の表示 -->
    <div v-if="selectedDate" class="text-xs text-gray-500">
      期限: {{ formatDate(selectedDate) }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { CalendarIcon, PlusIcon } from "lucide-vue-next";
import { validateTask } from "@/composables/validation";
import { format } from "date-fns";

interface TaskData {
  name: string;
  dueDate: string | null;
}

const emit = defineEmits<{
  "add-task": [taskData: TaskData];
}>();

// State
const taskName = ref<string>("");
const selectedDate = ref<any>(null); // DateValue type from reka-ui
const isCalendarOpen = ref<boolean>(false);
const isSubmitting = ref<boolean>(false);
const errors = reactive<Record<string, string>>({});

// フォーム送信
const handleSubmit = async () => {
  try {
    const formattedDate = selectedDate.value
      ? format(selectedDate.value, "yyyy-MM-dd")
      : null;

    await emit("add-task", {
      name: taskName.value.trim(), // 空白を削除
      dueDate: formattedDate,
    });

    // フォームをリセット
    taskName.value = "";
    selectedDate.value = null;
    isSubmitting.value = false;
  } catch (error) {
    console.error("フォーム送信エラー:", error);
  }
};

const handleDateSelect = (date: any): void => {
  selectedDate.value = date;
  isCalendarOpen.value = false;
};

const formatDate = (date: any): string => {
  if (!date) return "";

  // Date オブジェクトの場合
  if (date instanceof Date) {
    return date.toLocaleDateString("ja-JP", {
      month: "2-digit",
      day: "2-digit",
    });
  }

  // DateValue オブジェクトや文字列の場合、Date オブジェクトに変換
  let dateObj: Date;
  try {
    // DateValue オブジェクトの場合、toString() で ISO 文字列を取得
    if (date.toString && typeof date.toString === "function") {
      dateObj = new Date(date.toString());
    } else if (typeof date === "string") {
      dateObj = new Date(date);
    } else {
      dateObj = new Date(date);
    }

    return dateObj.toLocaleDateString("ja-JP", {
      month: "2-digit",
      day: "2-digit",
    });
  } catch (error) {
    console.warn("日付フォーマットエラー:", error, date);
    return "";
  }
};
</script>
