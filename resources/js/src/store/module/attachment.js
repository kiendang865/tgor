
const state = {
    limit: 25,
    listAttachment:{}
};
    
    const getters = {};
    
    const actions = {
      addAttachment({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/attachment`, {
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
      getListAttachment({commit},params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/attachment`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('getListAttachmentSuccess',response.data.data)
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      deleteAttachment({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/attachment`, {
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
      downloadAttachment({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/download-attachment`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
    };
    
    const mutations = {
      getListAttachmentSuccess(state,data) {
        state.listAttachment = data
      }
    };
    
    export default {
      namespaced: true,
      state,
      actions,
      mutations,
      getters,
    };
    