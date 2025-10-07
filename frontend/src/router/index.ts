import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/useAuthStore";
import TodoApp from "@/components/TodoApp.vue";
import SigninPage from "@/pages/SigninPage.vue";
import SignupPage from "@/pages/SignupPage.vue";

const routes = [
  {
    path: "/",
    name: "Todo",
    component: TodoApp,
    meta: { requiresAuth: true },
  },
  {
    path: "/signin",
    name: "Signin",
    component: SigninPage,
    meta: { requiresAuth: false },
  },
  {
    path: "/signup",
    name: "Signup",
    component: SignupPage,
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
    next("/signin");
  }
  // 認証済みユーザーがログイン・登録ページにアクセスした場合
  else if (!to.meta.requiresAuth && authStore.isAuthenticated) {
    next("/");
  } else {
    next();
  }
});

export default router;
