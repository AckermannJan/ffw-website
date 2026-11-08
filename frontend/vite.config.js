import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import vuetify from "vite-plugin-vuetify";
import basicSsl from "@vitejs/plugin-basic-ssl";
import { fileURLToPath, URL } from "node:url";

const useHttps = process.env.VITE_HTTPS !== "false";

export default defineConfig({
  plugins: [
    vue(),
    vuetify({ autoImport: true }),
    ...(useHttps ? [basicSsl()] : []),
  ],
  resolve: {
    alias: { "@": fileURLToPath(new URL("./src", import.meta.url)) },
  },
  server: {
    host: process.env.VITE_HOST || "fft.local",
    port: parseInt(process.env.VITE_PORT) || 8080,
    https: useHttps ? {} : false,
    open: process.platform === "darwin",
  },
});
