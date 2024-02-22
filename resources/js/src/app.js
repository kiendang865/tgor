import 'es6-promise/auto'
import './bootstrap'
import Vue from 'vue'
import VueRouter from 'vue-router'
import Index from './Index'
import router from './router'
import BootstrapVue from "bootstrap-vue"
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import store from './store'
import Axios from './common/axios'
import VueSignaturePad from 'vue-signature-pad';
import Vuelidate from 'vuelidate'
import Multiselect from 'vue-multiselect' 
import VueMeta from 'vue-meta'

// Set Vue globally
window.Vue = Vue

const options = {
  confirmButtonColor: '#71C5A1',
  cancelButtonColor: '#ff7674',
  title: 'Notification'
};
// Set Vue router
Vue.router = router
Vue.use(VueRouter)
Vue.use(VueSignaturePad);
Vue.use(BootstrapVue);
Vue.use(VueSweetalert2, options);
Vue.use(Vuelidate)
Vue.use(Multiselect)
Vue.component('vue-multiselect', window.VueMultiselect)
Vue.use(VueMeta, {
  // optional pluginOptions
  refreshOnceOnNavigation: true
})
global.axios = Axios
// Load Index
Vue.component('index', Index)
Vue.config.productionTip = false
const app = new Vue({
  store,
  el: '#app',
  router
});
