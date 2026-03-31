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
