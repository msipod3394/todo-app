<template>
  <div class="space-y-6">
    <!-- Incomplete Tasks Section -->
    <div v-if="incompleteTasks.length > 0" class="space-y-3">
      <div class="flex items-center">
        <h2 class="text-lg text-gray-800">
          未完了のタスク ({{ incompleteTasks.length }})
        </h2>
      </div>

      <div class="space-y-2">
        <TaskItem
          v-for="task in incompleteTasks"
          :key="task.id"
          :task="task"
          @toggle="(id: number) => emit('toggle', id)"
          @delete="(id: number) => emit('delete', id)"
          @update="
            (id: number, updates: TaskUpdate) => emit('update', id, updates)
          "
        />
      </div>
    </div>

    <!-- No Incomplete Tasks -->
    <div v-else class="text-center py-8 text-gray-500">
      <!-- No tasks at all -->
      <div v-if="totalTasks === 0">
        <div
          class="h-12 w-12 mx-auto mb-2 bg-blue-100 rounded-full flex items-center justify-center"
        >
          <PlusIcon class="h-6 w-6 text-blue-500" />
        </div>
        <p>タスクを登録しましょう！</p>
      </div>

      <!-- All tasks completed -->
      <div v-else>
        <CheckCircleIcon class="h-12 w-12 mx-auto mb-2 text-green-500" />
        <p>すべてのタスクが完了しました！</p>
      </div>
    </div>

    <!-- Completed Tasks Section -->
    <div v-if="completedTasks.length > 0" class="space-y-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <ChevronDownIcon
            class="h-4 w-4 text-gray-600 transition-transform duration-200"
            :class="{ 'rotate-180': !showCompleted }"
          />
          <button
            @click="showCompleted = !showCompleted"
            class="text-lg text-gray-600 hover:text-gray-800 transition-colors"
          >
            完了したタスク ({{ completedTasks.length }})
          </button>
        </div>

        <button
          @click="emit('deleteAllCompleted')"
          class="text-sm text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded-md transition-colors"
        >
          すべて削除
        </button>
      </div>

      <div v-if="showCompleted" class="space-y-2">
        <TaskItem
          v-for="task in completedTasks"
          :key="task.id"
          :task="task"
          @toggle="(id: number) => emit('toggle', id)"
          @delete="(id: number) => emit('delete', id)"
          @update="
            (id: number, updates: TaskUpdate) => emit('update', id, updates)
          "
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import TaskItem from "./TaskItem.vue";
import { CheckCircleIcon, ChevronDownIcon, PlusIcon } from "lucide-vue-next";
import type { Task, TaskUpdate } from "@/stores/useTodoStore";

interface Props {
  incompleteTasks: Task[];
  completedTasks: Task[];
}

const props = defineProps<Props>();

// Computed property to check if there are no tasks at all
const totalTasks = computed(
  () => props.incompleteTasks.length + props.completedTasks.length
);

const emit = defineEmits<{
  toggle: [id: number];
  delete: [id: number];
  update: [id: number, updates: TaskUpdate];
  deleteAllCompleted: [];
}>();

// State
const showCompleted = ref<boolean>(true);
</script>
