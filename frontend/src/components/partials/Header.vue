<template>
  <v-container fluid class="customContainer">
    <v-navigation-drawer v-model="drawer" absolute temporary>
      <v-list nav density="compact">
        <v-list-item to="/">
          <v-list-item-title>Startseite</v-list-item-title>
        </v-list-item>

        <v-list-item to="/seite/verein">
          <v-list-item-title>Verein</v-list-item-title>
        </v-list-item>

        <v-list-item to="/einsatzabteilung/einsatze">
          <v-list-item-title>Einsätze</v-list-item-title>
        </v-list-item>

        <v-list-item to="/einsatzabteilung" exact>
          <v-list-item-title>Einsatzabteilung</v-list-item-title>
        </v-list-item>
        <v-list-item
          to="/einsatzabteilung/dienstplan"
          prepend-icon="mdi-chevron-right"
        >
          <v-list-item-title>Dienstplan</v-list-item-title>
        </v-list-item>

        <v-list-item to="/kindergruppen">
          <v-list-item-title>Nachwuchs</v-list-item-title>
        </v-list-item>
        <v-list-item
          to="/kindergruppen/wichtelwehr"
          prepend-icon="mdi-chevron-right"
        >
          <v-list-item-title>Wichtelwehr</v-list-item-title>
        </v-list-item>
        <v-list-item
          to="/kindergruppen/wichtelwehr/uebungsplanwichtel"
          style="padding-left: 35px"
          prepend-icon="mdi-chevron-right"
        >
          <v-list-item-title>Übungsplan</v-list-item-title>
        </v-list-item>
        <v-list-item
          to="/kindergruppen/jugendfeuerwehr"
          prepend-icon="mdi-chevron-right"
        >
          <v-list-item-title>Jugendfeuerwehr</v-list-item-title>
        </v-list-item>
        <v-list-item
          to="/kindergruppen/jugendfeuerwehr/uebungsplan"
          style="padding-left: 35px"
          prepend-icon="mdi-chevron-right"
        >
          <v-list-item-title>Übungsplan</v-list-item-title>
        </v-list-item>

        <v-list-item to="/technik">
          <v-list-item-title>Technik</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-row justify="center" no-gutters class="mb-8">
      <v-col xl="7" lg="11" md="12" class="header__img">
        <img
          src="https://wordpress.feuerwehr-traisa.de/wp-content/uploads/2017/07/header.jpg"
          alt="Feuerwehr-Traisa Gerätehaus"
          class="fluidImg"
        />
      </v-col>
    </v-row>

    <v-row justify="center" no-gutters>
      <v-col
        xl="7"
        lg="11"
        md="12"
        class="navigation"
        style="background-color: #af4a45"
      >
        <v-row
          v-if="['md', 'lg', 'xl', 'xxl'].includes(breakpointName)"
          no-gutters
          align="center"
          style="height: 55px; text-align: center"
        >
          <v-col style="max-width: 50px">
            <router-link to="/" style="text-decoration: none">
              <v-icon size="large" color="#fff" style="margin-left: 15px"
                >mdi-home</v-icon
              >
            </router-link>
          </v-col>
          <v-col>
            <v-menu location="bottom" open-on-hover>
              <template #activator="{ props }">
                <v-btn
                  color="white"
                  v-bind="props"
                  variant="text"
                  :ripple="false"
                  size="large"
                  to="/seite/verein"
                >
                  Verein
                </v-btn>
              </template>
              <v-list>
                <v-list-item to="/termine">
                  <v-list-item-title
                    >Termine und Veranstaltungen</v-list-item-title
                  >
                </v-list-item>
              </v-list>
            </v-menu>
          </v-col>
          <v-col>
            <v-btn
              color="white"
              variant="text"
              :ripple="false"
              size="large"
              to="/einsatzabteilung/einsatze"
            >
              Einsätze
            </v-btn>
          </v-col>
          <v-col>
            <v-menu location="bottom" open-on-hover>
              <template #activator="{ props }">
                <v-btn
                  color="white"
                  v-bind="props"
                  variant="text"
                  :ripple="false"
                  size="large"
                  to="/einsatzabteilung"
                  exact
                >
                  Einsatzabteilung
                </v-btn>
              </template>
              <v-list>
                <v-list-item to="/einsatzabteilung/dienstplan">
                  <v-list-item-title>Dienstplan</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </v-col>
          <v-col>
            <v-menu location="bottom" open-on-hover>
              <template #activator="{ props }">
                <v-btn
                  color="white"
                  v-bind="props"
                  variant="text"
                  :ripple="false"
                  size="large"
                  to="/kindergruppen"
                >
                  Nachwuchs
                </v-btn>
              </template>
              <v-list>
                <v-list-item to="/kindergruppen/wichtelwehr">
                  <v-list-item-title>Wichtelwehr</v-list-item-title>
                </v-list-item>
                <v-list-item to="/kindergruppen/wichtelwehr/uebungsplanwichtel">
                  <v-list-item-title style="margin-left: 10px"
                    >> Übungsplan</v-list-item-title
                  >
                </v-list-item>
                <v-list-item to="/kindergruppen/jugendfeuerwehr">
                  <v-list-item-title>Jugendfeuerwehr</v-list-item-title>
                </v-list-item>
                <v-list-item to="/kindergruppen/jugendfeuerwehr/uebungsplan">
                  <v-list-item-title style="margin-left: 10px"
                    >> Übungsplan</v-list-item-title
                  >
                </v-list-item>
              </v-list>
            </v-menu>
          </v-col>
          <v-col>
            <v-btn
              color="white"
              variant="text"
              :ripple="false"
              size="large"
              to="/technik"
            >
              Technik
            </v-btn>
          </v-col>
        </v-row>
        <v-row
          v-if="['xs', 'sm'].includes(breakpointName)"
          no-gutters
          align="center"
          style="height: 55px; text-align: center"
        >
          <v-col cols="1">
            <v-icon
              size="large"
              color="#fff"
              style="margin-left: 15px"
              @click="toggleDrawer"
              >mdi-menu</v-icon
            >
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { useDisplay } from "vuetify";

export default {
  setup() {
    const { name } = useDisplay();
    return { breakpointName: name };
  },
  data: () => ({
    drawer: false,
  }),
  methods: {
    toggleDrawer() {
      this.drawer = !this.drawer;
    },
  },
};
</script>

<style lang="scss">
@import "Header";
</style>
