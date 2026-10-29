<template>
  <v-app id="my-app">
    <transition name="splash">
      <SplashScreen v-if="isLoading" />
    </transition>

    <transition name="fade">
      <app-header v-if="!isLoading" />
    </transition>
    <transition name="fade">
      <div v-if="!isLoading">
        <router-view name="hero" />
        <div class="ffw-wrap layout">
          <aside class="layout__side">
            <Sidebar />
          </aside>
          <main class="layout__main">
            <router-view :key="$route.path"></router-view>
          </main>
        </div>
      </div>
    </transition>
    <transition name="fade">
      <app-footer v-if="!isLoading" />
    </transition>
  </v-app>
</template>

<script>
import { mapState } from "pinia";
import { useIndexStore } from "./store/modules/index";
import { useHead } from "@unhead/vue";
import Header from "./components/partials/Header.vue";
import Footer from "./components/partials/Footer.vue";
import Sidebar from "./components/partials/Sidebar.vue";
import SplashScreen from "./components/partials/SplashScreen.vue";
import { ORGANIZATION_LD, SITE_NAME } from "@/utils/seo";

export default {
  components: {
    Sidebar,
    SplashScreen,
    appHeader: Header,
    appFooter: Footer,
  },
  setup() {
    // Site-wide head; each page sets its own title, description, canonical
    // URL and link previews via usePageSeo (utils/seo.js).
    useHead({
      htmlAttrs: { lang: "de" },
      meta: [{ name: "author", content: SITE_NAME }],
      script: [
        {
          key: "organization-ld",
          type: "application/ld+json",
          innerHTML: JSON.stringify(ORGANIZATION_LD),
        },
      ],
    });
  },
  computed: {
    ...mapState(useIndexStore, ["isLoading"]),
  },
};
</script>

<style lang="scss">
@import "App";
</style>
