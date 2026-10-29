<template>
  <article class="abt">
    <header class="ffw-notch abt__head">
      <h1>Einsatzabteilung</h1>
      <div class="abt__sub">Der aktive Feuerwehrdienst in Traisa</div>
    </header>

    <div class="abt__body">
      <ul class="facts">
        <li v-for="fact in facts" :key="fact.label" class="fact">
          <span class="fact__value">{{ fact.value }}</span>
          <span class="fact__label">{{ fact.label }}</span>
        </li>
      </ul>

      <section aria-labelledby="abt-tasks">
        <h2 id="abt-tasks" class="abt__title">Unsere Hauptaufgaben</h2>
        <ul class="tasks">
          <li v-for="task in tasks" :key="task.label" class="task">
            <span class="task__icon" aria-hidden="true">
              <v-icon size="26">{{ task.icon }}</v-icon>
            </span>
            <span class="task__label">{{ task.label }}</span>
          </li>
        </ul>
      </section>

      <section aria-labelledby="abt-training">
        <h2 id="abt-training" class="abt__title">Ausbildung</h2>
        <div class="prose">
          <p>
            Angehörige der Einsatzabteilung benötigen eine gewisse körperliche
            Voraussetzung, die durch die bestandene medizinische Untersuchung
            G26 gewährleistet ist.
          </p>
          <p>
            Damit die Feuerwehr für jeden Notfall bereit ist, besuchen unsere
            Feuerwehrfrauen und -männer Lehrgänge und Fortbildungen –
            Führungslehrgänge an der Landesfeuerwehrschule in Kassel.
          </p>
          <p>
            Auf Standortebene bauen wir darauf auf: Regelmäßiger theoretischer
            und praktischer Unterricht im Feuerwehrhaus Traisa macht uns mit den
            örtlichen Gegebenheiten und Einsatzobjekten vertraut.
          </p>
        </div>
      </section>

      <section aria-labelledby="abt-area">
        <h2 id="abt-area" class="abt__title">Unser Einsatzgebiet</h2>
        <div class="area">
          <div class="ffw-img area__img">
            <img
              :src="images.area"
              alt="Atemschutzgeräteträger der Feuerwehr Traisa nach der Heißausbildung"
              loading="lazy"
            />
          </div>
          <div class="area__bar" />
          <div class="area__text">
            <template v-for="(item, i) in area" :key="item.title">
              <div v-if="i" class="area__divider" />
              <div>
                <h3>{{ item.title }}</h3>
                <p>{{ item.text }}</p>
              </div>
            </template>
          </div>
        </div>
      </section>

      <section class="cta" aria-labelledby="abt-cta">
        <div>
          <h2 id="abt-cta" class="cta__title">
            Interesse an der Einsatzabteilung?
          </h2>
          <p class="cta__text">
            Schau beim Dienst vorbei – alle Termine stehen im Dienstplan.
          </p>
        </div>
        <router-link to="/einsatzabteilung/dienstplan" class="cta__btn">
          Zum Dienstplan
          <v-icon size="18">mdi-arrow-right</v-icon>
        </router-link>
      </section>

      <section aria-labelledby="abt-lead">
        <h2 id="abt-lead" class="abt__title">Wehrführung</h2>
        <div class="board">
          <div class="ffw-img board__img">
            <img :src="lead.image" :alt="lead.imageAlt" loading="lazy" />
          </div>
          <div>
            <div class="board__label">Wehrführer</div>
            <div class="board__names">{{ lead.names }}</div>
            <a :href="'mailto:' + lead.email" class="ffw-btn">
              <v-icon size="18">mdi-email-outline</v-icon>
              {{ lead.email }}
            </a>
          </div>
        </div>
      </section>
    </div>
  </article>
</template>

<script>
import { usePageSeo } from "@/utils/seo";
import api from "@/api";

const WP = "https://wordpress.feuerwehr-traisa.de/wp-content/uploads";

// Shown until the API answers, and for every field left empty in WordPress.
const DEFAULTS = {
  aktive: 37,
  feuerwehrfrauen: 14,
  einsaetzeProJahr: "40–50",
  wehrfuehrung: {
    namen: "Nicolas Richter & Daniel Funk",
    email: "traisa@feuerwehr-muehltal.de",
    bild: `${WP}/2025/04/Portrait_Wefu-683x1024.jpg`,
    bildAlt:
      "Wehrführer Nicolas Richter und Daniel Funk vor einem Löschfahrzeug",
  },
};

const pick = (value, fallback) =>
  value === null || value === undefined || value === "" ? fallback : value;

// Page layout per the design. Figures and Wehrführung are maintained in
// WordPress (Types group "Einsatzabteilung" on the page "einsatzabteilung",
// endpoint getEinsatzabteilung); the rest is static text from that page.
// The page's excerpt still feeds "Mitmachen" in the sidebar.
export default {
  name: "Einsatzabteilung",
  setup() {
    usePageSeo({
      title: "Einsatzabteilung",
      description:
        "Die Einsatzabteilung der Feuerwehr Traisa: Ehrenamtliche für Brandbekämpfung, technische Hilfeleistung und Brandschutz in Mühltal. Mitmachen ab 17 Jahren.",
    });
  },
  data: () => ({
    cms: null,
    tasks: [
      { icon: "mdi-fire", label: "Brandbekämpfung" },
      { icon: "mdi-wrench", label: "Technische Hilfeleistung" },
      {
        icon: "mdi-alert-outline",
        label: "Hilfeleistung bei besonderen Vorkommnissen",
      },
      { icon: "mdi-shield-check-outline", label: "Vorbeugender Brandschutz" },
    ],
    area: [
      {
        title: "Lohbergtunnel",
        text: "Bei einem Notfall im Tunnel sind wir die ersteingesetzte Wehr am Nordportal.",
      },
      {
        title: "Überörtlicher Löschzug Mühltal",
        text: "Gemeinsam mit Nieder-Ramstadt unterstützen wir auch außerhalb der Gemeinde – bisher u. a. in Weiterstadt, Münster und Darmstadt.",
      },
      {
        title: "Brandsicherheitsdienst",
        text: "Bei größeren Veranstaltungen sichern wir auf Anordnung der Gemeindeverwaltung die Besucherinnen und Besucher ab.",
      },
    ],
    images: {
      area: `${WP}/2026/05/IMG_7064-1024x768.jpeg`,
    },
  }),
  computed: {
    facts() {
      const cms = this.cms || {};
      return [
        {
          value: String(pick(cms.aktive, DEFAULTS.aktive)),
          label: "ehrenamtliche Aktive",
        },
        {
          value: String(pick(cms.feuerwehrfrauen, DEFAULTS.feuerwehrfrauen)),
          label: "Feuerwehrfrauen",
        },
        {
          value: pick(cms.einsaetzeProJahr, DEFAULTS.einsaetzeProJahr),
          label: "Einsätze pro Jahr",
        },
      ];
    },
    lead() {
      const cms = (this.cms && this.cms.wehrfuehrung) || {};
      const fallback = DEFAULTS.wehrfuehrung;
      const names = pick(cms.namen, fallback.namen);
      const image = pick(cms.bild, fallback.bild);
      let imageAlt = pick(cms.bildAlt, "");
      if (!imageAlt) {
        imageAlt =
          image === fallback.bild ? fallback.bildAlt : `Wehrführung: ${names}`;
      }
      return {
        names,
        email: pick(cms.email, fallback.email),
        image: image.replace(/^http:\/\//, "https://"),
        imageAlt,
      };
    },
  },
  mounted() {
    api.getEinsatzabteilung((data) => {
      // Errors arrive as an Error object; keep the defaults then.
      if (data && typeof data === "object" && "aktive" in data) {
        this.cms = data;
      }
    });
  },
};
</script>

<style scoped lang="scss">
@import "Einsatzabteilung";
</style>
