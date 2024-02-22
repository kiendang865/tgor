import {

} from "../actions.type";
import { 

} from "../mutations.type";
import router from "../../router";

const state = {
  errors: null,
  user : {},
  isLogin : false,
};

const getters = {


};

const actions = {
  login({commit},params){
    return new Promise (( resolve, reject ) => {
      global.axios(`/auth/login`, { 
        method: "POST",
        data: params,
        headers: {
          Accept: "application/json",
        },
      })
      .then(response => {
             commit( 'loginSuccess', response.data.data);
              localStorage.setItem("token_admin", response.data.data.access_token)
              localStorage.setItem("admin_profile", JSON.stringify(response.data.data))
             resolve( response.data.data );
       })
      .catch(error => {
          reject(error)
      });
           
    });
  },
  logout({commit}, params){

    return new Promise (( resolve, reject ) => {
      global.axios(`/auth/logout`, { 
        method: "POST",
        data: params,
        headers: {
          Accept: "application/json",
        },
      })
      .then(response => {
        localStorage.removeItem("token_admin")
        localStorage.removeItem("admin_profile")
        commit('logoutSuccess')
        resolve(true);
       })
       .catch( (error) => {
        // handle error
        reject(error)
      })
           
    });
  }
};

const mutations = {
  loginSuccess (state,data) {
    state.user = data
    state.isLogin = true
  },
  logoutSuccess(state){
    state.user = {};
    state.isLogin = false;

  }

};

export default {
  namespaced: true,
  state,
  actions,
  mutations,
  getters
};
