const state = {
    limit: 25,
    listAddress:[],
};

const getters = {


};

const actions = {
    getListAddress({commit, state}, params){ 
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/address`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("updateListAddress", response.data.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      createAddress({ commit }, params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/address`, {
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
      updateAddress({ commit }, params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/address/${params.id}`, {
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
      deleteAddress({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/address`, {
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
      getAddressDetail({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/detail-address/${params.id}`, {
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
      findAdress({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/find-address`, {
              method: "GET",
              params,
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
};

const mutations = {
    updateListAddress(state,data) {
        state.listAddress = data
    },

};

export default {
  namespaced: true,
  state,
  actions,
  mutations,
  getters
};
