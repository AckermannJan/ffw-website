import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import vuetify from "vite-plugin-vuetify";
import basicSsl from "@vitejs/plugin-basic-ssl";
import { fileURLToPath, URL } from "node:url";

export default defineConfig({
  plugins: [vue(), vuetify({ autoImport: true }), basicSsl()],
  resolve: {
    alias: { "@": fileURLToPath(new URL("./src", import.meta.url)) },
  },
  server: {
    host: process.env.VITE_HOST || "fft.local",
    port: parseInt(process.env.VITE_PORT) || 8080,
    https: process.env.VITE_HTTPS !== "false" ? {} : false,
    open: process.platform === "darwin",
  },
});
