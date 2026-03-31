import { defineStore } from "pinia";
import api from "../../api";

export const useArchiveStore = defineStore("archive", {
  state: () => ({
    archive: {},
    isLoading: false,
  }),
  getters: {
    archiveData: (state) => state.archive,
    getIsLoading: (state) => state.isLoading,
  },
  actions: {
    loadArchive() {
      this.isLoading = true;
      api.getArchive((res) => {
        this.archive = res;
        this.isLoading = false;
      });
    },
  },
});
