const state = {
    limit: 25,
    listnicheReserved:[],
};

const getters = {


};

const actions = {
    getListNichesReserved({commit, state}, params){ 
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/niche-reserved`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("updateListNichesReserved", response.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      createNicheReserved({ commit }, params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/niche-reserved`, {
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
      updateNicheReserved({ commit }, params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/niche-reserved/${params.id}`, {
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
      deleteNicheReserved({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/niche-reserved`, {
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
      getNicheReservedDetail({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/niche-reserved/${params.id}`, {
              method: "GET",
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
    //   getListAmountType({ commit }) {
    //     return new Promise((resolve, reject) => {
    //       global
    //         .axios(`/reference`, {
    //           method: "GET",
    //           params: {
    //             reference_type: ["amount_type"],
    //           },
    //           headers: {
    //             Accept: "application/json",
    //           },
    //         })
    //         .then((response) => {
    //           commit("getListAmountTypeSuccess", response.data.data);
    //           resolve(true);
    //         })
    //         .catch((error) => {
    //           reject(error);
    //         });
    //     });
    //   },
    //   getDiscountDetail({ commit },params) {
    //     return new Promise((resolve, reject) => {
    //       global
    //         .axios(`/discount-detail/${params.id}`, {
    //           method: "GET",
    //           params,
    //           headers: {
    //             Accept: "application/json",
    //           },
    //         })
    //         .then((response) => {
    //           commit("getdiscountDetailSuccess", response.data.data);
    //           resolve(true);
    //         })
    //         .catch((error) => {
    //           reject(error);
    //         });
    //     });
    //   },
};

const mutations = {
    updateListNichesReserved(state,data) {
        state.listnicheReserved = data
    },
    // getListDiscountTypeSuccess(state,data) {
    //     state.listDiscountType = data
    // },
    // getListAmountTypeSuccess(state,data) {
    //     state.listAmountType = data
    // },
    // getdiscountDetailSuccess(state,data) {
    //     state.discountDetail = data
    // }

};

export default {
  namespaced: true,
  state,
  actions,
  mutations,
  getters
};
