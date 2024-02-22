<template>
    <div class="content-tab-table">
        <div class="outside-tab-table" v-bind:class="{'run-loading': isLoading}">
           <ControllTable :isShowIconAdd="false" :isShowIconTrash="false" :optionSearch="optionsFilter" :onChangeSearch='onChangeSearch' @deleteItems="deleteItem"/>
            <TableCustom ref="adminOtherServiceTable" :tableFields="columnActive.fields" @Items="getItems" :tableItems="listServiceOther.data"></TableCustom>
            <b-row class="pagination">
                    <b-col md="12" class="end">
                        <span>
                           {{listServiceOther.from ? `${listServiceOther.from}-${listServiceOther.to} of ${listServiceOther.total}` : '0-0 of 0'}}
                        </span>
                        <span class="icon" @click="prevPanigate">
                            <b-img class="image" src="/images/left.png" fluid alt="Responsive image"></b-img>
                        </span>
                        <span class="icon" @click="nextPanigate">
                            <b-img class="image" src="/images/right.png" fluid alt="Responsive image"></b-img>
                        </span>
                    </b-col>
              </b-row>
        </div>
    </div>
    
</template>

<script>
import ControllTable from "@/components/customViews/controllTable.vue";
import TableCustom from '@/components/Table/nicheCustomerTable';
import {
    mapActions,
    mapState
} from 'vuex'
import moment from 'moment';
import Multiselect from 'vue-multiselect'
import MaskedInput from 'vue-text-mask';
import createNumberMask from 'text-mask-addons/dist/createNumberMask'
var accounting = require('accounting');
import {
    required,
    minLength,
    between,
    decimal
} from 'vuelidate/lib/validators'

export default {
  name: "ServiceNiches",
  components: { ControllTable, TableCustom, Multiselect,MaskedInput },
  metaInfo: {
      title: 'Columbarium Niches',
      meta: [{
      vmid: 'description',
      name: 'description',
      content: 'Columbarium Niches Description'
      }]
  },   
  data() {
    return {
      totalAmount:'',
      isLoading: false,
      allowExtent:true,
      ids:[],
      optionsNiche: [
        {
          name:"30 years",
          value:30
        },
        {
          name:"50 years",
          value:50
        }
      ],
      isLoading: false,
    serviceOtherParams:{
        page:1,
        type:'Other',
        filter:{},
        user_id: this.$router.history.current.params.id
    },
    optionsFilter: [
        {
            name: 'All',
            value: 'all'
        },
        // {
        //     name: 'ID',
        //     value: 'id'
        // },
        {
            name: "Client Name",
            value: 'clients_name'
        },
        {
            name: 'Service Type',
            value: 'service_type'
        },
        {
            name: 'Contractor',
            value: 'contractor'
        },
    ],
    tabIndex: 0,
    activeFilter: false,
    activeStatus: 'Active',
    activeClass: false,
    allSelected: false,
    selected: [],

    columnActive: {
        fields: [{
                key: 'index',
                label: '',
                thClass: 'checkbox-column text-center',
                tdClass: 'checkbox-column text-center',
                thStyle: "width: 20px;height:61px",
                isActive: 1
            },
            {
                key: 'booking.booking_no',
                label: 'Booking #',
                isActive: 1,
                keySearch: 'id',
                type: 'text',
                // thStyle: "width:100px",
                isFilter: true,

            },
            {
                key: 'client.display_name',
                label: `Client's Name`,

                isActive: 1,
                isFilter: true
            },
            {
                key: 'other.service_name',
                label: 'Service Type',
                isActive: 1,
                isFilter: true
            },
            {
                key: 'service_type.service_name',
                label: 'Description',
                isActive: 1,
                isFilter: true
            },
            {
                key: 'contractor.company_name',
                label: 'Contractor',
                isActive: 1,
                isFilter: true
            },
        ],
        show: [],
        hide: []
    },
    }
  },
  validations(){
    return{
      extensionParam:{
        // information:{
        //   required,
        //   $each: {
        //     reference_no:{
        //       required
        //     },
        //     time:{
        //       required
        //     },
        //     exten_year:{
        //       required
        //     },
        //     extension:{

        //     }
        //   }
        
        // }
        
      },
    }
  },
    created(){
      this.handlePanigate(this.serviceOtherParams)
    },
    computed: mapState({
        listServiceOther: state => state.service.listServiceOther,
    }),
  methods: {
        ...mapActions({
            getListServiceOther: 'service/getListServiceOther',
            deleteService: 'service/deleteService'    
        }),
        customFormatter(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        customFormatDeath(date) {
            return moment(date).format('DD/MM');
        },
        handlePanigate(params){
            this.isLoading = true;
            this.getListServiceOther(params).then((response)=>{
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
        prevPanigate() {
            let {current_page} = this.listServiceOther;
            if (current_page != 1) {
                this.serviceOtherParams.page = current_page - 1
                this.handlePanigate(this.serviceOtherParams)
            }
        },
        nextPanigate() {
            let {current_page, last_page} = this.listServiceOther;
            if (current_page != last_page) {
                this.serviceOtherParams.page = current_page + 1
                this.handlePanigate(this.serviceOtherParams)
            }
        },
        onChangeSearch(valueSearch, typeSearch){
            let {current_page, last_page} = this.listServiceOther;
            clearTimeout(this.actionSearch)
            this.serviceOtherParams.filter = {};
            if(!valueSearch){
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.serviceOtherParams)
                },300)
            }else{
                
                this.serviceOtherParams.filter[typeSearch.value] = valueSearch
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.serviceOtherParams)
                },300)
            }
            
        },
        deleteItem(){

        },
        getItems(){

        },
  },
  filters: {
      formatMoney(val) {
          return accounting.formatMoney(val, { format: { pos : "%s %v", neg : "%s (%v)", zero: "--" } })

      }
  },
  watch:{
      listServiceOther: function(val){
      }
  }
}
</script>