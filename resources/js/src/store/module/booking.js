const state = {
  listRate:[],
  listRelationship:[],
  listRoomType:[],
  listDirector:[],
  listOthers:[],
  listAllContracotr:[],
  listDuration:[],
  listCoLisesen:[],
  listReferral:[],
  listRelationshipColisense:[],
  listBookingLog:{
    data:[]
  },
  arr_niches:[],
  arr_rooms:[],
  arr_others:[],
  listEvent:[],
  listServiceBooking:{
    data:[]
  },
  listBookingGeneral:{
    data:[]
  },
  listStatusNiches:[],
  listStatusRoom:[],
  listStatusOther:[],
  listStatusBooking:[],
  clientAddress: null,
  clientPostalCode: null,
  listServiceByType: []
};
  
  const getters = {};
  
  const actions = {
    getListRate({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/gst-rate`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("updateListRate", response.data.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListRelationship({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["relationship_to_applicant"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListTypeRelationshipSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    createBooking({ commit },params) {
      return new Promise((resolve, reject) => {
        axios.post('/booking',params).then((response) => {
              commit("setClientAddress", response.data.data)
              commit("setClientPostalCode", response.data.data)
              resolve(response);
            })
            .catch((error) => {
              reject(error);
            });
      });
    },
    getListRoomType({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["room_type"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListRoomTypeSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListDirector({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/list-director`, {
            method: "GET",
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListDirectorSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListService({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/list-service`, {
            method: "GET",
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListServiceSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListServiceByType({ commit }, id) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/list-service-by-type/${id}`, {
            method: "GET",
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            resolve(true);
            commit("getListServiceByTypeSuccess", response.data.data);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListAllContractor({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/list-contractor`, {
            method: "GET",
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListAllContractorSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListDuration({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["duration_other"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListDurationSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListCoLisense({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["co_liense"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListCoLisenseSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListRelationshipCoLisense({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["relationship_with_lisense"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListRelationshipCoLisenseSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListBookingLog({ commit },params) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/booking-log/${params.id}`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListBookingLogSuccess", response.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListEvent({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["event_default"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListEventSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    updateService({ commit },params) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/booking/${params.id}`, {
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
    updateRoomService({ commit },params) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/room-booking/${params.id}`, {
            method: "POST",
            data: params.data
          })
          .then((response) => {
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListBookingGeneral({ commit },params) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/booking-general/${params.id}`, {
            method: "GET",
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListBookingGeneralSuccess", response.data);
            commit("setClientAddress", response.data.data);
            commit("setClientPostalCode", response.data.data);
            resolve(response);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    updateBooking({ commit },params) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/booking-general/${params.id_booking}`, {
            method: "POST",
            data: params.booking, 
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
    getBookingGeneralList({ commit },params) {
      params.limit = 25;
      return new Promise((resolve, reject) => {
        global
          .axios(`/booking-general`, {
            method: "GET",
            params,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getBookingGeneralListSuccess", response.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    deleteBooking({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/booking`, {
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
    extensionNiche({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`extension`, {
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
    getInfoExtensionNiche({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`get-niche-extension`, {
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
    extensionMutipleNiche({commit}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`extension-mutiple-niches`, {
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
    getListStatusNiches({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["status_services_niches"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListStatusNichesSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListStatusRoom({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["status_services_rooms"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListStatusRoomSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListStatusOther({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["status_services_products"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListStatusOtherSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    getListStatusBooking({ commit },parms) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/status-booking-general`, {
            method: "POST",
            data:parms,
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListStatusBookingSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
    updateStatusBooking({ commit },params) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/update-status-booking/${params.id}`, {
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
    exportLogBooking({commit, state}, params){
      return new Promise((resolve, reject) => {
        global
          .axios(`/export-booking-log`, {
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
    getListReferral({ commit }) {
      return new Promise((resolve, reject) => {
        global
          .axios(`/reference`, {
            method: "GET",
            params: {
              reference_type: ["referral"],
            },
            headers: {
              Accept: "application/json",
            },
          })
          .then((response) => {
            commit("getListReferralSuccess", response.data.data);
            resolve(true);
          })
          .catch((error) => {
            reject(error);
          });
      });
    },
  };
  
  const mutations = {
    updateListRate(state,data){
      state.listRate = data
    },
    getListTypeRelationshipSuccess(state,data) {
      state.listRelationship = data
    },
    getListRoomTypeSuccess(state,data){
      state.listRoomType = data
    },
    getListDirectorSuccess(state,data){
      state.listDirector = data
    },
    getListServiceSuccess(state,data) {
      state.listOthers = data
    },
    getListServiceByTypeSuccess(state,data) {
      state.listServiceByType = data
    },
    getListAllContractorSuccess(state,data) {
      state.listAllContracotr = data
    },
    getListDurationSuccess(state,data) {
      state.listDuration = data
    },
    getListCoLisenseSuccess(state,data) {
      state.listCoLisesen = data
    },
    getListRelationshipCoLisenseSuccess(state,data) {
      state.listRelationshipColisense = data
    },
    getListBookingLogSuccess(state,data) {
      state.listBookingLog = data
    },
    updateArrNiches(state,data) {
      state.arr_niches.push(data)
    },
    updateArrRoom(state,data) {
      state.arr_rooms.push(data)
    },
    updateArrOther(state,data) {
      state.arr_others.push(data)
    },
    getListEventSuccess(state,data) {
      state.listEvent = data
    },
    getListBookingGeneralSuccess(state,data) {
      state.listServiceBooking = data
    },
    updateNRS(state,data) {
      state.arr_niches = [],
      state.arr_others = [],
      state.arr_rooms = []
    },
    getBookingGeneralListSuccess(state,data) {
      state.listBookingGeneral = data
    },
    getListStatusNichesSuccess(state,data) {
      state.listStatusNiches = data
    },
    getListStatusRoomSuccess(state,data) {
      state.listStatusRoom = data
    },
    getListStatusOtherSuccess(state,data) {
      state.listStatusOther = data
    },
    getListStatusBookingSuccess(state,data) {
      state.listStatusBooking = data
    },
    getListReferralSuccess(state,data) {
      state.listReferral = data
    },
    setClientAddress(state, data) {
      state.clientAddress = data.clients.display_address
    },
    setClientPostalCode(state, data) {
      state.clientPostalCode = data.clients.postal_code
    }
  };
  
  export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
  };
  