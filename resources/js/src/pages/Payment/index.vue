<template>
<b-container fluid="lg">
    <div class="columbarium-niches">
        <div class="title">
            <span class="title-name">Receipt</span>
        </div>
    </div>

    <div class="others-table">
        <div class='content' v-bind:class="{'run-loading': isLoading}">
            <div class="outside-table">
                <ControllTable :isShowIconAdd="false" :isShowIconTrash="isTrash" :optionSearch="optionsFilter" :isShowIconExport="false" :onChangeSearch='onChangeSearch' @deleteItems="deleteItem"/>
                <TableCustom ref="PaymentTable" @Items="getItems" :tableFields="columnActive.fields" :tableItems="listPayment.data" @rowClicked="gotoPaymentInfo">
                     <template slot="tgor_table:total_amount" slot-scope="data">
                        {{data.item.total_amount ? data.item.total_amount : 0 | formatMoney}}
                    </template>
                    <template slot="tgor_table:total_tax_amount" slot-scope="data">
                        {{data.item.total_tax_amount ? data.item.total_tax_amount : 0 | formatMoney}}
                    </template>
                    <template slot="tgor_table:total_discount" slot-scope="data">
                        {{data.item.total_discount ? data.item.total_discount : 0 | formatMoney}}
                    </template>
                    <template slot="tgor_table:total" slot-scope="data">
                        {{data.item.total ? data.item.total : 0 | formatMoney}}
                    </template>
                    <template slot="tgor_table:amount_payable" slot-scope="data">
                        {{data.item.amount_payable ? data.item.amount_payable : 0 | formatMoney}}
                    </template>
                    <template slot="tgor_table:remarks" slot-scope="data">
                        {{data.item.remarks ? data.item.remarks : '--'}}
                    </template>
                    <template slot="tgor_table:payment_mode" slot-scope="data">
                        {{data.item.payment_mode ? data.item.payment_mode.reference_value_text : '--'}}
                    </template>
                    <template slot="tgor_table:payment_date" slot-scope="data">
                        {{data.item.payment_date ? customFormatter(data.item.payment_date) : '--'}}
                    </template>
                </TableCustom>
                <b-row class="pagination">
                    <b-col md="12" class="end">
                        <span>
                             {{listPayment.from ? `${listPayment.from}-${listPayment.to} of ${listPayment.total}` : '0-0 of 0'}}
                        </span>
                        <span class="icon">
                            <b-img @click="prevPanigate" class="image" src="images/left.png" fluid alt="Responsive image"></b-img>
                        </span>
                        <span class="icon">
                            <b-img @click="nextPanigate" class="image" src="images/right.png" fluid alt="Responsive image"></b-img>
                        </span>
                    </b-col>
                </b-row>
            </div>
        </div>
    </div>
</b-container>
</template>

<script>
//import
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from '../../components/Table'
const accounting = require('accounting');
import moment from 'moment'
import {
    mapActions,
    mapState
} from 'vuex'
export default {
    components: {
        ControllTable,
        TableCustom
    },
    metaInfo: {
        title: 'Receipt',
        meta: [{
        vmid: 'description',
        name: 'description',
        content: 'Receipt Description'
        }]
    },      
    data() {
        return {
            isLoading:false,
            isTrash: JSON.parse(localStorage.getItem("admin_profile")).roles_id == 1 ? true : false,
            paymentParams:{
                page:1,
                filter:{}
            },
            ids:[],
            optionsFilter: [
                {
                    name: 'All',
                    value: 'all'
                },
                {
                    name: 'Receipt #',
                    value: 'payment'
                },
                {
                    name: 'Date',
                    value: 'date'
                },
                {
                    name: "Client's Name",
                    value: 'clients_name'
                },
                {
                    name: 'Payment Mode',
                    value: 'payment_mode'
                },
                {
                    name: "Remarks",
                    value: 'clients_name'
                },
                {
                    name: "Amount",
                    value: 'amount'
                },
                {
                    name: "GST",
                    value: 'gst'
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
                fields: [
                    {
                        key: 'actions',
                        label: '',
                        thClass: 'checkbox-column text-center',
                        tdClass: 'checkbox-column text-center',
                        thStyle: "width: 50px",
                        isActive: 1
                    },
                    {
                        key: 'payment_no',
                        label: 'Receipt #',
                        isActive: 1,
                        keySearch: 'id',
                        type: 'text',
                        isFilter: true,

                    },
                    {
                        key: 'payment_date',
                        label: 'Date',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'client.display_name',
                        label: `Client's Name`,
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'payment_mode',
                        label: `Payment Mode`,
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
                        key: 'total_amount',
                        label: 'Amount',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'total_tax_amount',
                        label: 'GTS',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'total_discount',
                        label: 'Discount',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'total',
                        label: 'Total',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'amount_payable',
                        label: 'Amt. Payable',
                        isActive: 1,
                        isFilter: true
                    },
                    
                ],
                show: [],
                hide: []
            },

        }
    },
    filters: {
        formatMoney(val) {
            return accounting.formatMoney(val, { format: { pos : "%s %v", neg : "%s (%v)", zero: "--" } })
        }
    },
    created(){  
        this.handlePanigate(this.paymentParams);
    },
    computed: mapState({
        listPayment: state => state.payment.listPayment,
    }),
    methods: {
        ...mapActions({
            getListPayment: 'payment/getListPayment',   
            deletePayment: 'payment/deletePayment',      
        }),
        gotoPaymentInfo(item){ 
            if(item.is_donate == 0)
           {
               this.$router.push({name:"PaymentInfo", params: { id: item.id }})
           }
           else{
               this.$router.push({name:"DonateInfo", params: { id: item.id }})
           }
       },
       handlePanigate(params){
            this.isLoading = true;
            this.getListPayment(params).then((response)=>{ 
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
            let {current_page} = this.listPayment;
            if (current_page != 1) {
                this.paymentParams.page = current_page - 1
                this.handlePanigate(this.paymentParams)
            }
        },
        nextPanigate() {
            let {current_page, last_page} = this.listPayment;
            if (current_page != last_page) {
                this.paymentParams.page = current_page + 1
                this.handlePanigate(this.paymentParams)
            }
        },
        onChangeSearch(valueSearch, typeSearch){
            let {current_page, last_page} = this.listPayment;
            clearTimeout(this.actionSearch)
            this.paymentParams.filter = {};
            if(!valueSearch){
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.paymentParams)
                },300)
            }else{
                
                this.paymentParams.filter[typeSearch.value] = valueSearch
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.paymentParams)
                },300)
            }
            
        },
        deleteItem(){
            if(this.ids.length == 0){
                 this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Can not find payment."
                    });
            }
            else{
                this.$swal({
                    title: 'Permanently delete?',
                    text: 'This action is irreversible.',
                    icon: 'warning',
                    customClass:{
                        container: 'swal-del-item',
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.value) {
                        this.isLoading = true;
                        let prms = {ids:this.ids}
                        this.deletePayment(prms)
                        .then(res => {
                            this.$swal({
                                icon: 'success',
                                title: 'Success!',
                                text: res.data.status
                            });
                            this.handlePanigate(this.paymentParams)
                            this.$refs.PaymentTable.reloadData();
                            this.isLoading = false;
                        })
                        .catch((error)=>{
                            this.isLoading = false;
                            this.$swal({
                                icon: 'error',
                                title: 'Oops...',
                                text: error.response.data.errors
                            });
                        })
                    } 
                })
                
            }
        },
        getItems(item){ 
            this.ids = item;
        },
        onCreateNiche(){
            this.$router.push({
                name: "CreateRoom"
            })
        },
        customFormatter(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        unformatter(val){
            return accounting.unformat(val)
        }
    },
    watch: {

    }
}
</script>

<style lang="scss" scoped>

</style>
