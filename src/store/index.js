import Vue from 'vue'
import Vuex from 'vuex'

import cards from './cards'

Vue.use(Vuex)

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Store instance.
 */

export default function (/* { ssrContext } */) {
  const Store = new Vuex.Store({
    modules: {
      cards
    },
    state: {
      token: null
    },
    mutations: {
      setToken(state, token) {
        state.token = token;
      },
      clearToken(state) {
        state.token = null;
      }
    },
    actions: {
      saveToken({ commit }, token) {
        localStorage.setItem('auth_token', token);
        commit('setToken', token);
      },
      clearToken({ commit }) {
        localStorage.removeItem('auth_token');
        commit('clearToken');
      }
    },
    strict: process.env.DEBUGGING
  });

  return Store;
}

