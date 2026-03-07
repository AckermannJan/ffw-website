<template>
  <v-app id="my-app">
    <transition name="fade">
      <v-overlay :value="isLoading" class="overlay" v-if="isLoading">
        <div class="welcomeLoader">
          <img
            src="http://wordpress.feuerwehr-traisa.de/wp-content/uploads/2025/05/rlbs.png"
            alt="Retten Löschen Bergen Schützen"
          />
          <p class="welcomeLoader__headline">
            Unsere Freizeit für Ihre Sicherheit -<br />
            Seit {{ yearsInExistence }} Jahren
          </p>
          <p class="welcomeLoader__text">
            Wir freuen uns, dass Sie den Weg auf unsere Webseite gefunden
            haben.<br />
            Schauen Sie sich um und entdecken Sie die Vielfalt der Feuerwehr
            Traisa.
          </p>
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </div>
      </v-overlay>
    </transition>

    <transition name="fade">
      <app-header v-if="!isLoading" />
    </transition>
    <transition name="fade">
      <v-container
        fluid
        fill-height
        class="mb-8 main-content"
        v-if="!isLoading"
      >
        <v-row justify="center">
          <v-col xl="2" lg="4" md="4" cols="12" order-md="1" order="2">
            <Sidebar />
          </v-col>
          <v-col xl="5" lg="7" md="8" cols="12" order-md="2" order="1">
            <router-view :key="$route.path"></router-view>
          </v-col>
        </v-row>
      </v-container>
    </transition>
    <transition name="fade">
      <app-footer v-if="!isLoading" />
    </transition>
  </v-app>
</template>

<script>
import { mapGetters } from "vuex";
import Header from "./components/partials/Header.vue";
import Footer from "./components/partials/Footer.vue";
import Sidebar from "./components/partials/Sidebar";
import { momentInstance } from "@/utils/moment";

export default {
  data() {
    return {
      showLoader: true
    };
  },
  computed: {
    ...mapGetters({
      isLoading: "index/isLoading",
      loadingProgress: "loadingProgress"
    }),

    loaderStyle() {
      return `width: ${this.loadingProgress}%;`;
    },
    yearsInExistence() {
      return momentInstance().diff("1880-01-01", "years", false);
    }
  },
  components: {
    Sidebar,
    appHeader: Header,
    appFooter: Footer
  },
  metaInfo: {
    title: "Feuerwehr Mühltal Traisa",
    htmlAttrs: {
      lang: "de"
    },
    meta: [
      { charset: "utf-8" },
      { name: "robots", content: "INDEX, FOLLOW" },
      {
        name: "description",
        content:
          "Offizielle Homepage der Freiwilligen Feuerwehr Mühltal-Traisa. Auf dieser Seite finden Sie Informationen über die Feuerwehr Traisa."
      },
      {
        name: "author",
        content: "Freiwillige Feuerwehr Mühltal Traisa."
      },
      {
        name: "keywords",
        content:
          "feuer,feuerwehr,feuerwache,traisa,mühltal,muehltal,wache,aua,ortsteil,brand,ff," +
          "fw,ffw,post,bma,ölsput,lf,tlf,hlf,rtw,feuerwehrmann,dienst,freiwillig,freiwillige," +
          "ehrenamtlich,immer,da,darmstadt,muehltal,gemeinde,mtf,mtw,löschfahrzeug,löschgruppenfahrzeug," +
          "einsatz,einsätze,fft,ffwt,traase,traisa"
      }
    ]
  }
};
</script>

<style lang="scss">
@import "App";
</style>
