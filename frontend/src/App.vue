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

export default {
  components: {
    Sidebar,
    SplashScreen,
    appHeader: Header,
    appFooter: Footer,
  },
  setup() {
    useHead({
      title: "Feuerwehr Mühltal Traisa",
      htmlAttrs: { lang: "de" },
      meta: [
        { charset: "utf-8" },
        { name: "robots", content: "INDEX, FOLLOW" },
        {
          name: "description",
          content:
            "Offizielle Homepage der Freiwilligen Feuerwehr Mühltal-Traisa. Auf dieser Seite finden Sie Informationen über die Feuerwehr Traisa.",
        },
        {
          name: "author",
          content: "Freiwillige Feuerwehr Mühltal Traisa.",
        },
        {
          name: "keywords",
          content:
            "feuer,feuerwehr,feuerwache,traisa,mühltal,muehltal,wache,aua,ortsteil,brand,ff," +
            "fw,ffw,post,bma,ölsput,lf,tlf,hlf,rtw,feuerwehrmann,dienst,freiwillig,freiwillige," +
            "ehrenamtlich,immer,da,darmstadt,muehltal,gemeinde,mtf,mtw,löschfahrzeug,löschgruppenfahrzeug," +
            "einsatz,einsätze,fft,ffwt,traase,traisa",
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
