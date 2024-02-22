<template>
<b-container fluid="lg">
    <div class="columbarium-niches">
        <div class="title">
            <span class="title-name">Client Details</span>
        </div>
    </div>

    <div class="others-table">
        <div class='content'  v-bind:class="{'run-loading': isLoading}">
            <div class="outside-table">
                <ControllTable  :optionSearch="optionsFilter" :onChangeSearch='onChangeSearch' :onCreate="onCreateNiche" @deleteItems="deleteItem" />
                <TableCustom ref="adminCustomerTable" :tableFields="columnActive.fields" @Items="getItems"  :tableItems="listCustomer.data" @rowClicked="gotoCustomerInfo">
                    <template slot="tgor_table:display_name" slot-scope="data">
                          {{data.item.display_name && data.item.display_name != '' ? data.item.display_name : '--'}}
                    </template>
                    <template slot="tgor_table:phone" slot-scope="data">
                          {{data.item.phone && data.item.phone != '' ? data.item.phone : '--'}}
                    </template>
                </TableCustom>
                <b-row class="pagination">
                    <b-col md="12" class="end">
                        <span>
                            {{listCustomer.from ? `${listCustomer.from}-${listCustomer.to} of ${listCustomer.total}` : '0-0 of 0'}}
                        </span>
                        <span @click="prevPanigate" class="icon">
                            <b-img class="image" src="images/left.png" fluid alt="Responsive image"></b-img>
                        </span>
                        <span @click="nextPanigate" class="icon">
                            <b-img class="image" src="images/right.png" fluid alt="Responsive image"></b-img>
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
import TableCustom from '../../components/Table';
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
      title: 'Customer',
      meta: [{
      vmid: 'description',
      name: 'description',
      content: 'Customer Description'
      }]
  },
    data() {
        return {
            isLoading: false,
            customerParam:{
                page:1,
                filter:{}
            },
            optionsFilter: [
                {
                    name: 'All',
                    value: 'all'
                },
                {
                    name: 'Address Line 1',
                    value: 'address'
                },
                {
                    name: 'Client Name',
                    value: 'name'
                },
                {
                    name: 'Email',
                    value: 'email'
                },
                {
                    name: 'Mobile',
                    value: 'phone'
                },
                {
                    name: 'NRIC / Passport',
                    value: 'passport'
                },
                
            ],
            tabIndex: 0,
            activeFilter: false,
            activeStatus: 'Active',
            activeClass: false,
            allSelected: false,
            selected: [],
            actionSearch: null,
            ids:[],
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
                    // {
                    //     key: 'id',
                    //     label: '#',
                    //     isActive: 1,
                    //     keySearch: 'id',
                    //     type: 'text',
                    //     thStyle: "width: 100px",
                    //     isFilter: true,

                    // },
                    {
                        key: 'display_name',
                        label: 'Name',
                        isActive: 1,
                        thStyle: "width: 400px",
                        isFilter: true
                    },
                    {
                        key: 'passport',
                        label: 'NRIC / Passport',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'phone',
                        label: `Mobile`,
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'email',
                        label: `Email`,
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'display_address',
                        label: "Address",
                        isActive: 1,
                        isFilter: true
                    }
                    
                ],
                show: [],
                hide: []
            },

        }
    },
    created(){
        this.handlePanigate(this.customerParam)
    },
    computed: mapState({
        listCustomer: state => state.customer.listCustomer,
    }),
    methods: {
        ...mapActions({
            getListCustomer: 'customer/getListCustomer',
            deleteCustomer: 'customer/deleteCustomer',            
        }),
        gotoCustomerInfo(item){
            this.$router.push({name:"CustomerInfo", params: { id: item.id }})
        },
        handlePanigate(params){
            this.isLoading = true;
            this.getListCustomer(params).then((response)=>{
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
        onCreateNiche() {
            this.$router.push({
                name: "CreateCustomer"
            })
        },
        prevPanigate() {
            let {current_page} = this.listCustomer;
            if (current_page != 1) {
                this.customerParam.page = current_page - 1
                this.handlePanigate(this.customerParam)
            }
        },
        nextPanigate() {
            let {current_page, last_page} = this.listCustomer;
            if (current_page != last_page) {
                this.customerParam.page = current_page + 1
                this.handlePanigate(this.customerParam)
            }
        },
        onChangeSearch(valueSearch, typeSearch){
            let {current_page, last_page} = this.listCustomer;
            clearTimeout(this.actionSearch)
            this.customerParam.filter = {};
            if(!valueSearch){
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.customerParam)
                },300)
            }else{
                
                this.customerParam.filter[typeSearch.value] = valueSearch
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.customerParam)
                },300)
            }
            
        },
        deleteItem(){
            if(this.ids.length == 0){
                 this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Can not find customer."
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
                        this.deleteCustomer(prms)
                        .then(res => {
                            this.$swal({
                                icon: 'success',
                                title: 'Notifcation',
                                text: res.data.status
                            });
                            this.handlePanigate(this.customerParam)
                            this.$refs.adminCustomerTable.reloadData();
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
    },
    watch: {

    }
}
</script>

<style lang="scss" scoped>

</style>
