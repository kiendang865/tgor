<template>
  <div class="service-niches">
    <b-container fluid="lg">
      <div class="columbarium-niches">
        <div class="title">
          <span class="title-name">Niches (Reserved)</span>
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
              ref="nichesReserved"
              :tableFields="columnActive.fields"
              :tableItems="listnicheReserved.data"
              @Items="getItems"
              @rowClicked="gotoNicheReserved"
            >
              <template slot="tgor_table:reserved_date" slot-scope="data">
                  {{data.item.reserved_date ? customFormatter(data.item.reserved_date) : '--'}}
              </template>
            </Table>
            <b-row class="pagination">
              <b-col md="12" class="end">
                <span>
                  {{listnicheReserved.from ? `${listnicheReserved.from}-${listnicheReserved.to} of ${listnicheReserved.total}` : '0-0 of 0'}}
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
      nichesReservedParams:{
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
          name: "Niche Ref",
          value: "niche_ref",
        },
        {
          name: "Customer Name",
          value: "customer_name",
        },
        {
          name: "Mobile",
          value: "mobile",
        },
        {
          name: "Email",
          value: "email",
        },
        {
          name: "Reserve Date",
          value: "reserved_date",
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
            key: "niche.reference_no",
            label: "Niche Ref",
            // thStyle: "width:100px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "customer_name",
            label: "Customer Name",
            // thStyle: "width:100px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "mobile",
            label: "Mobile",
            // thStyle: "width:220px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "email",
            label: "Email",
            // thStyle: "width:100px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "reserved_date",
            label: "Reserve Date",
            isActive: 1,
            // thStyle: "width:100px",
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
    }
  },
  created(){
      this.handlePanigate(this.nichesReservedParams)
  },
  computed: mapState({
      listnicheReserved: state => state.nichereserved.listnicheReserved,
  }),
  methods: {
    ...mapActions({
        getListNichesReserved: 'nichereserved/getListNichesReserved',   
        deleteNicheReserved: 'nichereserved/deleteNicheReserved',      
    }),
    onCreateNicheReserved(){
      this.$router.push({
        name: "NewNicheReserved"
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
          text: "Can not find item.",
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
            this.deleteNicheReserved(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handlePanigate(this.nichesReservedParams);
                this.$refs.nichesReserved.reloadData();
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
      let { current_page } = this.listnicheReserved;
      if (current_page != 1) {
        this.nichesReservedParams.page = current_page - 1;
        this.handlePanigate(this.nichesReservedParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listnicheReserved;
      if (current_page != last_page) {
        this.nichesReservedParams.page = current_page + 1;
        this.handlePanigate(this.nichesReservedParams);
      }
    },
    handlePanigate(params){
      this.isLoading = true;
      this.getListNichesReserved(params).then((response)=>{
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
        let {current_page, last_page} = this.listnicheReserved;
        clearTimeout(this.actionSearch)
        this.nichesReservedParams.filter = {};
        if(!valueSearch){
            this.actionSearch = setTimeout(()=>{
                this.handlePanigate(this.nichesReservedParams)
            },300)
        }else{
            
            this.nichesReservedParams.filter[typeSearch.value] = valueSearch
            this.actionSearch = setTimeout(()=>{
                this.handlePanigate(this.nichesReservedParams)
            },300)
        }
        
    },
    customFormatter(date) {
      return moment(date).format('DD/MM/YYYY');
    },
    getItems(item){
        this.ids = item;
    },
    gotoNicheReserved(item){
      this.$router.push(`/reserved-niches/${item.id}`)
    }
  },
}
</script>