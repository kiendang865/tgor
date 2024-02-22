<template>
  <div class="admin-room-info">
    <b-container fluid="lg">
      <div class="contractors d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            <!-- Niche Info -->
            {{titleNiches}}
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div>
        <b-tabs class="tabs-index" v-model="tabIndex" nav-class="nav-cus">
          <b-tab
            nav-class="client-info"
            class="Basic info"
            title="Basic Info"
            v-bind:class="linkClass(0)"
            :active="$route.hash == '#basicinfo' || $route.hash == ''" @click="activeTab('BasicInfo')"
          >
              <NicheInfo ref="AdminNicheInfo"  :nicheData='data' @title="checkTitleniche"/>
          </b-tab>
          <template  v-if="currentBook">
               <b-tab
                    nav-class="booking-service"
                    class="currently-booking"
                    title="Currently Booking"
                >
                    <CurrentBooking :bookingData="data"/>
                </b-tab>
          </template>
         
        </b-tabs>
      </div>
    </b-container>
  </div>
</template>

<script>
  import ChevronLeft from "@/components/Icons/ChevronLeft";
  import BasicInfo from "@/components/BasicInfo";
  import CurrentBooking from "@/components/CurrentBooking"
  import Datepicker from 'vuejs-datepicker';
  import Multiselect from 'vue-multiselect';
  import createNumberMask from 'text-mask-addons/dist/createNumberMask';
  import MaskedInput from 'vue-text-mask';
  import NicheInfo from '../../components/NichesInfo'
  import {
    required,
    minLength,
    between
  } from 'vuelidate/lib/validators'
  import {
      mapActions,
      mapState
  } from 'vuex';

var accounting = require('accounting');

export default {
  components: {
    ChevronLeft,
    BasicInfo,
    CurrentBooking,
    Datepicker,
    Multiselect,
    MaskedInput,
    NicheInfo
  },
  props: {
    nicheData: {
      type: Object,
      default: () => {},
    },
  },

  metaInfo: {
      title: 'Niche Info',
      meta: [{
      vmid: 'description',
      name: 'description',
      content: 'Niche Info Description'
      }]
  },
  data() {
    return {
      tabIndex : 0,
      valueNiche: null,
      titleNiches:'Add Niche',
      statusRoom: [
          'Booked',
          'Agreement',
          'Partially Invoiced',
          'Fully Invoiced',
          'Partially Paid',
          'Fully Paid'
      ],
      currentBook:false,
      data:{}
    };
  },
  created(){
        this.titleNiches = this.$router.history.current.params.id ? "Niche Info" : "Add Niche";
        let idNiche = this.$router.history.current.params.id

        if (idNiche && _.isInteger(+idNiche)) { 
            //update niches
            this.getNicheDetail({
                id: idNiche
            }).then((response) => {
                //do something 
                this.data = response.data.data;
                
            }).catch((error) => {
                this.$router.replace('/admin-niches')
                this.$swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.response.data.errors,
                });
            })
        } else { //is create niche
            this.form.status = this.statusNiches[0]
        }
        
        // this.handlePanigate(this.durationParams);
    
    },
  watch: {
   "data.booking_line_item": function (val) {
     if(val != null){
        if(Object.keys(val).length > 0){
          this.currentBook = true;
        }
        else{
          this.currentBook = false;
        }
     }

   }
  },
  computed: mapState({
     
  }),
  methods: {
    ...mapActions({
        getListTypeNiches: 'niche/getListTypeNiches',
        createNiche: 'niche/createNiche',
        updateNiche: 'niche/updateNiche',
        getNicheDetail: 'niche/getNicheDetail',
        getListCategoryNiche: 'niche/getListCategoryNiche',
        getListDuration: 'niche/getListDuration'
    }),
    activeTab(tabName){

    },
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return '';
      } else {
        return '';
      }
    },
    filterActive(){
      this.activeClass = !this.activeClass
    },
    goBack(){
      // @click="goBack"
      this.$router.push('/admin-niches')
    },
    onSave(){
        this.$refs.AdminNicheInfo.handlecreate();
        
    },
    checkTitleniche(val){
      this.titleNiches  = val;
    }
  },
};
</script>

<style lang="scss" scoped></style>
