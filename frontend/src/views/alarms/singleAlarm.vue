<template>
  <div class="alarm-page">
    <nav aria-label="Brotkrumen" class="crumbs">
      <router-link :to="yearLink" class="ffw-link">
        Einsätze {{ year || "" }}
      </router-link>
      <span aria-hidden="true">›</span>
      <span>{{
        ready && title.number ? "Einsatz " + title.number : "Einsatz"
      }}</span>
    </nav>

    <article class="alarm">
      <header class="ffw-notch alarm__head">
        <div class="alarm__kicker">
          <span v-if="ready && title.number">
            Einsatz {{ title.number }} / {{ year }}
          </span>
          <span v-if="ready && category" class="alarm__chip">
            <v-icon size="16">{{ category.icon }}</v-icon>
            {{ category.label }}
          </span>
        </div>
        <h1>{{ ready ? title.label : "Einsatz wird geladen …" }}</h1>
      </header>

      <div class="alarm__body">
        <template v-if="!ready">
          <div class="keyfacts" aria-hidden="true">
            <div v-for="n in 6" :key="n" class="fact fact--loading" />
          </div>
        </template>

        <template v-else>
          <section aria-label="Einsatzdaten" class="keyfacts">
            <div
              v-for="fact in facts"
              :key="fact.label"
              :class="['fact', { 'fact--big': fact.big }]"
            >
              <div class="fact__label">{{ fact.label }}</div>
              <div class="fact__value">{{ fact.value }}</div>
            </div>
          </section>

          <section v-if="vehicles.length" aria-labelledby="alarm-fleet">
            <h2 id="alarm-fleet" class="alarm__title">Eingesetzte Fahrzeuge</h2>
            <ul class="fleet">
              <li v-for="car in vehicles" :key="car.name">
                <component
                  :is="car.to ? 'router-link' : 'div'"
                  :to="car.to"
                  :class="['car', { 'car--link': car.to }]"
                >
                  <span class="car__ring">
                    <img :src="car.image" alt="" loading="lazy" />
                  </span>
                  <span class="car__name">{{ car.name }}</span>
                </component>
              </li>
            </ul>
          </section>

          <section
            v-if="hasReport"
            class="alarm__report"
            aria-labelledby="alarm-report"
          >
            <h2 id="alarm-report" class="alarm__title">Einsatzbericht</h2>
            <CmsContent :html="alarm.post_content_html" />
          </section>

          <section v-if="photos.length" aria-labelledby="alarm-photos">
            <h2 id="alarm-photos" class="alarm__title">Bilder</h2>
            <ul class="photos">
              <li v-for="(photo, i) in photos" :key="photo.src">
                <button
                  type="button"
                  class="photo"
                  :aria-label="`${photo.alt} vergrößern`"
                  @click="photoIndex = i"
                >
                  <img :src="photo.src" :alt="photo.alt" loading="lazy" />
                  <span class="photo__zoom" aria-hidden="true">
                    <v-icon size="20">mdi-magnify-plus-outline</v-icon>
                  </span>
                </button>
              </li>
            </ul>
            <PhotoLightbox v-model:index="photoIndex" :photos="photos" />
          </section>

          <nav aria-label="Weitere Einsätze" class="pager">
            <router-link
              v-if="previous"
              :to="'/einsatzabteilung/einsatze/' + previous.post_name"
              class="pager__item"
            >
              <v-icon size="20" class="pager__arrow">mdi-arrow-left</v-icon>
              <span>
                <span class="pager__label">Vorheriger Einsatz</span>
                <span class="pager__title">{{ previous.title }}</span>
              </span>
            </router-link>
            <span v-else class="pager__spacer" />
            <router-link :to="yearLink" class="pager__item pager__item--next">
              <span>
                <span class="pager__label">Übersicht</span>
                <span class="pager__title">Alle Einsätze {{ year }}</span>
              </span>
              <v-icon size="20" class="pager__arrow">mdi-arrow-right</v-icon>
            </router-link>
          </nav>
        </template>
      </div>
    </article>
  </div>
</template>

<script>
import { mapState, mapActions } from "pinia";
import { useAlarmsStore } from "@/store/modules/alarms";
import { useHead } from "@unhead/vue";
import { computed } from "vue";
import api from "@/api";
import { momentInstance } from "@/utils/moment";
import {
  ALARM_CATEGORIES,
  ALARM_VEHICLES,
  parseAlarmTitle,
} from "@/utils/alarms";
import CmsContent from "@/components/cms/CmsContent.vue";
import PhotoLightbox from "@/components/partials/PhotoLightbox.vue";

// Uploads are stored with http:// URLs; the site runs on https.
const https = (url) => url.replace(/^http:\/\//, "https://");

export default {
  name: "SingleAlarm",
  components: { CmsContent, PhotoLightbox },
  setup() {
    const alarmsStore = useAlarmsStore();
    useHead({
      title: computed(
        () =>
          "Feuerwehr Mühltal Traisa | " + (alarmsStore.alarm.post_title || ""),
      ),
      meta: [{ name: "robots", content: "nofollow" }],
    });
  },
  data: () => ({
    yearAlarms: [],
    photoIndex: null,
  }),
  computed: {
    ...mapState(useAlarmsStore, {
      alarm: "alarm",
      isLoading: "isAlarmLoading",
    }),
    // The store still holds the previous alarm while the next one loads.
    ready() {
      return !this.isLoading && this.alarm.post_name === this.slug;
    },
    slug() {
      return this.$route.params.pageSlug;
    },
    title() {
      return parseAlarmTitle(this.alarm.post_title);
    },
    time() {
      const ts = this.alarm.alarmierungszeitpunkt?.timestamp;
      // Local clock time stored as a UTC timestamp (Types), read in UTC.
      return ts ? momentInstance(parseInt(ts) * 1000).utc() : null;
    },
    year() {
      if (this.title.year) return this.title.year;
      return this.time ? this.time.year() : null;
    },
    yearLink() {
      const query =
        this.year && this.year !== new Date().getFullYear()
          ? { jahr: String(this.year) }
          : {};
      return { path: "/einsatzabteilung/einsatze", query };
    },
    category() {
      return ALARM_CATEGORIES[this.alarm.einsatzicon] || null;
    },
    facts() {
      const a = this.alarm;
      return [
        { label: "Einsatzart", value: a.einsatzart },
        { label: "Einsatzort", value: a.einsatzort },
        {
          label: "Alarmierung",
          value: this.time
            ? this.time.format("dd, DD.MM.YYYY · HH:mm") + " Uhr"
            : "",
        },
        { label: "Alarmierungsart", value: a.alarmierungsart },
        { label: "Einsatzpersonal", value: a.einsatzpersonal, big: true },
        { label: "Weitere Kräfte", value: a.weitereKraefte },
      ].filter((fact) => fact.value && String(fact.value).trim());
    },
    vehicles() {
      return (this.alarm.fahrzeuge || [])
        .map((id) => ALARM_VEHICLES[id])
        .filter(Boolean);
    },
    hasReport() {
      const html = this.alarm.post_content_html || "";
      return html.replace(/<[^>]*>|&nbsp;/g, "").trim().length > 0;
    },
    photos() {
      const urls = Object.values(this.alarm.medienbilder || {}).filter(Boolean);
      return urls.map((url, i) => ({
        src: https(url),
        alt: `Bild ${i + 1} von ${urls.length} zum Einsatz`,
      }));
    },
    // Alarms of the year come newest first, so the previous alarm is the
    // next entry in the list.
    previous() {
      const index = this.yearAlarms.findIndex((a) => a.post_name === this.slug);
      const prev = index >= 0 ? this.yearAlarms[index + 1] : null;
      if (!prev) return null;
      const { number, label } = parseAlarmTitle(prev.post_title);
      return {
        post_name: prev.post_name,
        title: number ? `Einsatz ${number} · ${label}` : label,
      };
    },
  },
  watch: {
    slug: "load",
    year(year, oldYear) {
      if (year && year !== oldYear) this.loadYear(year);
    },
  },
  mounted() {
    this.load();
    if (this.year) this.loadYear(this.year);
  },
  methods: {
    ...mapActions(useAlarmsStore, ["getAlarm"]),
    load() {
      this.photoIndex = null;
      if (this.slug) this.getAlarm(this.slug);
    },
    // Fetched here instead of through the store, so the Einsätze list keeps
    // its own state.
    loadYear(year) {
      api.getAllAlarmsFromYear(String(year), (alarms) => {
        this.yearAlarms = Array.isArray(alarms) ? alarms : [];
      });
    },
  },
};
</script>

<style scoped lang="scss">
@import "singleAlarm";
</style>
