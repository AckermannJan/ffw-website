<template>
  <div class="site-header">
    <div class="utility">
      <div class="ffw-wrap utility__inner">
        <span class="utility__claim">
          Freiwillige Feuerwehr Mühltal · Ortsteil Traisa · seit
          {{ foundedYear }}
        </span>
        <span>
          Im Notfall immer
          <component
            :is="canCall ? 'a' : 'span'"
            :href="canCall ? 'tel:112' : undefined"
            class="utility__emergency"
            >112</component
          >
          wählen
        </span>
      </div>
    </div>

    <header class="brand">
      <div class="ffw-wrap brand__grid">
        <router-link to="/" class="brand__sketch" aria-label="Zur Startseite">
          <img :src="sketch" alt="" width="720" height="261" />
        </router-link>
        <div class="brand__text">
          <div class="brand__title">Freiwillige Feuerwehr Traisa</div>
          <div class="brand__claim">
            Wir sind jederzeit für Sie da – ehrenamtlich!
          </div>
        </div>
        <div class="brand__actions">
          <component
            :is="canCall ? 'a' : 'span'"
            :href="canCall ? 'tel:112' : undefined"
            :class="[
              'ffw-btn',
              'brand__emergency',
              { 'brand__emergency--label': !canCall },
            ]"
          >
            <v-icon size="20">mdi-phone</v-icon>
            Notruf 112
          </component>
          <button
            type="button"
            class="icon-btn"
            :aria-label="
              isDark ? 'Helles Design aktivieren' : 'Dunkles Design aktivieren'
            "
            @click="toggle"
          >
            <v-icon size="22">{{
              isDark ? "mdi-white-balance-sunny" : "mdi-weather-night"
            }}</v-icon>
          </button>
        </div>
      </div>
    </header>

    <nav class="nav" aria-label="Hauptnavigation">
      <div class="ffw-wrap">
        <div class="nav__bar">
          <ul class="nav__list">
            <li>
              <router-link
                to="/"
                class="nav__link nav__link--home"
                :class="{ 'is-active': $route.path === '/' }"
                aria-label="Startseite"
              >
                <v-icon size="22">mdi-home</v-icon>
              </router-link>
            </li>
            <li v-for="item in items" :key="item.to" class="nav__item">
              <router-link
                :to="item.to"
                class="nav__link"
                :class="{ 'is-active': isActive(item) }"
              >
                {{ item.label }}
                <v-icon v-if="item.children" size="16">mdi-chevron-down</v-icon>
              </router-link>
              <ul v-if="item.children" class="nav__sub">
                <li v-for="child in item.children" :key="child.to">
                  <router-link
                    :to="child.to"
                    :class="['nav__sub-link', { 'is-nested': child.nested }]"
                  >
                    {{ child.label }}
                  </router-link>
                </li>
              </ul>
            </li>
          </ul>

          <router-link
            to="/"
            class="nav__link nav__mobile-home"
            aria-label="Startseite"
          >
            <v-icon size="24">mdi-home</v-icon>
          </router-link>
          <button
            type="button"
            class="nav__toggle"
            :aria-expanded="menuOpen ? 'true' : 'false'"
            aria-controls="mobile-menu"
            @click="menuOpen = !menuOpen"
          >
            Menü
            <v-icon size="24">{{ menuOpen ? "mdi-close" : "mdi-menu" }}</v-icon>
          </button>
        </div>

        <ul v-if="menuOpen" id="mobile-menu" class="nav__mobile">
          <li>
            <router-link to="/" class="nav__mobile-link"
              >Startseite</router-link
            >
          </li>
          <template v-for="item in items" :key="item.to">
            <li>
              <router-link :to="item.to" class="nav__mobile-link">
                {{ item.label }}
              </router-link>
            </li>
            <li v-for="child in item.children || []" :key="child.to">
              <router-link
                :to="child.to"
                :class="[
                  'nav__mobile-link',
                  'nav__mobile-link--child',
                  { 'is-nested': child.nested },
                ]"
              >
                {{ child.label }}
              </router-link>
            </li>
          </template>
        </ul>
      </div>
    </nav>
  </div>
</template>

<script>
import sketch from "@/assets/img/sketch.png";
import { useColorScheme } from "@/composables/useColorScheme";
import { usePhoneCalls } from "@/composables/usePhoneCalls";
import { FOUNDED_YEAR } from "@/utils/constants";

const NAV_ITEMS = [
  {
    label: "Verein",
    to: "/seite/verein",
    match: ["/seite/verein", "/termine"],
    children: [{ label: "Termine und Veranstaltungen", to: "/termine" }],
  },
  {
    label: "Einsätze",
    to: "/einsatzabteilung/einsatze",
    match: ["/einsatzabteilung/einsatze"],
  },
  {
    label: "Einsatzabteilung",
    to: "/einsatzabteilung",
    match: ["/einsatzabteilung", "/einsatzabteilung/dienstplan"],
    exact: true,
    children: [{ label: "Dienstplan", to: "/einsatzabteilung/dienstplan" }],
  },
  {
    label: "Nachwuchs",
    to: "/kindergruppen",
    match: ["/kindergruppen"],
    children: [
      { label: "Wichtelwehr", to: "/kindergruppen/wichtelwehr" },
      {
        label: "Übungsplan Wichtelwehr",
        to: "/kindergruppen/wichtelwehr/uebungsplanwichtel",
        nested: true,
      },
      { label: "Jugendfeuerwehr", to: "/kindergruppen/jugendfeuerwehr" },
      {
        label: "Übungsplan Jugendfeuerwehr",
        to: "/kindergruppen/jugendfeuerwehr/uebungsplan",
        nested: true,
      },
    ],
  },
  { label: "Technik", to: "/technik", match: ["/technik"] },
];

export default {
  name: "AppHeader",
  setup() {
    const { isDark, toggle } = useColorScheme();
    const { canCall } = usePhoneCalls();
    return { isDark, toggle, canCall };
  },
  data: () => ({
    menuOpen: false,
    items: NAV_ITEMS,
    sketch,
    foundedYear: FOUNDED_YEAR,
  }),
  watch: {
    "$route.path"() {
      this.menuOpen = false;
    },
  },
  methods: {
    isActive(item) {
      const path = this.$route.path;
      if (item.exact) return item.match.includes(path);
      // "Einsätze" lives under /einsatzabteilung, so prefix matching must not
      // light up "Einsatzabteilung" for alarm pages.
      return item.match.some((m) => path === m || path.startsWith(m + "/"));
    },
  },
};
</script>

<style scoped lang="scss">
@import "Header";
</style>
