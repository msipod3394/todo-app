import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import { VueMcp } from "vite-plugin-vue-mcp";
import path from "path";

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), VueMcp()],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "src"),
    },
  },
});
