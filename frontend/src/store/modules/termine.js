import api from "../../api";
import * as types from "../mutation-types";

// initial state
const state = {
  isLoading: false,
  data: []
};

// getters
const getters = {
  isLoading: state => state.isLoading,
  termine: state => state.data
};

// actions
const actions = {
  getAllMeetings({ commit }) {
    commit(types.UPDATE_LOADING_STATE, true);
    api.getAllMeetings(info => {
      commit(types.STORE_FETCHED_TERMINE, info);
      commit(types.UPDATE_LOADING_STATE, false);
    });
  }
};

// mutations
const mutations = {
  [types.STORE_FETCHED_TERMINE](state, data) {
    state.data = data;
  },

  [types.UPDATE_LOADING_STATE](state, val) {
    state.isLoading = val;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
