import { createApp } from "vue";
import { createPinia } from "pinia";
import "./style.css";
import App from "./App.vue";
import router from "./router";
import { useAuthStore } from "./stores/useAuthStore";

const pinia = createPinia();
const app = createApp(App);

app.use(pinia);
app.use(router);

// 認証状態の初期化
const authStore = useAuthStore();
authStore.initializeAuth();

app.mount("#app");
