
const state = {
  limit: 25,
  invoiceDetail: {},
  totalDetail: {},
  listInvoices: {
    data: []
  },
  listServiceInvoice: [],
};

const getters = {};

const actions = {
  generateInvoices({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/invoices`, {
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
  getDetailInvoices({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/invoices/${params.id}`, {
          method: "GET",
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          resolve(response.data.data);
          commit('getDetailInvoicesSuccess', response.data.data)
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  //   getSumTotal({commit},params){
  //     return new Promise((resolve, reject) => {
  //       global
  //         .axios(`/amount-count`, {
  //           method: "POST",
  //           data:params,
  //           headers: {
  //             Accept: "application/json",
  //           },
  //         })
  //         .then((response) => {
  //           resolve(response.data.data);
  //           commit('getSumTotalSuccess',response.data.data)
  //         })
  //         .catch((error) => {
  //           reject(error);
  //         });
  //     });
  //   },
  getListInvoices({ commit }, params) {
    params.limit = state.limit;
    return new Promise((resolve, reject) => {
      global
        .axios(`/invoices`, {
          method: "GET",
          params,
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          resolve(response.data.data);
          commit('getListInvoicesSuccess', response.data.data)
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  saveSignInvoices({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/save-signature-invoice/${params.id}`, {
          method: "POST",
          data: params.data,
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
  deleteInvoices({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/invoices`, {
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
  printInvoices({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`make-invoices/${params.id}`, {
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
  getListBookingAddInvoice({ commit }) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/list-booking-add-invoice`, {
          method: "GET",
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          commit("getListBookingAddInvoiceSuccess", response.data.data);
          resolve(true);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  addInvoices({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/add-invoices`, {
          method: "POST",
          data: params,
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
  },
  sendEmail({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`send-invoice/${params.id}`, {
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
  getTotalInvoice({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`total-invoice`, {
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
  getDetailInvoicesSuccess(state, data) {
    state.invoiceDetail = data
  },
  getSumTotalSuccess(state, data) {
    state.totalDetail = data
  },
  getListInvoicesSuccess(state, data) {
    state.listInvoices = data
  },
  getListBookingAddInvoiceSuccess(state, data) {
    state.listServiceInvoice = data;
  }

};

export default {
  namespaced: true,
  state,
  actions,
  mutations,
  getters,
};
