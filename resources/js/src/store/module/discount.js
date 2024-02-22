import {

} from "../actions.type";
import { 

} from "../mutations.type";
import router from "../../router";

const state = {
    limit: 25,
    listDiscount:[],
    listDiscountType:[],
    listAmountType:[],
    discountDetail:{}
};

const getters = {


};

const actions = {
    getListDicount({commit, state}, params){ 
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/discount-niche`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("updateListDiscount", response.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      createDicountNiches({ commit }, params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/discount-niche`, {
              method: "POST",
              data: params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
             
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      updateDicountNiches({ commit }, params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/discount-niche/${params.id}`, {
              method: "PUT",
              data: params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
           
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      deleteDicount({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/discount-niche`, {
              method: "DELETE",
              data: params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListDiscountType({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["discount_type"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListDiscountTypeSuccess", response.data.data);
              resolve(true);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListAmountType({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["amount_type"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListAmountTypeSuccess", response.data.data);
              resolve(true);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getDiscountDetail({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/discount-detail/${params.id}`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getdiscountDetailSuccess", response.data.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
};

const mutations = {
    updateListDiscount(state,data) {
        state.listDiscount = data
    },
    getListDiscountTypeSuccess(state,data) {
        state.listDiscountType = data
    },
    getListAmountTypeSuccess(state,data) {
        state.listAmountType = data
    },
    getdiscountDetailSuccess(state,data) {
        state.discountDetail = data
    }

};

export default {
  namespaced: true,
  state,
  actions,
  mutations,
  getters
};
