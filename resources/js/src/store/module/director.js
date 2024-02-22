const state = {
    directorDetail: null,
    limit: 25,
    listDirector: {
      data: [],
    },
  };
  
  const getters = {};
  
  const actions = {
    getListDirector({commit, state}, params){
      params.limit = state.limit;
      return new Promise((resolve, reject) => {
        global
          .axios(`/directors`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListDirector", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    deleteDirector({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/directors`, {
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
      createDirector({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/directors`, {
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
      directorDetail({ commit },params) { 
        return new Promise((resolve, reject) => {
          global
            .axios(`/directors/${params}`, {
              method: "GET",
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('updateListDirector',response.data.data);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      updateDirector({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/directors/${params.id}`, {
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
    updateListDirector(state,data){
        state.listDirector = data;
    },
    directorDtailSuccess(state,data){
        state.directorDetail = data
    }
  };
  
  export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
  };
  