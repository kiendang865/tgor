const state = {
    limit: 25,
    listReport: {
      data: [],
    },
    listServiceReport: [],
  };
  
  const getters = {};
  
  const actions = {
    getListReport({commit, state}, params){
      params.limit = state.limit;
      return new Promise((resolve, reject) => {
        global
          .axios(`/report`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListReport", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    createReport({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/report`, {
            method: "POST",
            data: params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListReport", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    addReport({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/report`, {
            method: "POST",
            data: params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListReport", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    updateReport({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/report/${params.id}`, {
            method: "POST",
            data: params.data,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListReport", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    deleteReport({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/report`, {
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
    getListServiceReport({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["service_report"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListServiceReportSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    downloadReportFile({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/generate-report/${params.id}`, {
            method: "GET",
            responseType: 'blob',
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            resolve(response.data);
          })
          .catch((error) => {
            reject(error);
          });
      });
    }
  }
  
  const mutations = {
    updateListReport(state, data){
      state.listReport = data
    },
    getListServiceReportSuccess(state, data){
      state.listServiceReport = data;
    }
  };
  
  export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
  };
  