const state = {
  listTypeNiches: [],
  nicheDetail: null,
  limit: 25,
  listNiches: {
    data: [],
  },
  listNicheCategory:[],
  listNicheForBooking:[],
  listDuration:[],
  durationOfNiche:[]
};

const getters = {};

const actions = {
  getListNiches({commit, state}, params){
    params.limit = state.limit;
    return new Promise((resolve, reject) => {
      global
        .axios(`/niche`, {
          method: "GET",
          params,
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          commit("updateListNiches", response.data);
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  getListTypeNiches({ commit }) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/reference`, {
          method: "GET",
          params: {
            reference_type: ["type_niche"],
          },
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          commit("getListTypeNichesSuccess", response.data.data);
          resolve(true);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  getListCategoryNiche({ commit }) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/reference`, {
          method: "GET",
          params: {
            reference_type: ["category_niche"],
          },
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          commit("getListCategoryNicheSuccess", response.data.data);
          resolve(true);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  createNiche({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/niche`, {
          method: "POST",
          data: params,
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
            commit('updateNicheDetail', response.data.data)
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  updateNiche({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/niche/${params.id}`, {
          method: "PUT",
          data: params,
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
        commit('updateNicheDetail', response.data.data)
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  getNicheDetail({commit}, params){
    return new Promise((resolve, reject) => {
      global
        .axios(`/niche/${params.id}`, {
          method: "GET",
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          resolve(response);
          commit("updateNicheDetail", response.data.data);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  deleteNiches({commit}, params){
    return new Promise((resolve, reject) => {
      global
        .axios(`/niche`, {
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
  getListNichForBooking({commit}, params){
    return new Promise((resolve, reject) => {
      global
        .axios(`/niche-booking`, {
          method: "GET",
          params,
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          resolve(response)
          commit('updateNicheForBookingSuccess', response.data.data)
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  sumTotalNiches({commit}, params){
    return new Promise((resolve, reject) => {
      global
        .axios(`/niches-total`, {
          method: "POST",
          data: params,
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          resolve(response)
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  getListDuration({commit, state}, params){ 
    params.limit = state.limit;
    return new Promise((resolve, reject) => {
      global
        .axios(`/duration-niches`, {
          method: "GET",
          params,
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          commit("updateListDuration", response.data);
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  createDurationNiches({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/duration-niches`, {
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
  updateDurationNiches({ commit }, params) {
    return new Promise((resolve, reject) => {
      global
        .axios(`/duration-niches/${params.id}`, {
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
  deleteDuration({commit}, params){
    return new Promise((resolve, reject) => {
      global
        .axios(`/duration-niches`, {
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
  getDurationOfNiche({commit}, params){
    return new Promise((resolve, reject) => {
      global
        .axios(`/list-duration-niches`, {
          method: "GET",
          params,
          headers: {
            Accept: "application/json",
          },
        })
        .then((response) => {
          commit("getDurationOfNicheSuccess", response.data.data);
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  createBulkAction({commit}, params){
    return new Promise((resolve, reject) => {
      global
        .axios(`/bulk-action`, {
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
  exportNichesForAdmin({commit, state}, params){
    return new Promise((resolve, reject) => {
      global
        .axios(`/export-niche`, {
          method: "POST",
          data: params,
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
  getIdAllNichesFilter({commit, state}, params){
    return new Promise((resolve, reject) => {
      global
        .axios(`/all-id-niche`, {
          method: "GET",
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
};

const mutations = {
  getListTypeNichesSuccess(state, data) {
    state.listTypeNiches = data;
  },
  updateNicheDetail(state, data){
    state.nicheDetail = data;
  },
  updateListNiches(state, data){
    state.listNiches = data
  },
  getListCategoryNicheSuccess(state,data) {
    state.listNicheCategory = data
  },
  updateNicheForBookingSuccess(state,data) {
    state.listNicheForBooking = data
  },
  updateListDuration(state,data) {
    state.listDuration = data
  },
  getDurationOfNicheSuccess(state,data) {
    state.durationOfNiche = data
  }
  
};

export default {
  namespaced: true,
  state,
  actions,
  mutations,
  getters,
};
