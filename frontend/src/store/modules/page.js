import { defineStore } from "pinia";
import api from "../../api";

export const usePageStore = defineStore("page", {
  state: () => ({
    pageDetails: {
      title: {},
      content: {},
      attached_images: []
    },
    isLoading: false
  }),
  getters: {
    getPageDetails: state => state.pageDetails,
    getIsLoading: state => state.isLoading
  },
  actions: {
    getPageById(id) {
      this.isLoading = true;
      api.getPage(id, res => {
        this.pageDetails = res;
        this.isLoading = false;
      });
    }
  }
});
