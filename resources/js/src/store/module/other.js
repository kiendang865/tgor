const state = {
    otherDetail: null,
    limit: 25,
    listOther: {
      data: [],
    },
    listTypeOther:[],
    listContractorRequired:[],
    otherByContractor:[],
    listServiceType:[],
    listOtherStatus:[],
    listOtherCategory: []
  };
  
  const getters = {};
  
  const actions = {
    getListOther({commit, state}, params){
      params.limit = state.limit;
      return new Promise((resolve, reject) => {
        global
          .axios(`/other`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListOther", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListTypeOther({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["other_type"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListTypeOtherSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    createOther({ commit }, params) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/other`, {
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
    updateOther({ commit }, params) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/other/${params.id}`, {
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
    getOtherDetail({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/other/${params}`, {
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
    deleteOther({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/other`, {
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
    getListContractorRequired({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["contractor_required"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListContractorRequiredSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListOtherByContractRequired({commit}) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/other-by-contractor`, {
            method: "GET",
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListOtherByContractRequiredSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListServiceType({commit, state}, params){
      params.limit = state.limit;
      return new Promise((resolve, reject) => {
        global
          .axios(`/service-type`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListServiceTypeSuccess", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    createServiceType({ commit }, params) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/service-type`, {
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
    updateServiceType({ commit }, params) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/service-type/${params.id}`, {
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
    deleteServiceType({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/service-type`, {
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
    getListOtherStatus({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["other_status"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListOtherStatusSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListOtherCategory({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/list-other-category`, {
            method: "GET",
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListOtherCategorySuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
  };
  
  const mutations = {
    updateListOther(state,data){
        state.listOther = data;
    },
    getListTypeOtherSuccess(state,data){
        state.listTypeOther = data
    },
    getListContractorRequiredSuccess(state,data) {
        state.listContractorRequired = data
    },
    getListOtherByContractRequiredSuccess(state,data) {
        state.otherByContractor = data
    },
    getListServiceTypeSuccess(state,data) {
        state.listServiceType = data
    },
    getListOtherStatusSuccess(state,data) {
      state.listOtherStatus = data
    },
    getListOtherCategorySuccess(state,data) {
      state.listOtherCategory = data
    }
    
  };
  
  export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
  };
  