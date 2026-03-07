import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import vuetify from "./plugins/vuetify";
import VueMeta from "vue-meta";

Vue.config.productionTip = false;
Vue.use(VueMeta);

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App),
  created() {
    this.$store.dispatch("index/getIndexInfo");
    this.$store.dispatch("sideBar/getSidebarInfo");
  }
}).$mount("#app");
