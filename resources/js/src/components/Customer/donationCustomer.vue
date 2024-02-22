<template>
    <div class="content-tab-table">
        <div class="outside-tab-table" v-bind:class="{'run-loading': isLoading}">
           <ControllTable :isShowIconAdd="false" :isShowIconTrash="false" :optionSearch="optionsFilter" :onChangeSearch='onChangeSearch' @deleteItems="deleteItem"/>
            <TableCustom ref="adminOtherServiceTable" :tableFields="columnActive.fields" @Items="getItems" :tableItems="listDonation.data">
                <template slot="tgor_table:payment_date" slot-scope="data">
                  {{data.item.payment_date ? customFormatter(data.item.payment_date) : '--'}}
              </template>
            </TableCustom>
            <b-row class="pagination">
                    <b-col md="12" class="end">
                        <span>
                           {{listDonation.from ? `${listDonation.from}-${listDonation.to} of ${listDonation.total}` : '0-0 of 0'}}
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
      isLoading: false,
    donationParams:{
        page:1,
        id: this.$router.history.current.params.id
    },
    optionsFilter: [
        {
            name: 'All',
            value: 'all'
        },
        {
            name: 'Receipt #',
            value: 'payment_no'
        },
        {
            name: "Date",
            value: 'payment_date'
        },
        // {
        //     name: "Client Name",
        //     value: 'clients_name'
        // },
        {
            name: 'Payment Mode',
            value: 'payment_mode'
        },
        {
            name: 'Remarks',
            value: 'remarks'
        },
        {
            name: 'Total',
            value: 'total'
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
                key: 'payment_no',
                label: 'Receipt #',
                isActive: 1,
                // keySearch: 'id',
                // type: 'text',
                // thStyle: "width:100px",
                isFilter: true,

            },
            {
                key: 'payment_date',
                label: `Date`,
                isActive: 1,
                isFilter: true
            },
            {
                key: 'client.display_name',
                label: "Client's Name",
                isActive: 1,
                isFilter: true
            },
            {
                key: 'payment_mode.reference_value_text',
                label: 'Payment Mode',
                isActive: 1,
                isFilter: true
            },
            {
                key: 'remarks',
                label: 'Remarks',
                isActive: 1,
                isFilter: true
            },
            {
                key: 'total',
                label: 'Total',
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
      this.handlePanigate(this.donationParams)
    },
    computed: mapState({
        listDonation: state => state.service.listDonation,
    }),
  methods: {
        ...mapActions({
            getListDonation: 'service/getListDonation'   
        }),
        customFormatter(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        customFormatDeath(date) {
            return moment(date).format('DD/MM');
        },
        handlePanigate(params){
            this.isLoading = true;
            this.getListDonation(params).then((response)=>{
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
            let {current_page} = this.listDonation;
            if (current_page != 1) {
                this.donationParams.page = current_page - 1
                this.handlePanigate(this.donationParams)
            }
        },
        nextPanigate() {
            let {current_page, last_page} = this.listDonation;
            if (current_page != last_page) {
                this.donationParams.page = current_page + 1
                this.handlePanigate(this.donationParams)
            }
        },
        onChangeSearch(valueSearch, typeSearch){
            let {current_page, last_page} = this.listDonation;
            clearTimeout(this.actionSearch)
            this.donationParams.filter = {};
            if(!valueSearch){
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.donationParams)
                },300)
            }else{
                
                this.donationParams.filter[typeSearch.value] = valueSearch
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.donationParams)
                },300)
            }
            
        },
        deleteItem(){

        },
        getItems(){

        },
        customFormatter(date) {
            return moment(date).format('DD/MM/YYYY');
        },
  },
  filters: {
      formatMoney(val) {
          return accounting.formatMoney(val, { format: { pos : "%s %v", neg : "%s (%v)", zero: "--" } })

      }
  },
  watch: {
    'listDonation': function(val) {
    },
  },
}
</script>