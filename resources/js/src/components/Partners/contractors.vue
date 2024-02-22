<template>
    <div class="content-tab-table">
        <div class="outside-tab-table" v-bind:class="{'run-loading': isLoading}">
            <ControllTable :isShowIconTrash="showIconTrash" :optionSearch="optionsFilter" :onChangeSearch='onChangeSearch'  :onCreate="onCreateContractor" @deleteItems="deleteItem" />
            <TableCustom ref="adminContractorTable" @Items="getItems" :tableFields="columnActive.fields" :tableItems="listContractor.data" @rowClicked="gotoContractorInfo">
                <template slot="tgor_table:services" slot-scope="data">
                    <div v-if="data.item.services.length">
                        <span v-for="(item,key) in data.item.services" :key="key">
                            <template v-if="data.item.services.length == 1">
                              {{item.service_name}}
                            </template>
                            <template v-else-if="data.item.services.length == key+1">
                               {{item.service_name}}
                            </template>
                            <template v-else>
                               {{item.service_name}},
                            </template>
                        </span>
                    </div>
                    <div v-else>--</div>
                        
                </template>
            </TableCustom>
            <b-row class="pagination">
                <b-col md="12" class="end">
                    <span>
                            {{listContractor.from ? `${listContractor.from}-${listContractor.to} of ${listContractor.total}` : '0-0 of 0'}}
                    </span>
                    <span @click="prevPanigate" class="icon">
                        <b-img class="image" src="/images/left.png" fluid alt="Responsive image"></b-img>
                    </span>
                    <span @click="nextPanigate" class="icon">
                        <b-img class="image" src="/images/right.png" fluid alt="Responsive image"></b-img>
                    </span>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
//import
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from '../../components/Table'
import {
    mapActions,
    mapState
} from 'vuex'

export default {
    components: {
        ControllTable,
        TableCustom
    },
    data() {
        return {
            showIconTrash: true,
            admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
            isLoading: false,
            actionSearch: null,
            ids:[],
            contractorParams:{
                page:1,
                filter:{}
            },
            optionsFilter:[
                 {
                    name: 'All',
                    value: 'all'
                },
                // {
                //     name: 'ID',
                //     value: 'id'
                // },
                {
                    name: 'Company Name',
                    value: 'company_name'
                },
                // {
                //     name: 'Address',
                //     value: 'address'
                // },
                // {
                //     name: 'Website',
                //     value: 'website'
                // },
                {
                    name: 'Services',
                    value: 'services'
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
                        key: 'company_name',
                        label: 'Company Name',
                        isActive: 1,
                        isFilter: true
                    },
                    // {
                    //     key: 'address',
                    //     label: 'Address',
                    //     isActive: 1,
                    //     isFilter: true
                    // },
                    // {
                    //     key: 'website',
                    //     label: `Website`,
                    //     isActive: 1,
                    //     isFilter: true
                    // },
                    {
                        key: 'services',
                        label: `Services`,
                        isActive: 1,
                        isFilter: true
                    },
                    
                ],
                show: [],
                hide: []
            },

        }
    },
    created() {
        if(this.admin_profile.roles_id != 1 ){
            this.showIconTrash = false
        }
        this.handlePanigate(this.contractorParams);
    },
    computed: {
        ...mapState({
            listContractor: state => state.contractor.listContractor,
        }),

    },
    methods: {
        ...mapActions({
            getListContractor: 'contractor/getListContractor',   
            deleteContractor: 'contractor/deleteContractor',      
        }),
        gotoContractorInfo(item){
           this.$router.push({name:"ContractorInfo", params: { id: item.id }})
       },
       handlePanigate(params){
            this.isLoading = true;
            this.getListContractor(params).then((response)=>{
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
            let {current_page} = this.listContractor;
            if (current_page != 1) {
                this.contractorParams.page = current_page - 1
                this.handlePanigate(this.contractorParams)
            }
        },
        nextPanigate() {
            let {current_page, last_page} = this.listContractor;
            if (current_page != last_page) {
                this.contractorParams.page = current_page + 1
                this.handlePanigate(this.contractorParams)
            }
        },
        onChangeSearch(valueSearch, typeSearch){
            let {current_page, last_page} = this.listContractor;
            clearTimeout(this.actionSearch)
            this.contractorParams.filter = {};
            if(!valueSearch){
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.contractorParams)
                },300)
            }else{
                
                this.contractorParams.filter[typeSearch.value] = valueSearch
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.contractorParams)
                },300)
            }
            
        },
        deleteItem(){
            if(this.ids.length == 0){
                 this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Can not find contractor."
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
                        this.deleteContractor(prms)
                        .then(res => {
                            this.$swal({
                                icon: 'success',
                                title: 'Success!',
                                text: res.data.status
                            });
                            this.handlePanigate(this.contractorParams)
                            this.$refs.adminContractorTable.reloadData();
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
        onCreateContractor(){
            this.$router.push({
                name: "CreateContractor"
            })
        }
    },
    watch: {

    }
}
</script>

<style lang="scss" scoped>

</style>
