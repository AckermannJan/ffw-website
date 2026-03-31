<template>
  <div v-if="rosterData">
    <v-row
      v-for="(appointment, index) in rosterData"
      :key="index"
      class="calendar__entry mb-3"
    >
      <v-col
        :class="[
          'calendar__date',
          { 'calendar__date--active': isActive(appointment) },
        ]"
      >
        <span>{{ rosterWeekDay(appointment.Datum) }}</span>
        <span
          >{{ rosterNumberDay(appointment.Datum) }}.{{
            rosterNumberMonth(appointment.Datum)
          }}</span
        >
      </v-col>
      <v-col class="calender__info" style="padding: 0; position: relative">
        <div>{{ appointment.Thema }}</div>
        <div class="subtitle-2 calendar__detail-date">
          Art: {{ type[appointment.Art] }}<br />
          Ort: {{ appointment["Ortsteil-Feuerwehr"] }}<br />
          Uhrzeit: {{ appointment.von }} - {{ appointment.bis }}<br />
          Dauer: {{ appointment.Dauer }}h<br />
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { momentInstance } from "@/utils/moment";
import { useHead } from "@unhead/vue";
import {
  rosterWeekDay,
  rosterNumberDay,
  rosterNumberMonth,
} from "@/utils/dateFilters";
import settings from "@/settings";

export default {
  name: "Termine",
  props: {
    roster: null,
  },
  setup() {
    useHead({
      title: "Feuerwehr Mühltal Traisa | Dienstplan",
      meta: [
        { name: "title", content: "Feuerwehr Mühltal Traisa | Dienstplan" },
      ],
    });
  },
  data: () => {
    return {
      rosterData: null,
      type: {
        U: "Unterrricht",
        "U/P": "Unterrricht/Praxis",
        P: "Praxis",
        S: "Sonstiges",
        KatS: "Katastrophenschutz",
      },
    };
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
    rosterWeekDay,
    rosterNumberDay,
    rosterNumberMonth,
    isActive(appointment) {
      const tmpDate = appointment.Datum.split(".");
      const date = momentInstance(`${tmpDate[1]}.${tmpDate[0]}.${tmpDate[2]}`);
      const isSameDay = date.isSame(momentInstance(new Date()), "day");
      const isSameMonth = date.isSame(momentInstance(new Date()), "month");
      return isSameDay && isSameMonth;
    },
  },
};
</script>

<style scoped lang="scss">
.calendar {
  &__entry {
    border-bottom: dashed 1px #af4a45;
    padding-bottom: 10px;
    margin: 0;
  }
  &__body {
    border-top: solid 2px #af4a45;
    padding: 10px 15px;
  }
  &__date {
    margin-right: 15px;
    height: 90px;
    box-shadow: 0 0 5px 1px rgba(0, 0, 0, 0.1);
    max-width: fit-content;
    border: 5px solid #af4a45;
    text-align: center;
    padding: 10px 3px 0 3px;
    background: linear-gradient(
      to bottom,
      transparent,
      transparent 50%,
      #af4a45 50%,
      #af4a45
    );
    span:first-child {
      margin-top: -7px;
      font-size: 1.5rem;
      font-weight: 700;
      display: block;
      color: #af4a45;
    }
    span:last-child {
      margin-top: 7px;
      font-size: 1.5rem;
      font-weight: 700;
      display: block;
    }

    &--active {
      border: 5px solid #f16861;
      background: linear-gradient(
        to bottom,
        transparent,
        transparent 50%,
        #f16861 50%,
        #f16861
      );
      span:first-child {
        color: #f16861;
      }
    }
  }
}
</style>
