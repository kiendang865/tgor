const state = {
    limit: 25,
    listRoom: {
      data: [],
    },
    listTypeTV:[],
    roomDetail:null,
    listStatusRoom:[],
    listRoomForBooking:[]
  };
  
  const getters = {};
  
  const actions = {
    getListRoom({commit, state}, params){
      params.limit = state.limit;
      return new Promise((resolve, reject) => {
        global
          .axios(`/memorial-room`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListRoom", response.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListTypeTV({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["tv_dimention"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListTypeTVSuccess", response.data.data);
              resolve(true);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      roomDetail({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/memorial-room/${params}`, {
              method: "GET",
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              resolve(response.data.data);
              commit('roomDtailSuccess',response.data.data);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      updateRoom({ commit },params) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/memorial-room/${params.id}`, {
              method: "PUT",
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
      deleteRoom({commit}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/memorial-room`, {
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
      createRoom({ commit }, params) {
        return new Promise((resolve, reject) => {
          global
          .axios(`/memorial-room`, {
              method: "POST",
              data: params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
                commit('roomDtailSuccess', response.data.data)
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListStatusAdminRoom({ commit }) {
        return new Promise((resolve, reject) => {
          global
            .axios(`/reference`, {
              method: "GET",
              params: {
                reference_type: ["admin_room_status"],
              },
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("getListStatusAdminRoomSuccess", response.data.data);
              resolve(true);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
      getListRoomForBooking({commit, state}, params){
        return new Promise((resolve, reject) => {
          global
            .axios(`/list-room`, {
              method: "GET",
              params,
              headers: {
                Accept: "application/json",
              },
            })
            .then((response) => {
              commit("updateListRoomForBooking", response.data.data);
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
        });
      },
  }
  
  const mutations = {
    updateListRoom(state, data){
      state.listRoom = data
    },
    getListTypeTVSuccess(state,data){
        state.listTypeTV = data
    },
    roomDtailSuccess(state,data){
      state.roomDetail = data
    },
    getListStatusAdminRoomSuccess(state,data) {
      state.listStatusRoom = data
    },
    updateListRoomForBooking(state,data) {
      state.listRoomForBooking = data
    }
  };
  
  export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
  };
  