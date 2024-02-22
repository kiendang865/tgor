const state = {
    limit: 25,
    listServiceNiches: {
        data: [],
    },
    listServiceRoom: {
        data: [],
    },
    listServiceOther: {
        data: [],
    },
    listServiceRemarks: {
      data: [],
    },
    listDonation: {
      data: [],
    },
  };
  
  const getters = {};
  
  const actions = {
    getListServiceOther({commit, state}, params){
      params.limit = state.limit;
      return new Promise((resolve, reject) => {
        global
          .axios(`/booking`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListServiceOther", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListServiceRoom({commit, state}, params){
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/booking`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("updateListServiceRoom", response.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListServiceNiches({commit, state}, params){
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/booking`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("updateListServiceNiches", response.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      deleteService({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/service`, {
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
      getItemServiceBooking({commit, state}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/service-booking/${params.id}`, {
              method: "GET",
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              // commit("updateListServiceOther", response.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      exportNiches({commit, state}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/export`, {
              method: "GET",
              responseType: 'blob',
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              // commit("updateListServiceOther", response.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListRemarks({commit, state}, params){
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/remarks`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListRemarksSuccess", response.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      createRemarks({commit, state}, params){
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/remarks`, {
              method: "POST",
              data:params,
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
      updateRemarks({commit, state}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/remarks/${params.id}`, {
              method: "POST",
              data: params.formData,
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
      downloadRemarksFile({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/download-remarks/${params.id}`, {
              method: "GET",
              params,
              // responseType: 'blob',
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
      downloadRemarksZip({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/dowload-zip-remarks`, {
              method: "GET",
              params,
              responseType: 'blob',
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
      getListDonation({commit, state}, params){
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/get-donation/${params.id}`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("updateListDonation", response.data.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
  };
  
  const mutations = {
    updateListServiceOther(state,data) {
        state.listServiceOther = data
    },
    updateListServiceRoom(state,data) { 
        state.listServiceRoom = data
    },
    updateListServiceNiches(state,data) {
        state.listServiceNiches = data
    },
    getListRemarksSuccess(state,data) {
      state.listServiceRemarks = data
    },
    updateListDonation(state,data) {
      state.listDonation = data;
    }
  };
  
  export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
  };
  