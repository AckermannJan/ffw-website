<template>
  <article class="termine">
    <header class="ffw-notch termine__head">
      <h1>Termine</h1>
      <div class="termine__sub">Veranstaltungen der Feuerwehr Traisa</div>
    </header>

    <div class="termine__body">
      <template v-if="isLoading && !events.length">
        <div v-for="n in 3" :key="n" class="event event--loading" />
      </template>

      <template v-else>
        <p v-if="!upcomingCount" class="muted">
          Zurzeit stehen keine Termine an. Schau bald wieder vorbei!
        </p>

        <section v-for="month in months" :key="month.key" class="month">
          <h2 class="month__title">{{ month.label }}</h2>
          <div
            v-for="event in month.events"
            :key="event.id"
            :class="[
              'event',
              { 'event--past': event.isPast, 'event--today': event.isToday },
            ]"
          >
            <div class="badge" aria-hidden="true">
              <div class="badge__top">{{ event.weekday }}</div>
              <div class="badge__bottom">{{ event.day }}</div>
            </div>
            <div class="event__text">
              <h3 class="event__title">
                {{ event.title }}
                <span v-if="event.isToday" class="today">Heute</span>
              </h3>
              <div class="event__meta">{{ event.meta }}</div>
              <!-- eslint-disable-next-line vue/no-v-html -->
              <div v-if="event.html" class="event__desc" v-html="event.html" />
              <div class="event__actions">
                <button
                  v-if="!event.isPast"
                  type="button"
                  class="ffw-btn ffw-btn--outline event__btn"
                  @click="addToCalendar(event)"
                >
                  <v-icon size="18">mdi-calendar-plus</v-icon>
                  In Kalender eintragen
                </button>
                <a
                  v-if="event.link"
                  :href="event.link"
                  class="ffw-link event__report"
                >
                  Bericht lesen
                </a>
              </div>
            </div>
          </div>
        </section>

        <button
          v-if="pastCount"
          type="button"
          class="past-toggle"
          :aria-pressed="showPast ? 'true' : 'false'"
          @click="showPast = !showPast"
        >
          {{
            showPast
              ? "Vergangene ausblenden"
              : `Vergangene einblenden (${pastCount})`
          }}
        </button>

        <aside class="hint">
          <v-icon size="20">mdi-calendar-clock</v-icon>
          <span>
            Übungen und Unterrichte der Einsatzabteilung stehen im
            <router-link to="/einsatzabteilung/dienstplan" class="ffw-link">
              Dienstplan</router-link
            >.
          </span>
        </aside>
      </template>
    </div>
  </article>
</template>

<script>
import { mapState, mapActions } from "pinia";
import { useTermineStore } from "@/store/modules/termine";
import { useHead } from "@unhead/vue";
import { momentInstance } from "@/utils/moment";
import { buildIcsEvent, downloadIcs } from "@/utils/ics";

// Types stores "zeitpunkt" as the local clock time encoded as a UTC
// timestamp, so dates are read in UTC (same as dateFilters.js).
const readDate = (zeitpunkt) =>
  momentInstance(parseInt(zeitpunkt.timestamp) * 1000).utc();

// Strips Word/LibreOffice leftovers (class="western", lang, style) from the
// short event descriptions.
const cleanHtml = (html = "") =>
  html.replace(/\s(class|lang|style)="[^"]*"/g, "").trim();

export default {
  name: "Termine",
  setup() {
    useHead({
      title: "Feuerwehr Mühltal Traisa | Termine",
      meta: [{ name: "title", content: "Feuerwehr Mühltal Traisa | Termine" }],
    });
  },
  data: () => ({
    showPast: false,
  }),
  computed: {
    ...mapState(useTermineStore, ["isLoading", "termine"]),
    events() {
      const today = momentInstance().format("YYYY-MM-DD");
      return (this.termine || [])
        .filter((t) => t.zeitpunkt && t.zeitpunkt.timestamp)
        .map((t) => {
          const date = readDate(t.zeitpunkt);
          const day = date.format("YYYY-MM-DD");
          return {
            id: t.ID,
            title: t.post_title,
            date,
            weekday: date.format("dd").toUpperCase(),
            day: date.format("DD"),
            meta: `${date.format("dddd, D. MMMM YYYY")} · ${date.format("HH:mm")} Uhr`,
            html: cleanHtml(t.post_content),
            link: t.hasLink && t.link ? t.link : null,
            isToday: day === today,
            isPast: day < today,
          };
        })
        .sort((a, b) => a.date - b.date);
    },
    pastCount() {
      return this.events.filter((e) => e.isPast).length;
    },
    upcomingCount() {
      return this.events.length - this.pastCount;
    },
    months() {
      const groups = [];
      for (const event of this.events) {
        if (event.isPast && !this.showPast) continue;
        const key = event.date.format("YYYY-MM");
        let group = groups.find((g) => g.key === key);
        if (!group) {
          group = { key, label: event.date.format("MMMM YYYY"), events: [] };
          groups.push(group);
        }
        group.events.push(event);
      }
      return groups;
    },
  },
  mounted() {
    this.getAllMeetings();
  },
  methods: {
    ...mapActions(useTermineStore, ["getAllMeetings"]),
    addToCalendar(event) {
      const d = event.date;
      const start = new Date(
        d.year(),
        d.month(),
        d.date(),
        d.hour(),
        d.minute(),
      );
      const plain = document.createElement("div");
      plain.innerHTML = event.html;
      // Termine have no end time in the CMS, so the calendar entry has none.
      const ics = buildIcsEvent({
        title: `Feuerwehr Traisa: ${event.title}`,
        start,
        description: plain.textContent.trim(),
      });
      downloadIcs(`termin-${d.format("YYYY-MM-DD")}.ics`, ics);
    },
  },
};
</script>

<style scoped lang="scss">
@import "Termine";
</style>
