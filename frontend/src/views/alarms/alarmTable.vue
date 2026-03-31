<template>
  <div>
    <v-row no-gutters>
      <v-col>
        <Report>
          <template #headline>
            Einsätze
            <v-menu location="bottom" open-on-hover>
              <template #activator="{ props }">
                <div
                  v-bind="props"
                  style="display: inline-block; cursor: pointer"
                >
                  {{ selectedYear
                  }}<v-icon size="large" color="#fff">mdi-chevron-down</v-icon>
                </div>
              </template>
              <v-list>
                <v-list-item
                  v-for="year in selectableYears"
                  :key="year"
                  @click="() => selectYear(year)"
                >
                  <v-list-item-title> {{ year }} </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
          <template #content>
            <div v-if="!isLoading">
              <div v-if="alarms.length === 0">
                <v-row align="center">
                  <v-col cols="12" class="text-center">
                    <v-icon size="100" color="#af4a45">mdi-fire</v-icon>
                  </v-col>
                  <v-col cols="12" class="text-center">
                    <h2>Bisher wurden keine Einsätze gemeldet.</h2>
                  </v-col>
                </v-row>
              </div>
              <router-link
                v-for="(alarm, index) in alarms"
                :key="index"
                :to="'einsatze/' + alarm.post_name"
                class="removeLink"
              >
                <v-row class="mb-4">
                  <v-col cols="1">
                    <v-icon
                      v-if="alarm.einsatzicon === '1'"
                      color="#fff"
                      class="circle"
                      >mdi-fire</v-icon
                    >
                    <v-icon
                      v-if="alarm.einsatzicon === '2'"
                      color="#fff"
                      class="circle"
                      >mdi-hammer</v-icon
                    >
                    <v-icon
                      v-if="alarm.einsatzicon === '3'"
                      color="#fff"
                      class="circle"
                      >mdi-alarm-bell</v-icon
                    >
                    <v-icon
                      v-if="alarm.einsatzicon === '4'"
                      color="#fff"
                      class="circle"
                      >mdi-binoculars</v-icon
                    >
                  </v-col>
                  <v-col class="ml-3" style="overflow: hidden"
                    ><b>{{ alarm.post_title }}</b></v-col
                  >
                  <v-col
                    v-if="['lg', 'xl', 'md', 'xxl'].includes(breakpointName)"
                    cols="2"
                    ><b>{{
                      formatAlarmDate(alarm.alarmierungszeitpunkt.timestamp)
                    }}</b></v-col
                  >
                </v-row>
              </router-link>
            </div>
            <div v-else>
              <v-row v-for="index in 10" :key="index" align="center">
                <v-col>
                  <v-skeleton-loader
                    type="text"
                    color="transparent"
                    class="alarm-skeleton__text"
                  ></v-skeleton-loader>
                </v-col>
                <v-col cols="2">
                  <v-skeleton-loader
                    type="text"
                    color="transparent"
                    class="alarm-skeleton__text"
                  ></v-skeleton-loader>
                </v-col>
              </v-row>
            </div>
          </template>
        </Report>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { mapState, mapActions } from "pinia";
import { useAlarmsStore } from "@/store/modules/alarms";
import { useDisplay } from "vuetify";
import { useHead } from "@unhead/vue";
import { formatAlarmDate } from "@/utils/dateFilters";
import Report from "../../components/partials/Report/Report.vue";

export default {
  name: "AlarmTable",
  components: { Report },
  setup() {
    const { name } = useDisplay();
    useHead({
      title: "Feuerwehr Mühltal Traisa | Einsätze",
      meta: [{ name: "title", content: "Feuerwehr Mühltal Traisa | Einsätze" }],
    });
    return { breakpointName: name };
  },
  data() {
    return {
      firstYear: "2013",
      selectedYear: new Date().getFullYear().toString(),
    };
  },
  computed: {
    ...mapState(useAlarmsStore, ["alarms", "isLoading"]),
    selectableYears() {
      const currentYear = new Date().getFullYear();
      const firstYear = parseInt(this.firstYear);
      const years = [];
      for (let i = firstYear; i <= currentYear; i++) {
        years.push(i.toString());
      }
      return years;
    },
  },
  mounted() {
    this.getAllAlarmsFromYear(this.selectedYear);
  },
  methods: {
    ...mapActions(useAlarmsStore, ["getAllAlarmsFromYear"]),
    formatAlarmDate,
    selectYear(year) {
      this.selectedYear = year;
      this.getAllAlarmsFromYear(year);
    },
  },
};
</script>

<style>
.alarm-skeleton__text .v-skeleton-loader__text {
  background-color: rgba(0, 0, 0, 0.12) !important;
  height: 20px;
  border-radius: 4px;
}

.alarm-skeleton__text .v-skeleton-loader__paragraph {
  background-color: rgba(255, 255, 255, 0) !important;
  &::after {
    display: none;
  }
}
</style>

<style scoped lang="scss">
.circle {
  height: 30px;
  width: 30px;
  border-radius: 80px;
  box-shadow: 0 0 0 6px rgba(175, 74, 69, 0.4);
  overflow: hidden;
}
.removeLink {
  color: #000;
  text-decoration: none;
}
</style>
