<template>
  <div class="min-h-screen bg-gray-50">
    <!-- App Header -->
    <AppHeader />
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

// User state - 認証ストアから取得（AppHeaderで直接使用するため不要）
// const userEmail = computed(
//   () => authStore.currentUser?.email || "guest@example.com"
// );

// Computed properties from store (reactive access)
const incompleteTasks = computed(() => todoStore.incompleteTasks);
const completedTasks = computed(() => todoStore.completedTasks);
const totalTasks = computed(() => todoStore.totalTasks);

/**
 * Todoの追加
 * @param taskData
 */
const handleAddTask = async (taskData: TaskData): Promise<void> => {
  console.log("handleAddTask");
  console.log("taskData", taskData);

  try {
    await todoStore.addTask(taskData.name, taskData.dueDate);
  } catch (error) {
    console.error("Todoの追加に失敗しました:", error);
  }
};

const handleToggleTask = async (id: number): Promise<void> => {
  try {
    await todoStore.toggleTask(id);
  } catch (error) {
    console.error("Todo完了状態の切り替えに失敗しました:", error);
  }
};

const handleDeleteTask = async (id: number): Promise<void> => {
  try {
    await todoStore.deleteTask(id);
  } catch (error) {
    console.error("Todoの削除に失敗しました:", error);
  }
};

const handleUpdateTask = async (
  id: number,
  updates: TaskUpdate
): Promise<void> => {
  try {
    await todoStore.updateTask(id, updates);
  } catch (error) {
    console.error("Todoの更新に失敗しました:", error);
  }
};

const handleDeleteAllCompleted = (): void => {
  todoStore.deleteAllCompleted();
};

// handleLogoutはAppHeaderで直接処理されるため不要
// const handleLogout = async (): Promise<void> => {
//   try {
//     await authStore.logout();
//     router.push("/login");
//   } catch (error) {
//     console.error("ログアウトエラー:", error);
//   }
// };

// Initialize data on mount
onMounted(async () => {
  try {
    // 認証されている場合はAPIからデータを取得
    if (authStore.isAuthenticated) {
      // APIからデータを取得
      await todoStore.fetchTasks();
    }
    // else {
    //   // 認証されていない場合はサンプルデータを表示
    //   todoStore.initializeSampleData();
    // }
  } catch (error) {
    console.error("データの初期化に失敗しました:", error);
    // エラー時はサンプルデータを表示
    // todoStore.initializeSampleData();
  }
});
</script>
