const state = {
    userDetail: null,
    limit: 25,
    listUser: {
      data: [],
    },
    listStatusAdmin:[],
    listContactPerson:[]
  };
  
  const getters = {};
  
  const actions = {
    getListUser({commit, state}, params){
      params.limit = state.limit;
      return new Promise((resolve, reject) => {
        global
          .axios(`/admin`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListUser", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    deleteUser({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/admin`, {
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
      createUser({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/admin`, {
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
      userDetail({ commit },params) { 
        return new Promise((resolve, reject) => {
          global
            .axios(`/admin/${params}`, {
              method: "GET",
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('UserDtailSuccess',response.data.data);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      updateUser({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/admin/${params.id}`, {
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
      getListStatusAdmin({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["admin_status"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListStatusAdminSuccess", response.data.data);
              resolve(true);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getMe({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/me`, {
              method: "POST",
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              localStorage.setItem("admin_profile", JSON.stringify(response.data.data))
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListContactPerson({commit, state}, params){
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/contact-person`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("updateListContactPerson", response.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      createContactPerson({ commit }, params) { 
        return new Promise((resolve, reject) => {
          global
            .axios(`/contact-person`, {
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
      updateContactPerson({ commit }, params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/contact-person/${params.id}`, {
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
      deleteContactPerson({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/contact-person`, {
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
      forgetPassword({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/forget-password`, {
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
      confirmPassword({ commit },params) { 
        return new Promise((resolve, reject) => {
          global
            .axios(`/confirm/password/${params.code}`, {
              method: "GET",
              params: params, 
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
      userDonate({ commit },params) { 
        return new Promise((resolve, reject) => {
          global
            .axios(`/donate`, {
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
  };
  
  const mutations = {
    updateListUser(state,data){
        state.listUser = data;
    },
    UserDtailSuccess(state,data){
        state.UserDetail = data
    },
    getListStatusAdminSuccess(state,data){
        state.listStatusAdmin = data
    },
    updateListContactPerson(state,data) {
        state.listContactPerson = data
    }
  };
  
  export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
  };
  