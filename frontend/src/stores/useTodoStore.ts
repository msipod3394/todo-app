import { defineStore } from "pinia";
import { ref, computed } from "vue";

export interface Task {
  id: number;
  name: string;
  dueDate: string | null;
  completed: boolean;
  completedAt: Date | null;
  createdAt: Date;
}

export interface TaskUpdate {
  name?: string;
  dueDate?: string | null;
  completed?: boolean;
}

export const useTodoStore = defineStore("todo", () => {
  // State
  const tasks = ref<Task[]>([]);
  const nextId = ref<number>(1);

  // Getters
  const incompleteTasks = computed(() =>
    tasks.value.filter((task) => !task.completed)
  );

  const completedTasks = computed(() =>
    tasks.value.filter((task) => task.completed)
  );

  const totalTasks = computed(() => tasks.value.length);

  const progress = computed(() => {
    if (totalTasks.value === 0) return 0;
    return Math.round((completedTasks.value.length / totalTasks.value) * 100);
  });

  // Actions
  const addTask = (name: string, dueDate: string | null = null): void => {
    const newTask: Task = {
      id: nextId.value++,
      name: name.trim(),
      dueDate,
      completed: false,
      completedAt: null,
      createdAt: new Date(),
    };
    tasks.value.push(newTask);
  };

  const deleteTask = (id: number): void => {
    const index = tasks.value.findIndex((task) => task.id === id);
    if (index > -1) {
      tasks.value.splice(index, 1);
    }
  };

  const deleteAllCompleted = (): void => {
    tasks.value = tasks.value.filter((task) => !task.completed);
  };

  const toggleTask = (id: number): void => {
    const task = tasks.value.find((task) => task.id === id);
    if (task) {
      task.completed = !task.completed;
      task.completedAt = task.completed ? new Date() : null;
    }
  };

  const updateTask = (id: number, updates: TaskUpdate): void => {
    const task = tasks.value.find((task) => task.id === id);
    if (task) {
      Object.assign(task, updates);
    }
  };

  // Initialize with sample data matching the design
  const initializeSampleData = (): void => {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);

    const sampleTasks: Task[] = [
      {
        id: 1,
        name: "買い物に行く",
        dueDate: today.toISOString().split("T")[0],
        completed: false,
        completedAt: null,
        createdAt: new Date(),
      },
      {
        id: 2,
        name: "掃除をする",
        dueDate: tomorrow.toISOString().split("T")[0],
        completed: false,
        completedAt: null,
        createdAt: new Date(),
      },
      {
        id: 3,
        name: "○○に連絡をする",
        dueDate: today.toISOString().split("T")[0],
        completed: true,
        completedAt: new Date(),
        createdAt: new Date(),
      },
      {
        id: 4,
        name: "○○に連絡をする",
        dueDate: tomorrow.toISOString().split("T")[0],
        completed: true,
        completedAt: new Date(),
        createdAt: new Date(),
      },
    ];

    tasks.value = sampleTasks;
    nextId.value = 5;
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
    initializeSampleData,
  };
});
