import { defineStore } from "pinia";
import api from "../../api";

export const useAlarmsStore = defineStore("alarms", {
  state: () => ({
    isLoading: false,
    isAlarmLoading: false,
    alarms: [],
    alarm: {
      alarmierungszeitpunkt: {},
      fahrzeuge: [],
      medienbilder: [],
    },
  }),
  getters: {
    getIsLoading: (state) => state.isLoading,
    getAlarms: (state) => state.alarms,
    getIsAlarmLoading: (state) => state.isAlarmLoading,
    getAlarmData: (state) => state.alarm,
  },
  actions: {
    getAllAlarmsFromYear(year) {
      this.isLoading = true;
      api.getAllAlarmsFromYear(year, (info) => {
        this.alarms = info;
        this.isLoading = false;
      });
    },
    getAlarm(slug) {
      this.isAlarmLoading = true;
      api.getAlarm(slug, (info) => {
        this.alarm = info;
        this.isAlarmLoading = false;
      });
    },
  },
});
