//index.js
import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : {},
    isAuthenticated: localStorage.getItem('user') ? true : false,
  },

  getters: {

  },

  mutations: {
    storeUser(state, payload) {
      state.user = payload;
      state.isAuthenticated = true;
      console.log(payload)
      localStorage.setItem('user', JSON.stringify(payload));
    },
    logout(context){
      state.isAuthenticated = false;
    },
  },

  actions: {
    storeUser(context, payload) {
      context.commit('storeUser', payload);
    },
    logout(context){
      localStorage.clear();
      context.commit('logout');
    },
  }
});