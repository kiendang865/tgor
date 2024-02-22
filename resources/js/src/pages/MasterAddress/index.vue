<template>
  <div class="service-niches">
    <b-container fluid="lg">
      <div class="columbarium-niches">
        <div class="title">
          <span class="title-name">Master Addresses</span>
        </div>
      </div>
      <div class="wrapper-table">
        <b-container fluid="lg" v-bind:class="{ 'run-loading': isLoading }">
          <div class="outside-table">
            <ControllTable
              :isShowIconAdd="true"
              :isShowIconTrash="true"
              :optionSearch="optionsFilter"
              :onCreate="onCreateNicheReserved"
              :onChangeSearch='onChangeSearch'
              @deleteItems="deleteItem"
            ></ControllTable>
            <Table
              ref="masterAddress"
              :tableFields="columnActive.fields"
              :tableItems="listAddress.data"
              @Items="getItems"
              @rowClicked="gotoMasterAddress"
            >
              <template slot="tgor_table:reserved_date" slot-scope="data">
                  {{data.item.reserved_date ? customFormatter(data.item.reserved_date) : '--'}}
              </template>
            </Table>
            <b-row class="pagination">
              <b-col md="12" class="end">
                <span>
                  {{listAddress.from ? `${listAddress.from}-${listAddress.to} of ${listAddress.total}` : '0-0 of 0'}}
                </span>
                <span class="icon" @click="prevPanigate">
                  <b-img
                    class="image"
                    src="images/left.png"
                    fluid
                    alt="Responsive image"
                  ></b-img>
                </span>
                <span class="icon" @click="nextPanigate">
                  <b-img
                    class="image"
                    src="images/right.png"
                    fluid
                    alt="Responsive image"
                  ></b-img>
                </span>
              </b-col>
            </b-row>
          </div>
        </b-container>
      </div>
    </b-container>
  </div>
</template>
<script>
import ControllTable from "@/components/customViews/controllTable.vue";
import Table from "@/components/Table";
import moment from "moment";
import {
    mapActions,
    mapState
} from 'vuex'
export default {
  components: {
    ControllTable,
    Table,
  },
  data() {
    return {
      isLoading: false,
      addressParams:{
        page:1,
        filter:{}
      },
      ids:[],
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        {
          name: "Postal code",
          value: "postal_code",
        },
        {
          name: "Address",
          value: "address",
        },
      ],
      columnActive: {
        fields: [
          {
            key: "actions",
            label: "",
            thClass: "checkbox-column text-center",
            tdClass: "checkbox-column text-center",
            thStyle: "width: 50px",
            isActive: 1
          },
          {
            key: "postal_code",
            label: "Pastal code",
            // thStyle: "width:100px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "address",
            label: "Address",
            // thStyle: "width:100px",
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
    }
  },
  created(){
      this.handlePanigate(this.addressParams)
  },
  computed: mapState({
      listAddress: state => state.address.listAddress,
  }),
  methods: {
    ...mapActions({
        getListAddress: 'address/getListAddress',   
        deleteAddress: 'address/deleteAddress',      
    }),
    onCreateNicheReserved(){
      this.$router.push({
        name: "NewAddress"
      })
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listServiceNiches;
      clearTimeout(this.actionSearch);
      this.serviceNicheParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.serviceNicheParams);
        }, 1000);
      } else {
        this.serviceNicheParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.serviceNicheParams);
        }, 1000);
      }
    },
    deleteItem() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find service.",
        });
      } else {
        this.$swal({
          title: "Permanently delete?",
          text: "This action is irreversible.",
          icon: "warning",
          customClass: {
            container: "swal-del-item",
          },
          showCancelButton: true,
          confirmButtonText: "Yes",
          cancelButtonText: "No",
        }).then((result) => {
          if (result.value) {
            this.isLoading = true;
            let prms = {ids:this.ids}
            this.deleteAddress(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handlePanigate(this.addressParams);
                this.$refs.masterAddress.reloadData();
                this.isLoading = false;
              })
              .catch((error) => {
                this.isLoading = false;
                this.$swal({
                  icon: "error",
                  title: "Oops...",
                  text: error.response.data.errors,
                });
              });
          }
        });
      }
    },
    prevPanigate() {
      let { current_page } = this.listAddress;
      if (current_page != 1) {
        this.addressParams.page = current_page - 1;
        this.handlePanigate(this.addressParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listAddress;
      if (current_page != last_page) {
        this.addressParams.page = current_page + 1;
        this.handlePanigate(this.addressParams);
      }
    },
    handlePanigate(params){
      this.isLoading = true;
      this.getListAddress(params).then((response)=>{
          this.isLoading = false;
      }).catch((error)=> {
          this.isLoading = false;
          this.$swal({
              icon: 'error',
              title: 'Oops...',
              text: error.response.data.errors
          });
      })
    },
    onChangeSearch(valueSearch, typeSearch){
        let {current_page, last_page} = this.listAddress;
        clearTimeout(this.actionSearch)
        this.addressParams.filter = {};
        if(!valueSearch){
            this.actionSearch = setTimeout(()=>{
                this.handlePanigate(this.addressParams)
            },300)
        }else{
            
            this.addressParams.filter[typeSearch.value] = valueSearch
            this.actionSearch = setTimeout(()=>{
                this.handlePanigate(this.addressParams)
            },300)
        }
        
    },
    customFormatter(date) {
      return moment(date).format('DD/MM/YYYY');
    },
    getItems(item){
        this.ids = item;
    },
    gotoMasterAddress(item){
      this.$router.push(`/master-address/${item.id}`)
    }
  },
}
</script>