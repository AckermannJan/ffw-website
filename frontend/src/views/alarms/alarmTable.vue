<template>
  <div>
    <v-row no-gutters>
      <v-col>
        <Report>
          <template v-slot:headline>
            Einsätze
            <v-menu offset-y open-on-hover>
              <template v-slot:activator="{ on }">
                <div v-on="on" style="display: inline-block; cursor: pointer">
                  {{ selectedYear
                  }}<v-icon large color="#fff">mdi-chevron-down</v-icon>
                </div>
              </template>
              <v-list>
                <v-list-item
                  v-for="year in selectableYears"
                  @click="() => selectYear(year)"
                  :key="year"
                >
                  <v-list-item-title> {{ year }} </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
          <template v-slot:content>
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
                :to="'einsatze/' + alarm.post_name"
                v-for="(alarm, index) in alarms"
                :key="index"
                class="removeLink"
              >
                <v-row>
                  <v-col cols="1">
                    <v-icon
                      color="#fff"
                      class="circle"
                      v-if="alarm.einsatzicon === '1'"
                      >mdi-fire</v-icon
                    >
                    <v-icon
                      color="#fff"
                      class="circle"
                      v-if="alarm.einsatzicon === '2'"
                      >mdi-hammer</v-icon
                    >
                    <v-icon
                      color="#fff"
                      class="circle"
                      v-if="alarm.einsatzicon === '3'"
                      >mdi-alarm-bell</v-icon
                    >
                    <v-icon
                      color="#fff"
                      class="circle"
                      v-if="alarm.einsatzicon === '4'"
                      >mdi-binoculars</v-icon
                    >
                  </v-col>
                  <v-col class="ml-3" style="overflow: hidden"
                    ><b>{{ alarm.post_title }}</b></v-col
                  >
                  <v-col
                    cols="2"
                    v-if="['lg', 'xl', 'md'].includes($vuetify.breakpoint.name)"
                    ><b>{{
                      alarm.alarmierungszeitpunkt.timestamp | date
                    }}</b></v-col
                  >
                </v-row>
              </router-link>
            </div>
            <div v-else>
              <v-row align="center" v-for="index in 10" :key="index">
                <v-col cols="1">
                  <v-skeleton-loader
                    type="image"
                    style="height: 40px"
                  ></v-skeleton-loader>
                </v-col>
                <v-col>
                  <v-skeleton-loader type="text"></v-skeleton-loader>
                </v-col>
                <v-col cols="2">
                  <v-skeleton-loader type="text"></v-skeleton-loader>
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
import { mapActions, mapGetters } from "vuex";
import Report from "../../components/partials/Report/Report";
import { momentInstance } from "@/utils/moment";

export default {
  name: "alarmTable",
  components: { Report },
  filters: {
    date(date) {
      momentInstance().locale("de");
      return momentInstance(parseInt(date) * 1000).format("DD.MM.YYYY");
    }
  },
  computed: {
    ...mapGetters("alarms", {
      alarms: "alarms",
      isLoading: "isLoading"
    }),
    selectableYears() {
      const currentYear = new Date().getFullYear();
      const firstYear = parseInt(this.firstYear);
      const years = [];
      for (let i = firstYear; i <= currentYear; i++) {
        years.push(i.toString());
      }
      return years;
    }
  },
  data() {
    return {
      firstYear: "2013",
      selectedYear: new Date().getFullYear().toString()
    };
  },
  mounted() {
    this.getAllAlarmsFromYear(this.selectedYear);
  },
  methods: {
    ...mapActions("alarms", {
      getAllAlarmsFromYear: "getAllAlarmsFromYear"
    }),
    selectYear(year) {
      this.selectedYear = year;
      this.getAllAlarmsFromYear(year);
    }
  },
  metaInfo() {
    return {
      title: "Feuerwehr Mühltal Traisa | Einsätze",
      meta: [
        {
          name: "title",
          content: "Feuerwehr Mühltal Traisa | Einsätze"
        }
      ]
    };
  }
};
</script>

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
