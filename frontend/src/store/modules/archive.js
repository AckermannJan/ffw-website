import api from "../../api";
import * as types from "../mutation-types";

// initial state
const state = {
  archive: {},
  isLoading: false
};

// getters
const getters = {
  archiveData: state => state.archive,
  isLoading: state => state.isLoading
};

// actions
const actions = {
  async loadArchive({ commit }) {
    commit(types.UPDATE_LOADING_STATE, true);
    api.getArchive(res => {
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
    state.archive = pageDetails;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
