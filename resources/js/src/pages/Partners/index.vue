<template>
  <b-container fluid="lg">
    <div class="contractors">
      <div class="title">
        <span class="title-name">Partners</span>
      </div>
    </div>
    <div>
        <b-tabs class="tabs-index tab-contractor" v-model="tabIndex" nav-class="nav-cus">
          <b-tab :active="$route.hash == '#funeral' || $route.hash == ''" @click="activeTab('Funeral')" nav-class="client-info" class="funeral-directors-admin" title="Funeral  Directors" v-bind:class="linkClass(0)">
              <funeralDirectors/>
          </b-tab>
          <b-tab :active="$route.hash == '#contractor'" @click="activeTab('Contractors')" nav-class="booking-service" class="contractors-admin" title="Contractors" v-bind:class="linkClass(1)">
              <Contractor/>
          </b-tab>
        </b-tabs>
    </div>
  </b-container>
</template>

<script>
  import Contractor from "../../components/Partners/contractors";
  import funeralDirectors from "../../components/Partners/funeralDirectors";
export default {
  components: {
    Contractor,
    funeralDirectors
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
    }


  },

};
</script>

<style lang="scss" scoped>


</style>

