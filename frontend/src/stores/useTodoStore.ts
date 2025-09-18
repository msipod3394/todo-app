import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { useAuthStore } from "./useAuthStore";
import {
  createTodo,
  deleteTodo,
  fetchTodos,
  markTodoCompleted,
  markTodoUncompleted,
  updateTodo as updateTodoApi,
} from "@/lib/api";
import { format, isValid, parseISO } from "date-fns";

export interface Todo {
  id: number;
  title: string;
  deadlineDate: string | null;
  completed: boolean;
  completedAt: Date | null;
  createdAt: Date;
}

export interface TodoUpdate {
  title?: string;
  deadlineDate?: string | null;
  completed?: boolean;
}

export const useTodoStore = defineStore("todo", () => {
  // State
  const todos = ref<Todo[]>([]);

  // Getters
  const incompleteTodos = computed(() =>
    todos.value.filter((todo) => !todo.completed)
  );

  // 完了したTodo一覧
  const completedTodos = computed(() =>
    todos.value.filter((todo) => todo.completed)
  );

  // 全てのTodo数
  const totalTodos = computed(() => todos.value.length);

  // 進捗率
  const progress = computed(() => {
    if (totalTodos.value === 0) return 0;
    return Math.round((completedTodos.value.length / totalTodos.value) * 100);
  });

  // Todo追加
  const addTodo = async (
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
      const res = await createTodo(authStore.token, {
        title: name.trim(),
        deadline_date: formattedDate,
      });

      // フロント側のデータを更新
      const newTodo: Todo = {
        id: res.data.id, // API から返されたID
        title: res.data.title,
        deadlineDate: res.data.deadline_date,
        completed: false,
        completedAt: null,
        createdAt: new Date(res.data.created_at),
      };

      todos.value.push(newTodo);
    } catch (error) {
      console.error("Todo作成エラー:", error);
      throw error; // UIでエラーハンドリング
    }
  };

  // Todo完了状態削除
  const deleteAllCompleted = (): void => {
    todos.value = todos.value.filter((todo) => !todo.completed);
  };

  // TODOの完了・未完了状態を切り替え
  const toggleTodo = async (id: number): Promise<void> => {
    // 認証ストア取得
    const authStore = useAuthStore();

    if (!authStore.token) {
      throw new Error("認証が必要です");
    }

    try {
      // Todoを取得
      const todo = todos.value.find((todo) => todo.id === id);

      if (!todo) {
        throw new Error("Todoが見つかりません");
      }

      if (todo.completed) {
        // TODOが完了している場合、未完了にする
        await markTodoUncompleted(authStore.token, id);
        todo.completed = false;
        todo.completedAt = null;
      } else {
        // TODOが未完了している場合、完了にする
        await markTodoCompleted(authStore.token, id);
        todo.completed = true;
        todo.completedAt = new Date();
      }
    } catch (error) {
      console.error("Todo完了状態切り替えエラー:", error);
      throw error;
    }
  };

  // Todoの内容を更新
  const updateTodo = async (id: number, updates: TodoUpdate): Promise<void> => {
    // 認証ストア取得
    const authStore = useAuthStore();

    if (!authStore.token) {
      throw new Error("認証が必要です");
    }

    try {
      // APIに送信（API関数の型に合わせてパラメータを変換）
      const apiData = {
        title: updates.title || "",
        deadline_date: updates.deadlineDate || null,
      };
      const res = await updateTodoApi(authStore.token, id, apiData);

      // フロント側のデータを更新
      const todoIndex = todos.value.findIndex((todo) => todo.id === id);

      // 更新対象のTodoを更新
      if (todoIndex !== -1) {
        todos.value[todoIndex] = {
          ...todos.value[todoIndex],
          title: res.data.title,
          deadlineDate: res.data.deadline_date,
        };
      }
    } catch (error) {
      console.error("Todo更新エラー:", error);
      throw error;
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
      todos.value = res.data.map((todo: any) => ({
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
  const removeTodo = async (id: number): Promise<void> => {
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

      // フロント側のデータを更新
      todos.value = todos.value.filter((todo) => todo.id !== id);
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
    todos,

    // Getters
    incompleteTodos,
    completedTodos,
    totalTodos,
    progress,

    // Actions
    addTodo,
    removeTodo,
    deleteAllCompleted,
    toggleTodo,
    updateTodo,
    loadTodos,
    // initializeSampleData,
  };
});
