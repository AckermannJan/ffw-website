import { defineStore } from "pinia";
import api from "../../api";

export const useIndexStore = defineStore("index", {
  state: () => ({
    isLoading: false,
    pages: [],
  }),
  getters: {
    getIsLoading: (state) => state.isLoading,
    getPages: (state) => state.pages,
  },
  actions: {
    getIndexInfo() {
      this.isLoading = true;
      api.getIndexInfo((info) => {
        this.pages = info;
        this.isLoading = false;
      });
    },
  },
});
