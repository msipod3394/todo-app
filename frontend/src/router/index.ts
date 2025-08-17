import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/useAuthStore";
import TodoApp from "@/components/TodoApp.vue";
import LoginPage from "@/pages/LoginPage.vue";
import RegisterPage from "@/pages/RegisterPage.vue";

const routes = [
  {
    path: "/",
    name: "Todo",
    component: TodoApp,
    meta: { requiresAuth: true },
  },
  {
    path: "/login",
    name: "Login",
    component: LoginPage,
    meta: { requiresAuth: false },
  },
  {
    path: "/register",
    name: "Register",
    component: RegisterPage,
    meta: { requiresAuth: false },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// ナビゲーションガード
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  // 認証が必要なページの場合
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next("/login");
  }
  // 認証済みユーザーがログイン・登録ページにアクセスした場合
  else if (!to.meta.requiresAuth && authStore.isAuthenticated) {
    next("/");
  } else {
    next();
  }
});

export default router;
