<template>
  <div class="min-h-screen bg-gray-50">
    <!-- App Header -->
    <AppHeader :user-email="userEmail" @logout="handleLogout" />
    <!-- Main Content -->
    <div class="pt-16 px-0 flex justify-center">
      <div class="w-[680px]">
        <!-- Main Card Container -->
        <div
          class="bg-white rounded-lg border border-zinc-200 overflow-hidden"
          style="
            box-shadow:
              0px 4px 6px -4px rgba(0, 0, 0, 0.1),
              0px 10px 15px -3px rgba(0, 0, 0, 0.1);
          "
        >
          <!-- Title Section -->
          <div class="px-6 pt-6">
            <div class="text-center">
              <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
                Todoリスト
              </h1>
            </div>
          </div>

          <!-- Input Section -->
          <div class="p-6 border-b border-zinc-200">
            <TaskInput @add-task="handleAddTask" />
          </div>

          <!-- Content Section -->
          <div class="p-6 space-y-6">
            <!-- Progress Section -->
            <ProgressBar
              :completed-count="completedTasks.length"
              :total-count="totalTasks"
            />

            <!-- Task Lists -->
            <TaskList
              :incomplete-tasks="incompleteTasks"
              :completed-tasks="completedTasks"
              @toggle="handleToggleTask"
              @delete="handleDeleteTask"
              @update="handleUpdateTask"
              @delete-all-completed="handleDeleteAllCompleted"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useTodoStore, type TaskUpdate } from "@/stores/useTodoStore";
import { useAuthStore } from "@/stores/useAuthStore";
import AppHeader from "./AppHeader.vue";
import TaskInput from "./TaskInput.vue";
import TaskList from "./TaskList.vue";
import ProgressBar from "./ProgressBar.vue";

interface TaskData {
  name: string;
  dueDate: string | null;
}

// Store
const todoStore = useTodoStore();
const authStore = useAuthStore();
const router = useRouter();

// User state - 認証ストアから取得
const userEmail = computed(
  () => authStore.currentUser?.email || "guest@example.com"
);

// Computed properties from store (reactive access)
const incompleteTasks = computed(() => todoStore.incompleteTasks);
const completedTasks = computed(() => todoStore.completedTasks);
const totalTasks = computed(() => todoStore.totalTasks);

// Methods
const handleAddTask = (taskData: TaskData): void => {
  todoStore.addTask(taskData.name, taskData.dueDate);
};

const handleToggleTask = (id: number): void => {
  todoStore.toggleTask(id);
};

const handleDeleteTask = (id: number): void => {
  todoStore.deleteTask(id);
};

const handleUpdateTask = (id: number, updates: TaskUpdate): void => {
  todoStore.updateTask(id, updates);
};

const handleDeleteAllCompleted = (): void => {
  todoStore.deleteAllCompleted();
};

const handleLogout = async (): Promise<void> => {
  try {
    await authStore.logout();
    router.push("/login");
  } catch (error) {
    console.error("ログアウトエラー:", error);
  }
};

// Initialize sample data on mount
onMounted(() => {
  todoStore.initializeSampleData();
});
</script>
