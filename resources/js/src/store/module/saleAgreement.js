
const state = {
    limit: 25,
    saleDetail:{},
    totalDetail:{
      amount:0,
      gst_amount:0,
      discount:0,
      total_amount:0
    },
    listSaleAgreement:{
      data:[]
    },
    listAgreement:[]
};
    
    const getters = {};
    
    const actions = {
      caculateChageBooking({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/sale-agreement`, {
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
      getDetailSaleAreement({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/sale-agreement/${params.id}`, {
              method: "GET",
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('getDetailSaleAreementSuccess',response.data.data)
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getSumTotal({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/amount-count`, {
              method: "POST",
              data:params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('getSumTotalSuccess',response.data.data)
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListSaleAreement({commit},params){
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/sale-agreement`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('getListSaleAreementSuccess',response.data.data)
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      saveSaleArgeement({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/save-signature/${params.id}`, {
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
      deleteSaleAgreement({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/sale-agreement`, {
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
      getSumDonate({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/amount-donate`, {
              method: "POST",
              data:params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('getSumTotalSuccess',response.data.data)
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      printNichesLicense({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`make-niche-licence/${params.id}`, {
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
      printAgreement({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`print-document/${params.id}`, {
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
      listAgreementForInvoice({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`list-agreement`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit('getListAgreementForInvoiceSuccess',response.data.data)
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      addAgreementForInvoice({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/add-agreement`, {
              method: "POST",
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
     deleteAgreementForInvoice({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/delete-agreement-line`, {
              method: "DELETE",
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
      sendAgreement({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`send-document/${params.id}`, {
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
      handleTotalSaleAgreement({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`total-sale-agreement`, {
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
      }
    };
    
    const mutations = {
        getDetailSaleAreementSuccess(state,data){
            state.saleDetail = data
        },
        getSumTotalSuccess(state,data){
          state.totalDetail = data
        },
        emptySumTotal(state,data){
          state.totalDetail = {}
        },
        getListSaleAreementSuccess(state,data) {
          state.listSaleAgreement = data
        },
        updateTotal(state,data) {
          state.totalDetail = data
        },
        getListAgreementForInvoiceSuccess(state,data) {
          state.listAgreement = data
        }
    };
    
    export default {
      namespaced: true,
      state,
      actions,
      mutations,
      getters,
    };
    