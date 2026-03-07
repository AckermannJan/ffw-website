import Vue from "vue";
import Vuex from "vuex";
import sideBar from "./modules/sideBar";
import index from "./modules/index";
import page from "./modules/page";
import termine from "./modules/termine";
import alarms from "./modules/alarms";
import archive from "./modules/archive";
import * as actions from "./actions";
import * as getters from "./getters";

Vue.use(Vuex);

export default new Vuex.Store({
  namespaced: true,
  actions,
  getters,
  modules: {
    sideBar,
    index,
    page,
    termine,
    archive,
    alarms
  }
});
