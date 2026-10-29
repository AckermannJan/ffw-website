<template>
  <article class="verein">
    <header class="ffw-notch verein__head">
      <h1>Verein</h1>
      <div class="verein__sub">Freiwillige Feuerwehr Traisa e.V.</div>
    </header>

    <div class="verein__body">
      <p class="verein__lead">
        Der Verein „Freiwillige Feuerwehr Traisa e.V.“ unterstützt seit
        {{ yearsInExistence }} Jahren die Förderung des Brandschutzes – mit
        Ausrüstung, Nachwuchsarbeit und gelebter Gemeinschaft.
      </p>

      <section>
        <h2 class="verein__title">Was wir fördern</h2>
        <div class="two">
          <div class="support">
            <v-icon size="32" class="support__icon">mdi-fire-truck</v-icon>
            <h3>Ausrüstung &amp; Fahrzeuge</h3>
            <p>
              Wir finanzieren zusätzliche Ausrüstung bis hin zur Mitfinanzierung
              von Fahrzeugen, die unseren Einsatzkräften im Ernstfall zur
              Verfügung stehen.
            </p>
            <router-link to="/einsatzabteilung" class="ffw-link support__link"
              >Zur Einsatzabteilung</router-link
            >
          </div>
          <div class="support">
            <v-icon size="32" class="support__icon"
              >mdi-account-group-outline</v-icon
            >
            <h3>Nachwuchsarbeit</h3>
            <p>
              Unsere Wichtelfeuerwehr besteht seit {{ wichtelwehrSince }} – die
              älteste Kinderfeuerwehr Deutschlands. Die Jugendfeuerwehr sichert
              die Zukunft der Einsatzabteilung.
            </p>
            <router-link to="/kindergruppen" class="ffw-link support__link"
              >Zum Nachwuchs</router-link
            >
          </div>
        </div>
      </section>

      <section>
        <h2 class="verein__title">Feste &amp; Tradition</h2>
        <div class="fest">
          <div class="ffw-img fest__img">
            <img
              :src="images.fest"
              alt="Gottesdienst in der Fahrzeughalle beim Gerätehausfest"
              loading="lazy"
            />
          </div>
          <div class="fest__bar" />
          <div class="fest__text">
            <h3>Feiern Sie mit uns und unterstützen Sie das Ehrenamt</h3>
            <p>
              Besuchen Sie unser Gerätehausfest oder das Johannisfeuer. Am
              Gerätehausfest gibt es neben Fahrzeugschau und Einblicken in die
              Feuerwehr unsere originalen „Traaser Pannekuche“.
            </p>
            <router-link to="/termine" class="ffw-link fest__link"
              >Alle Termine und Feste</router-link
            >
          </div>
        </div>
      </section>

      <section id="mitglied" class="two">
        <div class="member">
          <div class="member__head">Mitglied werden</div>
          <div class="member__body">
            <p class="muted">
              Auf die Hilfe der Feuerwehr möchte niemand verzichten.
              Unterstützen Sie die ehrenamtliche Arbeit mit Ihrer
              Mitgliedschaft.
            </p>
            <ol class="steps">
              <li>
                <a :href="pdfs.beitritt" target="_blank" rel="noopener"
                  >Beitrittserklärung herunterladen (PDF)</a
                >
              </li>
              <li>
                <a :href="pdfs.sepa" target="_blank" rel="noopener"
                  >SEPA-Einzugsermächtigung herunterladen (PDF)</a
                >
              </li>
              <li>
                Beide ausgefüllt in den Briefkasten der Feuerwehr werfen oder
                einem Vorstandsmitglied übergeben.
              </li>
            </ol>
            <p class="member__privacy">
              Bitte beachten Sie unsere
              <a :href="pdfs.datenschutz" target="_blank" rel="noopener"
                >Datenschutzhinweise (PDF)</a
              >. Sie sind Bestandteil der Beitrittserklärung.
            </p>
          </div>
        </div>

        <div class="donate">
          <div class="ffw-notch donate__head">Spenden</div>
          <div class="donate__body">
            <p class="muted">
              Möchten Sie Ihre Feuerwehr finanziell unterstützen? Gerne stellen
              wir Ihnen eine Spendenquittung aus.
            </p>
            <div class="bank">
              <div class="bank__label">IBAN</div>
              <div class="bank__row">
                <span class="bank__value">{{ bank.iban }}</span>
                <button type="button" class="bank__copy" @click="copyIban">
                  <v-icon size="18">{{
                    copied ? "mdi-check" : "mdi-content-copy"
                  }}</v-icon>
                  {{ copied ? "Kopiert" : "Kopieren" }}
                </button>
              </div>
              <div class="bank__label bank__label--spaced">BIC</div>
              <div class="bank__bic">{{ bank.bic }}</div>
            </div>
            <div class="donate__recipient">
              Empfänger: Freiwillige Feuerwehr Traisa e.V.
            </div>
          </div>
        </div>
      </section>

      <section>
        <h2 class="verein__title">Vorstand</h2>
        <div class="board">
          <div class="ffw-img board__img">
            <img
              :src="images.vorstand"
              alt="Vereinsvorsitzende Daniela Richter und Dieter Scheider vor einem Löschfahrzeug"
              loading="lazy"
            />
          </div>
          <div>
            <div class="ffw-eyebrow">Vereinsvorsitzende</div>
            <div class="board__names">
              Daniela Richter &amp; Dieter Scheider
            </div>
            <a :href="'mailto:' + email" class="ffw-btn">
              <v-icon size="18">mdi-email-outline</v-icon>
              {{ email }}
            </a>
          </div>
        </div>
      </section>
    </div>
  </article>
</template>

<script>
import { useHead } from "@unhead/vue";
import { FOUNDED_YEAR, WICHTELWEHR_SINCE } from "@/utils/constants";

const UPLOADS = "https://wordpress.feuerwehr-traisa.de/wp-content/uploads";

// Static page: the content lives here, not in WordPress. Update this file
// when the Vorstand, the forms or the bank details change.
export default {
  name: "Verein",
  setup() {
    useHead({ title: "Feuerwehr Mühltal Traisa | Verein" });
  },
  data: () => ({
    yearsInExistence: new Date().getFullYear() - FOUNDED_YEAR,
    wichtelwehrSince: WICHTELWEHR_SINCE,
    email: "verein@feuerwehr-traisa.de",
    bank: { iban: "DE46 5085 0150 0022 0003 73", bic: "HELADEF1DAS" },
    pdfs: {
      beitritt: `${UPLOADS}/2018/11/Beitrittserklärung-FFW-Traisa.pdf`,
      sepa: `${UPLOADS}/2018/11/SEPA-Basislastschrift-Mandat-FFW-Traisa.pdf`,
      datenschutz: `${UPLOADS}/2018/11/Datenschutzhinweise-FFW-Traisa.pdf`,
    },
    images: {
      fest: `${UPLOADS}/2026/06/20260607_103328-1024x498.jpg`,
      vorstand: `${UPLOADS}/2025/04/Portrait_Vorsitz-683x1024.jpg`,
    },
    copied: false,
  }),
  methods: {
    async copyIban() {
      try {
        await navigator.clipboard.writeText(this.bank.iban.replace(/\s/g, ""));
        this.copied = true;
        setTimeout(() => (this.copied = false), 2000);
      } catch (e) {
        // Clipboard can be blocked; the IBAN stays visible to copy by hand.
      }
    },
  },
};
</script>

<style scoped lang="scss">
@import "Verein";
</style>
