import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { createPinia } from "pinia";
import vuetify from "./plugins/vuetify";
import { createHead } from "@unhead/vue";
import { useIndexStore } from "./store/modules/index";
import { useSideBarStore } from "./store/modules/sideBar";

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(vuetify);
app.use(createHead());

app.mount("#app");

useIndexStore().getIndexInfo();
useSideBarStore().getSidebarInfo();
