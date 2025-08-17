<template>
  <div
    class="bg-white border rounded-lg p-4 space-y-2 shadow-sm"
    :class="{
      'border-gray-300': !task.completed,
      'border-gray-200 bg-gray-50 opacity-75': task.completed,
    }"
  >
    <div class="flex items-start gap-3">
      <!-- Checkbox -->
      <Checkbox
        :model-value="task.completed"
        @update:model-value="() => emit('toggle', task.id)"
        class="mt-1"
      />

      <!-- Task Content -->
      <div class="flex-1 space-y-1">
        <!-- Task Name -->
        <div class="flex-1">
          <div
            v-if="!isEditing"
            class="text-base leading-6"
            :class="{
              'text-gray-800': !task.completed,
              'text-zinc-500 line-through': task.completed,
            }"
          >
            {{ task.name }}
          </div>

          <!-- Edit Mode -->
          <div v-else class="space-y-2">
            <Input
              v-model="editName"
              class="h-9"
              @keyup.enter="saveEdit"
              @keyup.escape="cancelEdit"
              ref="editInput"
            />
            <div class="flex gap-2">
              <Button size="sm" @click="saveEdit">保存</Button>
              <Button size="sm" variant="outline" @click="cancelEdit"
                >キャンセル</Button
              >
            </div>
          </div>
        </div>

        <!-- Dates -->
        <div class="flex gap-2 text-xs">
          <!-- Due Date -->
          <div class="flex items-center gap-1 text-gray-500">
            <span>期限:</span>
            <span>{{
              task.dueDate ? formatDueDate(task.dueDate) : "なし"
            }}</span>
          </div>

          <!-- Completed Date -->
          <div
            v-if="task.completed && task.completedAt"
            class="flex items-center gap-1 text-gray-900"
          >
            <span>完了日:</span>
            <span>{{ formatCompletedDate(task.completedAt) }}</span>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div v-if="!task.completed" class="flex items-center gap-2">
        <!-- Edit Button -->
        <Button size="icon" variant="ghost" class="h-9 w-9" @click="startEdit">
          <EditIcon class="h-4 w-4 text-blue-600" />
        </Button>

        <!-- Delete Button -->
        <Button
          size="icon"
          variant="ghost"
          class="h-9 w-9"
          @click="() => emit('delete', task.id)"
        >
          <TrashIcon class="h-4 w-4 text-red-500" />
        </Button>
      </div>

      <!-- Completed Task Actions -->
      <div v-else class="flex items-center gap-2">
        <!-- Edit Button (disabled for completed) -->
        <Button size="icon" variant="ghost" class="h-9 w-9" disabled>
          <EditIcon class="h-4 w-4 text-blue-600" />
        </Button>

        <!-- Delete Button -->
        <Button
          size="icon"
          variant="ghost"
          class="h-9 w-9"
          @click="() => emit('delete', task.id)"
        >
          <TrashIcon class="h-4 w-4 text-red-500" />
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick } from "vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Checkbox } from "@/components/ui/checkbox";
import { EditIcon, TrashIcon } from "lucide-vue-next";
import type { Task, TaskUpdate } from "@/stores/useTodoStore";

interface Props {
  task: Task;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  toggle: [id: number];
  delete: [id: number];
  update: [id: number, updates: TaskUpdate];
}>();

// State
const isEditing = ref<boolean>(false);
const editName = ref<string>("");
const editInput = ref<HTMLInputElement | null>(null);

// Methods
const startEdit = async (): Promise<void> => {
  if (props.task.completed) return;

  isEditing.value = true;
  editName.value = props.task.name;

  await nextTick();
  if (editInput.value) {
    editInput.value.focus();
  }
};

const saveEdit = (): void => {
  if (editName.value.trim() && editName.value.trim() !== props.task.name) {
    emit("update", props.task.id, { name: editName.value.trim() });
  }
  cancelEdit();
};

const cancelEdit = (): void => {
  isEditing.value = false;
  editName.value = "";
};

const formatDueDate = (date: string | Date | null): string => {
  if (!date) return "";
  if (typeof date === "string") {
    const d = new Date(date);
    return d.toLocaleDateString("ja-JP", { month: "2-digit", day: "2-digit" });
  }
  return date.toLocaleDateString("ja-JP", { month: "2-digit", day: "2-digit" });
};

const formatCompletedDate = (date: string | Date | null): string => {
  if (!date) return "";
  if (typeof date === "string") {
    const d = new Date(date);
    return d.toLocaleDateString("ja-JP", { month: "2-digit", day: "2-digit" });
  }
  return date.toLocaleDateString("ja-JP", { month: "2-digit", day: "2-digit" });
};
</script>
