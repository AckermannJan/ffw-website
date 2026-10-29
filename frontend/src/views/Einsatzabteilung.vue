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
            <img
              :src="images.lead"
              alt="Wehrführer Nicolas Richter und Daniel Funk vor einem Löschfahrzeug"
              loading="lazy"
            />
          </div>
          <div>
            <div class="board__label">Wehrführer</div>
            <div class="board__names">Nicolas Richter &amp; Daniel Funk</div>
            <a href="mailto:traisa@feuerwehr-muehltal.de" class="ffw-btn">
              <v-icon size="18">mdi-email-outline</v-icon>
              traisa@feuerwehr-muehltal.de
            </a>
          </div>
        </div>
      </section>
    </div>
  </article>
</template>

<script>
import { useHead } from "@unhead/vue";

const WP = "https://wordpress.feuerwehr-traisa.de/wp-content/uploads";

// Static page per the design; the text comes from the WordPress page
// "einsatzabteilung" (its excerpt still feeds "Mitmachen" in the sidebar).
export default {
  name: "Einsatzabteilung",
  setup() {
    useHead({
      title: "Feuerwehr Mühltal Traisa | Einsatzabteilung",
      meta: [
        {
          name: "title",
          content: "Feuerwehr Mühltal Traisa | Einsatzabteilung",
        },
      ],
    });
  },
  data: () => ({
    facts: [
      { value: "37", label: "ehrenamtliche Aktive" },
      { value: "14", label: "Feuerwehrfrauen" },
      { value: "40–50", label: "Einsätze pro Jahr" },
      { value: "17–60", label: "Jahre aktiver Dienst" },
    ],
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
      lead: `${WP}/2025/04/Portrait_Wefu-683x1024.jpg`,
    },
  }),
};
</script>

<style scoped lang="scss">
@import "Einsatzabteilung";
</style>
