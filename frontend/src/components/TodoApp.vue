<template>
  <div class="min-h-screen bg-gray-50">
    <AppHeader />
    <div class="pt-16 px-0 flex justify-center">
      <div class="w-[680px]">
        <div
          class="bg-white rounded-lg border border-zinc-200 overflow-hidden"
          style="
            box-shadow:
              0px 4px 6px -4px rgba(0, 0, 0, 0.1),
              0px 10px 15px -3px rgba(0, 0, 0, 0.1);
          "
        >
          <div class="p-6 border-b border-zinc-200">
            <TaskInput @add-task="handleAddTask" />
          </div>
          <div class="p-6 space-y-6">
            <ProgressBar
              :completed-count="completedTodos.length"
              :total-count="totalTodos"
            />
            <TaskList
              :incomplete-tasks="incompleteTodos"
              :completed-tasks="completedTodos"
              @toggle="handleToggleTodo"
              @delete="handleDeleteTodo"
              @update="handleUpdateTodo"
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
import { useTodoStore, type TodoUpdate } from "@/stores/useTodoStore";
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
const incompleteTodos = computed(() => todoStore.incompleteTodos);
const completedTodos = computed(() => todoStore.completedTodos);
const totalTodos = computed(() => todoStore.totalTodos);

/**
 * Todoの追加
 * @param taskData
 */
const handleAddTask = async (taskData: TaskData): Promise<void> => {
  console.log("handleAddTask");
  console.log("taskData", taskData);

  try {
    await todoStore.addTodo(taskData.name, taskData.dueDate);
  } catch (error) {
    console.error("Todoの追加に失敗しました:", error);
  }
};

// Todo完了状態の切り替え
const handleToggleTodo = async (id: number): Promise<void> => {
  try {
    await todoStore.toggleTodo(id);
  } catch (error) {
    console.error("Todo完了状態の切り替えに失敗しました:", error);
  }
};

const handleDeleteTodo = async (id: number): Promise<void> => {
  try {
    await todoStore.removeTodo(id);
  } catch (error) {
    console.error("Todoの削除に失敗しました:", error);
  }
};

const handleUpdateTodo = async (
  id: number,
  updates: TodoUpdate
): Promise<void> => {
  try {
    await todoStore.updateTodo(id, updates);
  } catch (error) {
    console.error("Todoの更新に失敗しました:", error);
  }
};

const handleDeleteAllCompleted = (): void => {
  todoStore.deleteAllCompleted();
};

// Initialize data on mount
onMounted(async () => {
  try {
    // 認証されている場合はAPIからデータを取得
    if (authStore.isAuthenticated) {
      // APIからデータを取得
      await todoStore.loadTodos();
    } else {
      // 認証されていない場合はサンプルデータを表示
      // todoStore.initializeSampleData();
    }
  } catch (error) {
    console.error("データの初期化に失敗しました:", error);
    // エラー時はサンプルデータを表示
    // todoStore.initializeSampleData();
  }
});
</script>
