
const state = {
    limit: 25,
    paymentDetail:{},
    totalDetail:{},
    listPayment:{
      data:[]
    },
    listPaymentMode:[],
    partialPayment: {}
};
    
    const getters = {};
    
    const actions = {
      generatePayment({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/payment`, {
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
      getDetailPayment({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/payment/${params.id}`, {
              method: "GET",
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('getDetailPaymentSuccess',response.data.data)
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListPayment({commit},params){
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/payment`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data);
              commit('getListPaymentSuccess',response.data)
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      saveSignPayment({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/update-payment/${params.id}`, {
              method: "POST",
              data:params.data,
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
      deletePayment({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/payment`, {
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
      getListPaymentMode({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["payment_mode"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListPaymentModeSuccess", response.data.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      printPayment({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`make-payment/${params.id}`, {
              method: "GET",
              responseType: 'arraybuffer',
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
      sendPayment({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`send-payment/${params.id}`, {
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
      getDetailPartialPayment({commit},id){
        return new Promise((resolve, reject) => {
          global
            .axios(`/partial-payment/${id}`, {
              method: "GET",
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('getDetailPartialPaymentSuccess',response.data.data)
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      createPartialPayment({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/partial-payment`, {
              method: "POST",
              data:params.data,
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
      sendPartialPayment({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`send-partial-payment/${params.id}`, {
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
      printPartialPayment({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`print-partial-payment/${params.id}`, {
              method: "GET",
              responseType: 'arraybuffer',
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
      updatePartialPayment({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/update-partial-payment/${params.id}`, {
              method: "POST",
              data:params.data,
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
      deletePartialPayment({commit}, id){
        return new Promise((resolve, reject) => {
          global
            .axios(`/delete-partial-payment/${id}`, {
              method: "DELETE",
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
      getListPaymentSuccess(state,data) { 
        state.listPayment = data.data
      },
      getDetailPaymentSuccess(state,data) {
        state.paymentDetail = data
      },
      getListPaymentModeSuccess(state,data) {
        state.listPaymentMode = data
      },
      getDetailPartialPaymentSuccess(state, data){
        state.partialPayment = data
      }
    };
    
    export default {
      namespaced: true,
      state,
      actions,
      mutations,
      getters,
    };
    