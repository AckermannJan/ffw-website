import { defineStore } from "pinia";
import api from "../../api";

export const useTermineStore = defineStore("termine", {
  state: () => ({
    isLoading: false,
    data: [],
  }),
  getters: {
    getIsLoading: (state) => state.isLoading,
    termine: (state) => state.data,
  },
  actions: {
    getAllMeetings() {
      this.isLoading = true;
      api.getAllMeetings((info) => {
        this.data = info;
        this.isLoading = false;
      });
    },
  },
});
