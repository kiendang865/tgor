<template>
    <div class="content-tab-table">
        <div class="outside-tab-table" v-bind:class="{'run-loading': isLoading}">
            <ControllTable :isShowIconTrash="showIconTrash" :optionSearch="optionsFilter" :onChangeSearch='onChangeSearch'  :onCreate="onCreateDireactor" @deleteItems="deleteItem" />
            <TableCustom ref="adminDirectorTable" @Items="getItems" :tableFields="columnActive.fields" :tableItems="listDirector.data" @rowClicked="gotoFuneralDirector">
                <template slot="tgor_table:address" slot-scope="data">
                    {{data.item.address ? data.item.address+" "+data.item.postal_code : '--'}}
                </template>
            </TableCustom>
            <b-row class="pagination">
                <b-col md="12" class="end">
                    <span>
                            {{listDirector.from ? `${listDirector.from}-${listDirector.to} of ${listDirector.total}` : '0-0 of 0'}}
                    </span>
                    <span class="icon">
                        <b-img class="image" src="/images/left.png" fluid alt="Responsive image"></b-img>
                    </span>
                    <span class="icon">
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
            directorParams:{
                page:1,
                filter:{}
            },
            optionsFilter:[
                // {
                //     name: 'All',
                //     value: 'all'
                // },
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
            ],
            items: [
                {
                    id: 2,
                    index: '02',
                    name: 'John Smith',
                    phonenumber: '985684623',
                    email: 'james@email.com',
                },
                {
                    id: 1,
                    index: '01',
                    name: 'John Doe',
                    phonenumber: '985684623',
                    email: 'james@email.com',
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
                    //     isFilter: true,
                    //     thStyle: "width: 100px",

                    // },
                    {
                        key: 'company_name',
                        label: 'Company Name',
                        isActive: 1,
                        isFilter: true
                    },
                    // {
                    //     key: 'address',
                    //     label: `Address`,
                    //     isActive: 1,
                    //     isFilter: true
                    // },
                    // {
                    //     key: 'website',
                    //     label: `Website`,
                    //     isActive: 1,
                    //     isFilter: true
                    // },
                    
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
        this.handlePanigate(this.directorParams);
    },
    // director
    computed: mapState({
        listDirector: state => state.director.listDirector,
    }),
    methods: {
        ...mapActions({
            getListDirector: 'director/getListDirector',   
            deleteDirector: 'director/deleteDirector',      
        }),        
        gotoFuneralDirector(item){
           this.$router.push({name:"FuneralDirectorInfo", params: { id: item.id }})
       },
       handlePanigate(params){
            this.isLoading = true;
            this.getListDirector(params).then((response)=>{
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
            let {current_page} = this.listDirector;
            if (current_page != 1) {
                this.directorParams.page = current_page - 1
                this.handlePanigate(this.directorParams)
            }
        },
        nextPanigate() {
            let {current_page, last_page} = this.listDirector;
            if (current_page != last_page) {
                this.directorParams.page = current_page + 1
                this.handlePanigate(this.directorParams)
            }
        },
        onChangeSearch(valueSearch, typeSearch){
            let {current_page, last_page} = this.listDirector;
            clearTimeout(this.actionSearch)
            this.directorParams.filter = {};
            if(!valueSearch){
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.directorParams)
                },300)
            }else{
                
                this.directorParams.filter[typeSearch.value] = valueSearch
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.directorParams)
                },300)
            }
            
        },
        deleteItem(){
            if(this.ids.length == 0){
                 this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Can not find funeral director."
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
                        this.deleteDirector(prms)
                        .then(res => {
                            this.$swal({
                                icon: 'success',
                                title: 'Success!',
                                text: res.data.status
                            });
                            this.handlePanigate(this.directorParams)
                            this.$refs.adminDirectorTable.reloadData();
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
        onCreateDireactor(){
            this.$router.push({
                name: "CreateDirector"
            })
        }
    },
    watch: {

    }
}
</script>

<style lang="scss" scoped>

</style>
