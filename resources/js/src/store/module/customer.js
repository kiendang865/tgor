const state = {
    customerDetail: null,
    limit: 25,
    listCustomer: {
      data: [],
    },
    listTypeSalutation:[],
    listTypeReligion:[],
    listTypeContact:[],    
    listTypeTGOR:[],
  };
  
  const getters = {};
  
  const actions = {
    getListCustomer({commit, state}, params){
      params.limit = 300;
      return new Promise((resolve, reject) => {
        global
          .axios(`/customer`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListCustomer", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    deleteCustomer({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/customer`, {
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
      getListTypeSalutation({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["salutation"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("listTypeSalutationSuccess", response.data.data);
              resolve(true);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListTypeReligion({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["religion"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListTypeReligionSuccess", response.data.data);
              resolve(true);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListTypeContact({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["preferred_contact_by"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListTypeContactSuccess", response.data.data);
              resolve(true);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListTypeTGOR({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["is_tgor"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListTypeTGORSuccess", response.data.data);
              resolve(true);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      createCustomer({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/customer`, {
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
      customerDetail({ commit },params) { 
        return new Promise((resolve, reject) => {
          global
            .axios(`/customer/${params}`, {
              method: "GET",
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('customerDtailSuccess',response.data.data);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      updateCustomer({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/customer/${params.id}`, {
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
    updateListCustomer(state,data){
        state.listCustomer = data;
    },
    listTypeSalutationSuccess(state,data){
        state.listTypeSalutation = data
    },
    getListTypeReligionSuccess(state,data){
        state.listTypeReligion = data
    },
    getListTypeContactSuccess(state,data){
        state.listTypeContact = data
    },
    getListTypeTGORSuccess(state,data){
        state.listTypeTGOR = data
    },
    customerDtailSuccess(state,data){
        state.customerDetail = data
    }
  };
  
  export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
  };
  