<template>
  <article class="kids">
    <header class="ffw-notch kids__head">
      <h1>Nachwuchs</h1>
      <div class="kids__sub">Wichtelwehr &amp; Jugendfeuerwehr Traisa</div>
    </header>

    <div class="kids__body">
      <p class="kids__lead">
        Bei uns wachsen Kinder und Jugendliche spielerisch in die Feuerwehr
        hinein – mit Teamgeist, Technik und jeder Menge Spaß.
      </p>

      <section aria-label="Der Weg in die Feuerwehr">
        <ol class="path">
          <li class="step">
            <span class="step__age">6 bis 10 Jahre</span>
            <span class="step__name">Wichtelwehr</span>
          </li>
          <li class="path__arrow" aria-hidden="true">
            <v-icon size="24">mdi-arrow-right</v-icon>
          </li>
          <li class="step">
            <span class="step__age">bis 17 Jahre</span>
            <span class="step__name">Jugendfeuerwehr</span>
          </li>
          <li class="path__arrow" aria-hidden="true">
            <v-icon size="24">mdi-arrow-right</v-icon>
          </li>
          <li>
            <router-link to="/einsatzabteilung" class="step step--goal">
              <span class="step__age">ab 17 Jahren</span>
              <span class="step__name">Einsatzabteilung</span>
            </router-link>
          </li>
        </ol>
      </section>

      <section>
        <div class="tabs" role="group" aria-label="Gruppe wählen">
          <button
            v-for="g in groups"
            :key="g.id"
            type="button"
            :class="['tab', { 'tab--on': g.id === active.id }]"
            :aria-pressed="g.id === active.id ? 'true' : 'false'"
            @click="pick(g.id)"
          >
            {{ g.name }}
          </button>
        </div>

        <div :key="active.id" class="group">
          <div class="group__media">
            <div class="ffw-img group__img">
              <img
                :src="active.image"
                :alt="active.imageAlt"
                :style="{ objectPosition: active.imagePosition }"
              />
            </div>
            <router-link
              :to="active.moreLink"
              class="ffw-btn ffw-btn--outline group__more"
            >
              Mehr über die {{ active.name }}
              <v-icon size="18">mdi-arrow-right</v-icon>
            </router-link>
          </div>
          <div class="group__text">
            <div>
              <h2>{{ active.title }}</h2>
              <div v-if="active.tagline" class="group__tagline">
                {{ active.tagline }}
              </div>
            </div>
            <p v-for="(text, i) in active.paragraphs" :key="i">{{ text }}</p>

            <div class="meet">
              <div
                v-for="fact in active.facts"
                :key="fact.label"
                class="meet__item"
              >
                <div class="meet__label">{{ fact.label }}</div>
                <div class="meet__value">{{ fact.value }}</div>
              </div>
            </div>

            <div class="lead">
              <div class="lead__label">{{ active.leadLabel }}</div>
              <div class="lead__names">{{ active.leadNames }}</div>
            </div>

            <div class="group__actions">
              <router-link :to="active.planLink" class="ffw-btn">
                {{ active.planLabel }}
              </router-link>
              <a
                v-if="active.email"
                :href="'mailto:' + active.email"
                class="ffw-btn ffw-btn--outline group__mail"
              >
                {{ active.email }}
              </a>
            </div>
          </div>
        </div>
      </section>
    </div>
  </article>
</template>

<script>
import { useHead } from "@unhead/vue";

const WP = "https://wordpress.feuerwehr-traisa.de/wp-content/uploads";

// Static overview; the detailed pages of both groups stay in WordPress
// (/kindergruppen/wichtelwehr/info, /kindergruppen/jugendfeuerwehr/info-2).
const GROUPS = [
  {
    id: "wichtel",
    name: "Wichtelwehr",
    title: "Wir sind die Wichtelfeuerwehr!",
    tagline: "Unsere Kleinsten stellen sich vor",
    image: `${WP}/2025/05/Gruppenb_wichtel-scaled.jpg`,
    imageAlt: "Kinder der Wichtelwehr mit ihren Betreuerinnen in Warnwesten",
    imagePosition: "center",
    paragraphs: [
      "Gegründet 1987 – als erste Kinderfeuerwehr Deutschlands. Bis heute bringen wir Kindern von 6 bis 10 Jahren die Feuerwehr kindgerecht und spielerisch näher und bereiten sie auf die Jugendfeuerwehr vor.",
      "Vor allem aber steht bei uns der Zusammenhalt im Mittelpunkt, denn nur gemeinsam kann eine Feuerwehr funktionieren.",
    ],
    facts: [
      { label: "Wann", value: "Jeden zweiten Dienstag, 17:00–18:30 Uhr" },
      { label: "Wo", value: "Feuerwehrhaus Traisa" },
    ],
    leadLabel: "Kindergruppenleitung",
    leadNames: "Lorin Kroihs & Lea-Viktoria Nopper",
    planLabel: "Zum Gruppenplan",
    planLink: "/kindergruppen/wichtelwehr/uebungsplanwichtel",
    email: "wichtelwehr@feuerwehr-muehltal.de",
    moreLink: "/kindergruppen/wichtelwehr/info",
  },
  {
    id: "jugend",
    name: "Jugendfeuerwehr",
    title: "Wir sind die Jugendfeuerwehr!",
    tagline: "",
    image: `${WP}/2025/04/Portrait_JF-683x1024.jpg`,
    imageAlt: "Leitung der Jugendfeuerwehr: Nicole Funk und Ulf Stabenow",
    imagePosition: "center 35%",
    paragraphs: [
      "Bei der Jugendfeuerwehr lernen Jugendliche früh und teilweise spielerisch den Umgang mit der Feuerwehrtechnik. Neben Übungen, Sportturnieren und Theorieunterricht stehen vor allem Zusammenhalt und soziale Kompetenz im Vordergrund.",
      "In Wettbewerben wie der Leistungsspange – der höchsten Auszeichnung der Jugendfeuerwehr – zählt immer die Gruppenleistung. Mit 17 Jahren geht es in die Einsatzabteilung.",
    ],
    facts: [
      {
        label: "Wann",
        value: "Jeden Dienstag, 18:00–20:00 Uhr (außer Schulferien)",
      },
      {
        label: "Schnuppern",
        value: "Komm vorbei – bitte mit einem Sorgeberechtigten",
      },
    ],
    leadLabel: "Leitung Jugendfeuerwehr",
    leadNames: "Nicole Funk & Ulf Stabenow",
    planLabel: "Zum Übungsplan",
    planLink: "/kindergruppen/jugendfeuerwehr/uebungsplan",
    email: "",
    moreLink: "/kindergruppen/jugendfeuerwehr/info-2",
  },
];

export default {
  name: "Kindergruppen",
  setup() {
    useHead({
      title: "Feuerwehr Mühltal Traisa | Kindergruppen",
      meta: [
        { name: "title", content: "Feuerwehr Mühltal Traisa | Kindergruppen" },
      ],
    });
  },
  data: () => ({
    groups: GROUPS,
  }),
  computed: {
    // The chosen group lives in the URL (?gruppe=jugend), so it can be linked.
    active() {
      return GROUPS.find((g) => g.id === this.$route.query.gruppe) || GROUPS[0];
    },
  },
  methods: {
    pick(id) {
      const query = id === GROUPS[0].id ? {} : { gruppe: id };
      this.$router.replace({ query });
    },
  },
};
</script>

<style scoped lang="scss">
@import "Kindergruppen";
</style>
