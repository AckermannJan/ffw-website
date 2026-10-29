<template>
  <article class="roster">
    <header class="ffw-notch roster__head">
      <h1>Dienstplan</h1>
      <div class="roster__sub">Einsatzabteilung Traisa</div>
    </header>

    <div class="roster__body">
      <template v-if="!entries">
        <div class="next next--loading" aria-hidden="true" />
        <div v-for="n in 5" :key="n" class="row row--loading" />
      </template>

      <template v-else>
        <div v-if="nextDuty" class="next">
          <div class="badge badge--large" aria-hidden="true">
            <div class="badge__top">{{ nextDuty.weekday }}</div>
            <div class="badge__bottom">{{ nextDuty.dayMonth }}</div>
          </div>
          <div class="next__text">
            <div class="ffw-eyebrow">
              {{ nextDuty.isToday ? "Heute" : "Nächster Dienst" }}
            </div>
            <div class="next__title">{{ nextDuty.title }}</div>
            <div class="next__meta">{{ nextDuty.meta }}</div>
          </div>
          <button
            type="button"
            class="ffw-btn next__cal"
            @click="addToCalendar(nextDuty)"
          >
            <v-icon size="18">mdi-calendar-plus</v-icon>
            In Kalender eintragen
          </button>
        </div>
        <p v-else class="muted">
          Für dieses Jahr stehen keine Dienste mehr an.
        </p>

        <div class="toolbar">
          <div class="chips" role="group" aria-label="Nach Art filtern">
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
        </div>

        <section v-for="month in months" :key="month.key" class="month">
          <h2 class="month__title">{{ month.label }}</h2>
          <div
            v-for="entry in month.entries"
            :key="entry.key"
            :class="[
              'row',
              { 'row--past': entry.isPast, 'row--today': entry.isToday },
            ]"
          >
            <div class="badge" aria-hidden="true">
              <div class="badge__top">{{ entry.weekday }}</div>
              <div class="badge__bottom">{{ entry.day }}</div>
            </div>
            <div class="row__text">
              <div class="row__title">
                {{ entry.title }}
                <span v-if="entry.isToday" class="today">Heute</span>
              </div>
              <div class="row__meta">{{ entry.meta }}</div>
            </div>
            <span v-if="entry.type" class="tag">
              <span :class="['dot', 'dot--' + entry.type.dot]" />
              {{ entry.type.label }}
            </span>
          </div>
        </section>

        <p v-if="!months.length" class="muted month">
          Keine Dienste für diese Auswahl.
        </p>
      </template>
    </div>
  </article>
</template>

<script>
import { useHead } from "@unhead/vue";
import { momentInstance } from "@/utils/moment";
import { buildIcsEvent, downloadIcs } from "@/utils/ics";
import settings from "@/settings";

// "Art" codes used by the ICS importer (scripts/icsToJsonConverter.js).
// The dot shapes differ in shape and lightness, not only in colour.
const TYPES = {
  P: { label: "Praxis", dot: "solid" },
  U: { label: "Unterricht", dot: "ring" },
  "U/P": { label: "Unterricht & Praxis", dot: "half" },
  KatS: { label: "Katastrophenschutz", dot: "square" },
  S: { label: "Sonstiges", dot: "muted" },
};

export default {
  name: "Roster",
  props: {
    roster: { type: String, default: null },
  },
  setup() {
    useHead({
      title: "Feuerwehr Mühltal Traisa | Dienstplan",
      meta: [
        { name: "title", content: "Feuerwehr Mühltal Traisa | Dienstplan" },
      ],
    });
  },
  data: () => ({
    rosterData: null,
    filter: "all",
    showPast: false,
  }),
  computed: {
    entries() {
      if (!this.rosterData) return null;
      const today = momentInstance().startOf("day");
      return this.rosterData
        .map((item, index) => {
          const [d, m, y] = item.Datum.split(".");
          const start = momentInstance(`${y}-${m}-${d}T${item.von}`);
          const [endH, endM] = (item.bis || item.von).split(":").map(Number);
          const end = start.clone().set({ hour: endH, minute: endM });
          const day = start.clone().startOf("day");
          const place = item["Ortsteil-Feuerwehr"];
          const time = `${start.format("HH:mm")}–${end.format("HH:mm")} Uhr`;
          return {
            key: `${item.Datum}-${index}`,
            title: item.Thema,
            start,
            end,
            place,
            type: TYPES[item.Art] || null,
            art: item.Art,
            weekday: start.format("dd").toUpperCase(),
            day: start.format("DD"),
            dayMonth: start.format("DD.MM"),
            meta: [place, time, item.Dauer ? `${item.Dauer} h` : ""]
              .filter(Boolean)
              .join(" · "),
            isToday: day.isSame(today),
            isPast: day.isBefore(today),
          };
        })
        .sort((a, b) => a.start - b.start);
    },
    nextDuty() {
      return this.entries.find((e) => !e.isPast) || null;
    },
    pastCount() {
      return this.entries.filter((e) => e.isPast).length;
    },
    chips() {
      const used = Object.keys(TYPES).filter((id) =>
        this.entries.some((e) => e.art === id),
      );
      return [
        { id: "all", label: "Alle" },
        ...used.map((id) => ({ id, label: TYPES[id].label })),
      ];
    },
    months() {
      const groups = [];
      for (const entry of this.entries) {
        if (entry.isPast && !this.showPast) continue;
        if (this.filter !== "all" && entry.art !== this.filter) continue;
        const key = entry.start.format("YYYY-MM");
        let group = groups.find((g) => g.key === key);
        if (!group) {
          group = { key, label: entry.start.format("MMMM YYYY"), entries: [] };
          groups.push(group);
        }
        group.entries.push(entry);
      }
      return groups;
    },
  },
  async mounted() {
    try {
      const res = await fetch(settings.ROSTER_API_URL);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      if (Array.isArray(data) && data.length > 0) {
        this.rosterData = data;
        return;
      }
    } catch {
      // fall through to local fallback
    }
    // Fallback to bundled static file (dev environment / plugin not yet deployed)
    const tmp = await import(`@/utils/rosters/${this.roster}.json`);
    this.rosterData = tmp.default;
  },
  methods: {
    addToCalendar(entry) {
      const ics = buildIcsEvent({
        title: `Feuerwehr Traisa: ${entry.title}`,
        start: entry.start.toDate(),
        end: entry.end.toDate(),
        location: entry.place ? `Feuerwehr ${entry.place}` : "",
        description: entry.type ? entry.type.label : "",
      });
      downloadIcs(`dienst-${entry.start.format("YYYY-MM-DD")}.ics`, ics);
    },
  },
};
</script>

<style scoped lang="scss">
@import "Roster";
</style>
