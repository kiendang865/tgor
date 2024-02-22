<template>
  <b-container fluid="lg" class="admin-customer-basic-info">
    <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Client Detail
          </span>
        </div>
        <div class="wrapper-btn">
          <!-- v-b-modal="'donation'" -->
          <b-button class="btn-donation" @click="onDonate">Donation</b-button>
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
    <div>
        <b-tabs class="tabs-index tab-contractor" v-model="tabIndex" nav-class="nav-cus">
          <b-tab nav-class="client-info" class="funeral-directors-admin" title="Client" v-bind:class="linkClass(0)">
              <customer ref="Customer"/>
          </b-tab>
          <b-tab nav-class="booking-service" class="contractors-admin" title="Niches" v-bind:class="linkClass(1)">
              <nichesCustomer/>
          </b-tab>
          <b-tab nav-class="booking-service" class="contractors-admin" @click="getUserRoom" title="Memorial Room" v-bind:class="linkClass(2)">
              <roomCustomer/>
          </b-tab>
          <b-tab nav-class="booking-service" class="contractors-admin" title="Additional Service" v-bind:class="linkClass(3)">
              <serviceCustomer/>
          </b-tab>
          <b-tab nav-class="booking-service" class="contractors-admin" title="Donation" v-bind:class="linkClass(4)">
              <donationCustomer/>
          </b-tab>
        </b-tabs>
    </div>
  </b-container>
</template>
<script>
  import customer from "../../components/Customer";
  import nichesCustomer from "../../components/Customer/nichesCustomer";
  import ChevronLeft from "@/components/Icons/ChevronLeft";
  import roomCustomer from "../../components/Customer/roomCustomer";
  import serviceCustomer from "../../components/Customer/serviceCustomer";
  import donationCustomer from "../../components/Customer/donationCustomer";
export default {
  components: {
    customer,
    nichesCustomer,
    ChevronLeft,
    roomCustomer,
    serviceCustomer,
    donationCustomer
  },
  metaInfo: {
      title: 'Partners',
      meta: [{
      vmid: 'description',
      name: 'description',
      content: 'Partners Description'
      }]
  },
  data() {
    return {
      tabIndex : 0

    };
  },
  methods: {
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
    activeTab(tabName){
      switch (tabName) {
        case 'Funeral':
          if(window.location.hash != "#funeral"){
            this.$router.push(`/admin-partners/#funeral`)
          }
          break;
        case 'Contractors':
          if(window.location.hash != "#contractor"){
            this.$router.push(`/admin-partners/#contractor`)
          }
          break;      
        default:
          break;
      }
    },
    goBack(){
      this.$router.push('/admin-customer')
    },
    onSave(){
      this.$refs.Customer.onSave();
    },
    onDonate(){
      this.$refs.Customer.showModal();
    },
    getUserRoom(){
    }


  },

};
</script>

<style lang="scss" scoped>


</style>

