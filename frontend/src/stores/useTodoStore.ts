import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { useAuthStore } from "./useAuthStore";
import {
  createTodo,
  deleteTodo,
  fetchTodos,
  markTodoCompleted,
  markTodoUncompleted,
} from "@/lib/api";
import { format, isValid, parseISO } from "date-fns";

export interface Task {
  id: number;
  title: string;
  deadlineDate: string | null;
  completed: boolean;
  completedAt: Date | null;
  createdAt: Date;
}

export interface TaskUpdate {
  title?: string;
  deadlineDate?: string | null;
  completed?: boolean;
}

export const useTodoStore = defineStore("todo", () => {
  // State
  const tasks = ref<Task[]>([]);

  // Getters
  const incompleteTasks = computed(() =>
    tasks.value.filter((task) => !task.completed)
  );

  // 完了したTodo一覧
  const completedTasks = computed(() =>
    tasks.value.filter((task) => task.completed)
  );

  // 全てのTodo数
  const totalTasks = computed(() => tasks.value.length);

  // 進捗率
  const progress = computed(() => {
    if (totalTasks.value === 0) return 0;
    return Math.round((completedTasks.value.length / totalTasks.value) * 100);
  });

  // Todo追加
  const addTask = async (
    name: string,
    deadlineDate: string | null = null
  ): Promise<void> => {
    const authStore = useAuthStore();

    if (!authStore.token) {
      throw new Error("認証が必要です");
    }

    try {
      // 日付を文字列形式に変換
      const formattedDate = formatDateForApi(deadlineDate);

      console.log("API送信データ:", {
        title: name.trim(),
        deadline_date: formattedDate,
      });

      // APIに送信
      const response = await createTodo(authStore.token, {
        title: name.trim(),
        deadline_date: formattedDate,
      });

      // ローカル状態を更新
      const newTask: Task = {
        id: response.data.id, // API から返されたID
        title: response.data.title,
        deadlineDate: response.data.deadline_date,
        completed: false,
        completedAt: null,
        createdAt: new Date(response.data.created_at),
      };

      tasks.value.push(newTask);
    } catch (error) {
      console.error("Todo作成エラー:", error);
      throw error; // UIでエラーハンドリング
    }
  };

  // Todo完了状態削除
  const deleteAllCompleted = (): void => {
    tasks.value = tasks.value.filter((task) => !task.completed);
  };

  // TODOの完了・未完了状態を切り替え
  const toggleTask = async (id: number): Promise<void> => {
    // 認証ストア取得
    const authStore = useAuthStore();

    if (!authStore.token) {
      throw new Error("認証が必要です");
    }

    try {
      // Todoを取得
      const task = tasks.value.find((task) => task.id === id);

      if (!task) {
        throw new Error("Todoが見つかりません");
      }

      if (task.completed) {
        // TODOが完了している場合、未完了にする
        await markTodoUncompleted(authStore.token, id);
        task.completed = false;
        task.completedAt = null;
      } else {
        // TODOが未完了している場合、完了にする
        await markTodoCompleted(authStore.token, id);
        task.completed = true;
        task.completedAt = new Date();
      }
    } catch (error) {
      console.error("Todo完了状態切り替えエラー:", error);
      throw error;
    }
  };

  // Todoの内容を更新
  const updateTask = (id: number, updates: TaskUpdate): void => {
    const task = tasks.value.find((task) => task.id === id);
    if (task) {
      Object.assign(task, updates);
    }
  };

  // Todo一覧取得
  const loadTodos = async (): Promise<void> => {
    // 認証ストア取得
    const authStore = useAuthStore();

    // 認証が必要な場合
    if (!authStore.token) {
      throw new Error("認証が必要です");
    }

    try {
      // APIからデータを取得
      const res = await fetchTodos(authStore.token);

      // APIレスポンスをフロントエンドに合わせて変換
      tasks.value = res.data.map((todo: any) => ({
        id: todo.id,
        title: todo.title,
        deadlineDate: todo.deadline_date,
        completed: todo.completed_at !== null,
        completedAt: todo.completed_at ? new Date(todo.completed_at) : null,
        createdAt: new Date(todo.created_at),
      }));
    } catch (error) {
      console.error("Todo一覧取得エラー:", error);
      throw error;
    }
  };

  // Todo削除
  const deleteTask = async (id: number): Promise<void> => {
    // 認証ストア取得
    const authStore = useAuthStore();

    // 認証が必要な場合
    if (!authStore.token) {
      throw new Error("認証が必要です");
    }

    try {
      // データを削除
      await deleteTodo(authStore.token, id);
      console.log("Todo削除成功:", id);

      // ローカルデータを更新
      tasks.value = tasks.value.filter((task) => task.id !== id);
    } catch (error) {
      console.error("Todo削除エラー:", error);
      throw error;
    }
  };

  // サンプルデータ初期化
  // const initializeSampleData = (): void => {
  //   const today = new Date();
  //   const tomorrow = new Date(today);
  //   tomorrow.setDate(today.getDate() + 1);

  //   const sampleTasks: Task[] = [
  //     {
  //       id: 1,
  //       title: "買い物に行く",
  //       deadlineDate: today.toISOString().split("T")[0],
  //       completed: false,
  //       completedAt: null,
  //       createdAt: new Date(),
  //     },
  //     {
  //       id: 2,
  //       title: "掃除をする",
  //       deadlineDate: tomorrow.toISOString().split("T")[0],
  //       completed: false,
  //       completedAt: null,
  //       createdAt: new Date(),
  //     },
  //     {
  //       id: 3,
  //       title: "○○に連絡をする",
  //       deadlineDate: today.toISOString().split("T")[0],
  //       completed: true,
  //       completedAt: new Date(),
  //       createdAt: new Date(),
  //     },
  //     {
  //       id: 4,
  //       title: "○○に連絡をする",
  //       deadlineDate: tomorrow.toISOString().split("T")[0],
  //       completed: true,
  //       completedAt: new Date(),
  //       createdAt: new Date(),
  //     },
  //   ];

  //   tasks.value = sampleTasks;
  //   nextId.value = 5;
  // };

  // 日付フォーマット用
  const formatDateForApi = (dateValue: any): string | null => {
    if (!dateValue) return null;

    try {
      let date: Date;

      if (typeof dateValue === "string") {
        // ISO文字列の場合
        date = parseISO(dateValue);
      } else if (dateValue instanceof Date) {
        // Dateオブジェクトの場合
        date = dateValue;
      } else if (
        dateValue.toString &&
        typeof dateValue.toString === "function"
      ) {
        // DateValueオブジェクトや他のオブジェクトの場合
        date = new Date(dateValue.toString());
      } else {
        // その他の場合はDateコンストラクタで変換を試行
        date = new Date(dateValue);
      }

      // 有効な日付かチェック
      if (!isValid(date)) {
        console.warn("無効な日付:", dateValue);
        return null;
      }

      // YYYY-MM-DD形式で返す
      return format(date, "yyyy-MM-dd");
    } catch (error) {
      console.error("日付変換エラー:", error, dateValue);
      return null;
    }
  };

  return {
    // State
    tasks,

    // Getters
    incompleteTasks,
    completedTasks,
    totalTasks,
    progress,

    // Actions
    addTask,
    deleteTask,
    deleteAllCompleted,
    toggleTask,
    updateTask,
    loadTodos,
    // initializeSampleData,
  };
});
