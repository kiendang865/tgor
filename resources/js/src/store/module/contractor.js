const state = {
    contractorDetail: null,
    limit: 25,
    listContractor: {
      data: [],
    },
    listTypeBooking:[]
  };
  
  const getters = {};
  
  const actions = {
    getListContractor({commit, state}, params){
      params.limit = state.limit;
      return new Promise((resolve, reject) => {
        global
          .axios(`/contractor`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListContractor", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListTypeBooking({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["booking_type"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListTypeBookingSuccess", response.data.data);
              resolve(true);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
    deleteContractor({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/contractor`, {
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
      createContractor({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/contractor`, {
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
      contractorDetail({ commit },params) { 
        return new Promise((resolve, reject) => {
          global
            .axios(`/contractor/${params}`, {
              method: "GET",
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('contractorDtailSuccess',response.data.data);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      updateContractor({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/contractor/${params.id}`, {
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
      
  };
  
  const mutations = {
    updateListContractor(state,data){
        state.listContractor = data;
    },
    contractorDtailSuccess(state,data){
        state.contractorDetail = data
    },
    getListTypeBookingSuccess(state,data){
        state.listTypeBooking = data
    }
  };
  
  export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
  };
  