import api from "../../api";
import * as types from "../mutation-types";

// initial state
const state = {
  pageDetails: {
    title: {},
    content: {},
    attached_images: []
  },
  isLoading: false
};

// getters
const getters = {
  pageDetails: state => state.pageDetails,
  isLoading: state => state.isLoading
};

// actions
const actions = {
  async getPageById({ commit }, id) {
    commit(types.UPDATE_LOADING_STATE, true);
    api.getPage(id, res => {
      commit(types.UPDATE_PAGE, res);
      commit(types.UPDATE_LOADING_STATE, false);
    });
  }
};

// mutations
const mutations = {
  [types.UPDATE_LOADING_STATE](state, isLoading) {
    state.isLoading = isLoading;
  },

  [types.UPDATE_PAGE](state, pageDetails) {
    state.pageDetails = pageDetails;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
