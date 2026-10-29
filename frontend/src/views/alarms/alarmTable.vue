<template>
  <article class="alarms">
    <header class="ffw-notch alarms__head">
      <h1>Einsätze {{ selectedYear }}</h1>
      <label class="year">
        <span class="ffw-visually-hidden">Jahr wählen</span>
        <select v-model="selectedYear" class="year__select" @change="loadYear">
          <option v-for="year in selectableYears" :key="year" :value="year">
            {{ year }}
          </option>
        </select>
        <v-icon size="20" class="year__icon">mdi-chevron-down</v-icon>
      </label>
    </header>

    <div class="alarms__body">
      <template v-if="isLoading">
        <div class="stats" aria-hidden="true">
          <div v-for="n in 4" :key="n" class="stat stat--loading" />
        </div>
        <div v-for="n in 6" :key="n" class="row row--loading" />
      </template>

      <div v-else-if="!alarms.length" class="empty">
        <v-icon size="64" class="empty__icon">mdi-fire</v-icon>
        <h2>Bisher wurden keine Einsätze gemeldet.</h2>
      </div>

      <template v-else>
        <div class="stats">
          <div v-for="cat in categoryStats" :key="cat.id" class="stat">
            <div class="stat__value">{{ cat.count }}</div>
            <div class="stat__label">{{ cat.plural }}</div>
          </div>
        </div>

        <div class="chips" role="group" aria-label="Einsätze filtern">
          <button
            v-for="chip in chips"
            :key="chip.id"
            type="button"
            :class="['chip', { 'chip--on': filter === chip.id }]"
            :aria-pressed="filter === chip.id ? 'true' : 'false'"
            @click="filter = chip.id"
          >
            {{ chip.label }}
          </button>
        </div>

        <section v-for="month in months" :key="month.key" class="month">
          <h2 class="month__title">{{ month.label }}</h2>
          <router-link
            v-for="alarm in month.alarms"
            :key="alarm.ID"
            :to="'/einsatzabteilung/einsatze/' + alarm.post_name"
            class="row"
          >
            <div class="badge" aria-hidden="true">
              <div class="badge__top">Nr.</div>
              <div
                :class="[
                  'badge__bottom',
                  { 'badge__bottom--long': (alarm.number || '').length > 3 },
                ]"
              >
                {{ alarm.number || "–" }}
              </div>
            </div>
            <div class="row__text">
              <div class="row__title">{{ alarm.label }}</div>
              <div class="row__date">{{ alarm.date }}</div>
            </div>
            <span v-if="alarm.category" class="tag">
              <v-icon size="16">{{ alarm.category.icon }}</v-icon>
              {{ alarm.category.label }}
            </span>
          </router-link>
        </section>

        <p v-if="!months.length" class="empty-filter">
          Keine Einsätze in dieser Kategorie.
        </p>
      </template>
    </div>
  </article>
</template>

<script>
import { mapState, mapActions } from "pinia";
import { useAlarmsStore } from "@/store/modules/alarms";
import { usePageSeo } from "@/utils/seo";
import { momentInstance } from "@/utils/moment";
import { ALARM_CATEGORIES, parseAlarmTitle } from "@/utils/alarms";

const FIRST_YEAR = 2013;

const yearFromQuery = (value) => {
  const year = parseInt(value);
  const now = new Date().getFullYear();
  return year >= FIRST_YEAR && year <= now ? year : now;
};

export default {
  name: "AlarmTable",
  setup() {
    usePageSeo({
      title: "Einsätze",
      description:
        "Alle Einsätze der Feuerwehr Traisa nach Jahr: Brände, technische Hilfeleistungen und Brandmeldealarme – mit Datum, Einsatzart und Einsatzbericht.",
    });
  },
  data() {
    return {
      selectedYear: yearFromQuery(this.$route.query.jahr),
      filter: "all",
    };
  },
  computed: {
    ...mapState(useAlarmsStore, ["alarms", "isLoading"]),
    selectableYears() {
      const years = [];
      for (let y = new Date().getFullYear(); y >= FIRST_YEAR; y--) {
        years.push(y);
      }
      return years;
    },
    // Alarm times are stored as local time in a UTC timestamp (Types), so
    // they are formatted in UTC, like everywhere else on the site.
    entries() {
      return this.alarms.map((alarm) => {
        const time = momentInstance(
          parseInt(alarm.alarmierungszeitpunkt.timestamp) * 1000,
        ).utc();
        const { number, label } = parseAlarmTitle(alarm.post_title);
        return {
          ...alarm,
          number,
          label,
          time,
          date: time.format("dd, DD.MM.YYYY · HH:mm") + " Uhr",
          category: ALARM_CATEGORIES[alarm.einsatzicon] || null,
        };
      });
    },
    categoryStats() {
      return Object.entries(ALARM_CATEGORIES).map(([id, cat]) => ({
        id,
        plural: cat.plural,
        count: this.entries.filter((e) => e.einsatzicon === id).length,
      }));
    },
    chips() {
      return [
        { id: "all", label: `Alle (${this.entries.length})` },
        ...Object.entries(ALARM_CATEGORIES).map(([id, cat]) => ({
          id,
          label: cat.label,
        })),
      ];
    },
    months() {
      const shown =
        this.filter === "all"
          ? this.entries
          : this.entries.filter((e) => e.einsatzicon === this.filter);
      const groups = [];
      for (const alarm of shown) {
        const key = alarm.time.format("YYYY-MM");
        let group = groups.find((g) => g.key === key);
        if (!group) {
          group = { key, label: alarm.time.format("MMMM YYYY"), alarms: [] };
          groups.push(group);
        }
        group.alarms.push(alarm);
      }
      return groups;
    },
  },
  mounted() {
    this.loadYear();
  },
  methods: {
    ...mapActions(useAlarmsStore, ["getAllAlarmsFromYear"]),
    loadYear() {
      this.filter = "all";
      // Keep the year in the URL (?jahr=2025) so alarm pages can link back
      // to the right year; the current year needs no parameter.
      const query =
        this.selectedYear === new Date().getFullYear()
          ? {}
          : { jahr: String(this.selectedYear) };
      this.$router.replace({ query });
      this.getAllAlarmsFromYear(String(this.selectedYear));
    },
  },
};
</script>

<style scoped lang="scss">
@import "alarmTable";
</style>
