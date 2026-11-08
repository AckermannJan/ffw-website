import { defineStore } from "pinia";
import api from "../../api";

export const useSideBarStore = defineStore("sideBar", {
  state: () => ({
    isLoading: false,
    data: {},
  }),
  getters: {
    getIsLoading: (state) => state.isLoading,
    latestAlarm: (state) => state.data.latestAlarm,
    nextThreeMeetings: (state) => state.data.nextThreeMeetings,
    sideBarPosts: (state) => state.data.sideBarPosts,
    // Landing page fields from wpTheme (see docs/landing-page-content.md).
    alarmCountThisYear: (state) => state.data.alarmCountThisYear,
    hero: (state) => state.data.hero,
    mitmachen: (state) => state.data.mitmachen,
  },
  actions: {
    getSidebarInfo() {
      this.isLoading = true;
      api.getSidebarInfo((info) => {
        if (info && !(info instanceof Error)) this.data = info;
        this.isLoading = false;
      });
    },
  },
});
