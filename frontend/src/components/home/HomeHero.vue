<template>
  <section class="hero">
    <div :class="['ffw-wrap', 'hero__grid', { 'hero__grid--text': !hero }]">
      <div>
        <h1 class="hero__title">
          Unsere Freizeit für Ihre Sicherheit –
          <span class="hero__accent">seit {{ yearsInExistence }} Jahren</span>
        </h1>
        <p class="hero__lead">
          Wir freuen uns, dass Sie den Weg auf unsere Webseite gefunden haben.
          Schauen Sie sich um und entdecken Sie die Vielfalt der Feuerwehr
          Traisa.
        </p>
        <div class="hero__actions">
          <router-link to="/seite/verein" class="ffw-btn">
            Mitglied werden
            <v-icon size="18">mdi-arrow-right</v-icon>
          </router-link>
          <router-link to="/kindergruppen" class="ffw-btn ffw-btn--outline">
            Mitmachen im Nachwuchs
          </router-link>
        </div>
      </div>
      <!-- Start-Bild of the private WordPress page "startseite" -->
      <div v-if="hero" class="ffw-img hero__img">
        <img :src="hero.image" :alt="hero.imageAlt" />
      </div>
    </div>
  </section>

  <section class="ffw-wrap facts-wrap" aria-label="Zahlen">
    <ul class="facts">
      <li class="fact">
        <span class="fact__value">{{ yearsInExistence }} Jahre</span>
        <span class="fact__label">ehrenamtlich im Einsatz für Traisa</span>
      </li>
      <li v-if="alarmCountThisYear !== undefined" class="fact">
        <span class="fact__value">{{ alarmCountThisYear }} Einsätze</span>
        <span class="fact__label">bisher im Jahr {{ currentYear }}</span>
      </li>
      <li class="fact">
        <span class="fact__value">seit {{ wichtelwehrSince }}</span>
        <span class="fact__label"
          >Wichtelwehr – älteste Kinderfeuerwehr Deutschlands</span
        >
      </li>
    </ul>
  </section>
</template>

<script>
import { mapState } from "pinia";
import { useSideBarStore } from "@/store/modules/sideBar";
import { FOUNDED_YEAR, WICHTELWEHR_SINCE } from "@/utils/constants";

export default {
  name: "HomeHero",
  data: () => ({
    wichtelwehrSince: WICHTELWEHR_SINCE,
    currentYear: new Date().getFullYear(),
  }),
  computed: {
    ...mapState(useSideBarStore, ["alarmCountThisYear", "hero"]),
    yearsInExistence() {
      return this.currentYear - FOUNDED_YEAR;
    },
  },
};
</script>

<style scoped lang="scss">
.hero {
  background: var(--ffw-surface);
  padding: 16px 0 56px;

  &__grid {
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
    gap: 40px;
    align-items: center;

    @media (max-width: 960px) {
      grid-template-columns: minmax(0, 1fr);
    }

    &--text {
      grid-template-columns: minmax(0, 1fr);
    }
  }

  &__title {
    margin: 0;
    font-size: 2.875rem;
    font-weight: 800;
    font-style: italic;
    line-height: 1.1;
    letter-spacing: -0.015em;

    @media (max-width: 1120px) {
      font-size: 2.375rem;
    }
    @media (max-width: 640px) {
      font-size: 1.9375rem;
    }
  }

  &__accent {
    color: var(--ffw-red-text);
  }

  &__lead {
    margin: 20px 0 0;
    max-width: 52ch;
    font-size: 1.125rem;
    color: var(--ffw-muted);
  }

  &__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 28px;
  }

  &__img {
    aspect-ratio: 16 / 10;
    border-radius: var(--ffw-radius);
    border-bottom: 8px solid var(--ffw-red);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
  }
}

.facts-wrap {
  position: relative;
  margin-top: -28px;
}

.facts {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  margin: 0;
  padding: 0;
  list-style: none;
  background: var(--ffw-panel);
  border-radius: var(--ffw-radius);

  @media (max-width: 640px) {
    grid-template-columns: minmax(0, 1fr);
  }
}

.fact {
  padding: 22px 28px;

  & + & {
    border-left: 2px dotted var(--ffw-red);

    @media (max-width: 640px) {
      border-left: 0;
      border-top: 2px dotted var(--ffw-red);
    }
  }

  &__value {
    display: block;
    font-size: 2.125rem;
    font-weight: 800;
    line-height: 1.1;
    color: var(--ffw-red-text);
  }

  &__label {
    display: block;
    font-size: 0.9375rem;
    color: var(--ffw-muted);
  }
}
</style>
