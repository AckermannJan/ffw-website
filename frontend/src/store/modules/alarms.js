import api from "../../api";
import * as types from "../mutation-types";

// initial state
const state = {
  isLoading: false,
  isAlarmLoading: false,
  alarms: [],
  alarm: {
    alarmierungszeitpunkt: {},
    fahrzeuge: [],
    medienbilder: []
  }
};

// getters
const getters = {
  isLoading: state => state.isLoading,
  alarms: state => state.alarms,
  isAlarmLoading: state => state.isAlarmLoading,
  alarm: state => state.alarm
};

// actions
const actions = {
  getAllAlarmsFromYear({ commit }, year) {
    commit(types.UPDATE_LOADING_STATE, true);
    api.getAllAlarmsFromYear(year, info => {
      commit(types.STORE_FETCHED_ALARMS, info);
      commit(types.UPDATE_LOADING_STATE, false);
    });
  },
  getAlarm({ commit }, year) {
    commit(types.UPDATE_LOADING_STATE_ALARM, true);
    api.getAlarm(year, info => {
      commit(types.STORE_FETCHED_ALARM, info);
      commit(types.UPDATE_LOADING_STATE_ALARM, false);
    });
  }
};

// mutations
const mutations = {
  [types.STORE_FETCHED_ALARMS](state, alarms) {
    state.alarms = alarms;
  },

  [types.STORE_FETCHED_ALARM](state, alarm) {
    state.alarm = alarm;
  },

  [types.UPDATE_LOADING_STATE](state, val) {
    state.isLoading = val;
  },

  [types.UPDATE_LOADING_STATE_ALARM](state, val) {
    state.isAlarmLoading = val;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
