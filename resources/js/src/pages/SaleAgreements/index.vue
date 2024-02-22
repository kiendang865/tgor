<template>
    <div class="sale-agreements">
        <b-container fluid="lg">
            <div class="columbarium-niches">
                <div class="title">
                    <span class="title-name">Sales Agreement</span>
                </div>
            </div>

            <div class="others-table">
                <div class='content' v-bind:class="{'run-loading': isLoading}">
                    <div class="outside-table">
                        <ControllTable :isShowIconAdd="false"  :isShowIconTrash="false" :optionSearch="optionsFilter" :isShowIconExport="false" :onChangeSearch='onChangeSearch' @deleteItems="deleteItem"/>
                        <TableCustom ref="SaleAgreementTable" :tableFields="columnActive.fields" @Items="getItems"  :tableItems="listSaleAgreement.data" @rowClicked="gotoSaleAgreementInfo">
                            <template slot="tgor_table:invoices" slot-scope="data">
                                <div v-if="data.item.invoices.length">
                                    <span v-for="(item,key) in data.item.invoices" :key="key">
                                        <template v-if="data.item.invoices.length == 1">
                                            <router-link :to="{ name: 'InvoiceInfo', params: { id: item.id }}" class="underline">
                                                {{item.invoice_no}}
                                            </router-link>
                                        </template>
                                        <template v-else-if="data.item.invoices.length == key+1">
                                            <router-link :to="{ name: 'InvoiceInfo', params: { id: item.id }}" class="underline">
                                                {{item.invoice_no}}
                                            </router-link>
                                        </template>
                                        <template v-else>
                                            <router-link :to="{ name: 'InvoiceInfo', params: { id: item.id }}" class="underline">
                                                {{item.invoice_no   }},
                                            </router-link>
                                        </template>
                                    </span>
                                </div>
                                <div v-else>--</div>
                            </template>

                            <template slot="tgor_table:total_amount" slot-scope="data">
                                {{data.item.total_amount ? data.item.total_amount : 0 | formatMoney}}
                            </template>
                            <template slot="tgor_table:total_tax_amount" slot-scope="data">
                                {{data.item.total_tax_amount ? data.item.total_tax_amount : 0 | formatMoney}}
                            </template>
                            <template slot="tgor_table:total" slot-scope="data">
                                {{data.item.total ? data.item.total : 0 | formatMoney}}
                            </template>
                            <template slot="tgor_table:created_at" slot-scope="data">
                                {{data.item.created_at ? customFormatter(data.item.created_at) : '--'}}
                            </template>
                        </TableCustom>
                        <b-row class="pagination">
                            <b-col md="12" class="end">
                                <span>
                                    {{listSaleAgreement.from ? `${listSaleAgreement.from}-${listSaleAgreement.to} of ${listSaleAgreement.total}` : '0-0 of 0'}}
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
    </div>
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
        title: 'Sale Agreement',
        meta: [{
        vmid: 'description',
        name: 'description',
        content: 'Sale Agreement Description'
        }]
    },    
    data() {
        return {
            isLoading:false,
            saleParams:{
                page:1,
                filter:{}
            },
            ids:[],
            tabIndex: 0,
            activeFilter: false,
            activeStatus: 'Active',
            activeClass: false,
            allSelected: false,
            selected: [],

            columnActive: {
                fields: [
                    {
                        key: "xxx",
                        label: "",
                        thClass: "checkbox-column text-center",
                        tdClass: "checkbox-column text-center",
                        thStyle: "width: 50px",
                        isActive: 1,
                    },
                    {
                        key: 'sale_agreement_no',
                        label: 'Sale Agreement #',
                        isActive: 1,
                        keySearch: 'id',
                        type: 'text',
                        isFilter: true,
                        thStyle: "width: 150px",

                    },
                    {
                        key: 'invoices',
                        label: 'Invoice #',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'created_at',
                        label: `Date`,
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
                        key: 'reference_value_text',
                        label: `Services`,
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'total_amount',
                        label: 'Amount',
                        isActive: 1,
                        // thStyle: "width: 100px",
                        isFilter: true
                    },
                    {
                        key: 'total_tax_amount',
                        label: 'GST',
                        isActive: 1,
                        // thStyle: "width: 100px",
                        isFilter: true
                    },
                    {
                        key: 'total',
                        label: 'Total',
                        isActive: 1,
                        // thStyle: "width: 100px",
                        isFilter: true
                    },
                ],
                show: [],
                hide: []
            },
            optionsFilter: [
                {
                    name: 'All',
                    value: 'all'
                },
                {
                    name: 'Sale Agreement #',
                    value: 'sale_agreement'
                },
                {
                    name: 'Invoice #',
                    value: 'invoices'
                },
                {
                    name: 'Date',
                    value: 'created_date'
                },
                {
                    name: "Client's Name",
                    value: 'clients_name'
                },
                {
                    name: "Services",
                    value: 'services'
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
        }
    },
    created(){
        this.handlePanigate(this.saleParams);
    },
    computed: mapState({
        listSaleAgreement: state => state.saleareement.listSaleAgreement,
    }),
    filters: {
        formatMoney(val) {
            return accounting.formatMoney(val, { format: { pos : "%s %v", neg : "%s (%v)", zero: "--" } })
        }
    },
    methods: {
        ...mapActions({
            getListSaleAreement: 'saleareement/getListSaleAreement',   
            deleteSaleAgreement: 'saleareement/deleteSaleAgreement',      
        }),
        gotoSaleAgreementInfo(item){
            this.$router.push({name:"SaleAgreementInfo", params: { id: item.id }})
        },
        handlePanigate(params){
            this.isLoading = true;
            this.getListSaleAreement(params).then((response)=>{
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
            let {current_page} = this.listSaleAgreement;
            if (current_page != 1) {
                this.saleParams.page = current_page - 1
                this.handlePanigate(this.saleParams)
            }
        },
        nextPanigate() {
            let {current_page, last_page} = this.listSaleAgreement;
            if (current_page != last_page) {
                this.saleParams.page = current_page + 1
                this.handlePanigate(this.saleParams)
            }
        },
        onChangeSearch(valueSearch, typeSearch){
            let {current_page, last_page} = this.listSaleAgreement;
            clearTimeout(this.actionSearch)
            this.saleParams.filter = {};
            if(!valueSearch){
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.saleParams)
                },300)
            }else{
                
                this.saleParams.filter[typeSearch.value] = valueSearch
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.saleParams)
                },300)
            }
            
        },
        deleteItem(){
            if(this.ids.length == 0){
                 this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Can not find sale agreement."
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
                        this.deleteSaleAgreement(prms)
                        .then(res => {
                            this.$swal({
                                icon: 'success',
                                title: 'Success!',
                                text: res.data.status
                            });
                            this.handlePanigate(this.saleParams)
                            this.$refs.SaleAgreementTable.reloadData();
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
