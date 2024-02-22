<template>
<b-container fluid="lg">
    <div class="columbarium-niches">
        <div class="title">
            <span class="title-name">Users Management</span>
        </div>
    </div>

    <div class="others-table">
        <div class='content' v-bind:class="{'run-loading': isLoading}">
            <div class="outside-table">
                <ControllTable :optionSearch="optionsFilter" :onChangeSearch='onChangeSearch'  :onCreate="onCreateUser" @deleteItems="deleteItem" />
                <TableCustom ref="adminNichesTable" @Items="getItems" :tableFields="columnActive.fields" :tableItems="listUser.data" @rowClicked="gotoUserManagementInfo" >
                    <template slot="tgor_table:admin_status.reference_value_text" slot-scope="data">
                        <div :class="{
                            'active': data.item.admin_status.reference_value_text == 'Active',
                            'inactive': data.item.admin_status.reference_value_text == 'Inactive'
                        }">
                            {{data.item.admin_status.reference_value_text}}
                        </div>
                    </template>
                </TableCustom>
                <b-row class="pagination">
                    <b-col md="12" class="end">
                        <span>
                            {{listUser.from ? `${listUser.from}-${listUser.to} of ${listUser.total}` : '0-0 of 0'}}
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
    metaInfo: {
        title: 'User Management',
        meta: [{
        vmid: 'description',
        name: 'description',
        content: 'User Management Description'
        }]
    },
    data() {
        return {
            isLoading: false,
            actionSearch: null,
            ids:[],
            tabIndex: 0,
            activeFilter: false,
            activeStatus: 'Active',
            activeClass: false,
            allSelected: false,
            selected: [],
            optionsFilter: [
                {
                    name: 'All',
                    value: 'all'
                },
                {
                    name: 'ID',
                    value: 'id'
                },
                {
                    name: 'User Name',
                    value: 'display_name'
                },
                {
                    name: 'Email',
                    value: 'email'
                },
                {
                    name: 'Status',
                    value: 'status'
                }
                
            ],

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
                        key: 'id',
                        label: '#',
                        isActive: 1,
                        keySearch: 'id',
                        type: 'text',
                        thStyle: "width: 50px",
                        isFilter: true,

                    },
                    {
                        key: 'display_name',
                        label: 'User Name',
                        isActive: 1,
                        thStyle: "width: 150px",
                        isFilter: true
                    },
                    {
                        key: 'email',
                        label: `Email`,
                        thStyle: "width: 400px",
                        isActive: 1,
                    },
                    {
                        key: 'admin_status.reference_value_text',
                        label: `Status`,
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
        this.handlePanigate({page: 1});
    },
    computed: mapState({
        listUser: state => state.user.listUser,
    }),
    methods: {
        ...mapActions({
            getlistUser: 'user/getListUser',
            deleteUser: 'user/deleteUser',
        }),
        gotoUserManagementInfo(item) {
            this.$router.push({
                name: "UserInfo",
                params: {
                    id: item.id
                }
            })
        },
        onCreateUser() {
            this.$router.push({
                name: "CreateUser"
            })
        },
        handlePanigate(params){
                this.isLoading = true;
                this.getlistUser(params).then((response)=>{
                    this.isLoading = false;
                }).catch((error)=>{
                    this.isLoading = false;
                    this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.data.errors
                    });
                })
        },
        prevPanigate() {
            let {current_page} = this.getlistUser;
            if (current_page != 1) {
                this.handlePanigate({page:current_page - 1})
            }
        },
        nextPanigate() {
            let {current_page, last_page} = this.getlistUser;
            if (current_page != last_page) {
                this.handlePanigate({page: current_page + 1})
            }
        },
        onChangeSearch(valueSearch, typeSearch){
            let {current_page, last_page} = this.getlistUser;
            clearTimeout(this.actionSearch)
            if(!valueSearch){
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate({page: 1})
                },300)
            }else{
                let filter = {};
                filter[typeSearch.value] = valueSearch
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate({page: 1, filter})
                },300)
            }
            
            
        },
        deleteItem(){
            if(this.ids.length == 0){
                 this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Can not find user."
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
                        this.deleteUser(prms)
                        .then(res => {
                            this.$swal({
                                icon: 'success',
                                title: 'Success!',
                                text: res.data.status
                            });
                            this.handlePanigate({page: 1})
                            this.$refs.adminNichesTable.reloadData();
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
}
</script>

<style lang="scss" scoped>

</style>
