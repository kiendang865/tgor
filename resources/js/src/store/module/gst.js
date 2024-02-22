const state = {
    limit: 25,
    listGST:[],
};

const getters = {


};

const actions = {
    getListGST({commit, state}, params){ 
        params.limit = state.limit;
        return new Promise((resolve, reject) => {
          global
            .axios(`/history-gst-rate`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("updateListGST", response.data.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
    },
    createGST({ commit }, params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/gst-rate`, {
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
    gstdetail({ commit }, params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/gst-rate`+ (params?.gst_id ? `?gst_id=${params.gst_id}` : ``), {
              method: "GET",
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
    updateListGST(state,data) {
        state.listGST = data
    },

};

export default {
  namespaced: true,
  state,
  actions,
  mutations,
  getters
};
