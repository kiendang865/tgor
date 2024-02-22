<template>
<b-container fluid="lg">
    <div class="columbarium-niches">
        <div class="title">
            <span class="title-name">Additional Services</span>
        </div>
    </div>

    <div class="others-table">
        <div class='content' v-bind:class="{'run-loading': isLoading}">
            <div class="outside-table">
                <ControllTable :isShowIconTrash="showIconTrash" :optionSearch="optionsFilter" :onChangeSearch='onChangeSearch' :onCreate="onCreateNiche" @deleteItems="deleteItem"/>
                <TableCustom ref="adminOtherTable" @Items="getItems" :tableFields="columnActive.fields" :tableItems="listOther.data" @rowClicked="gotoOthersInfo"/>
                <b-row class="pagination">
                    <b-col md="12" class="end">
                        <span>
                            {{listOther.from ? `${listOther.from}-${listOther.to} of ${listOther.total}` : '0-0 of 0'}}
                        </span>
                        <span class="icon" @click="prevPanigate">
                            <b-img class="image" src="images/left.png" fluid alt="Responsive image"></b-img>
                        </span>
                        <span class="icon" @click="nextPanigate">
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
        title: 'Others',
        meta: [{
        vmid: 'description',
        name: 'description',
        content: 'Others Description'
        }]
    },
    data() {
        return {
            showIconTrash: true,
            admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
            isLoading: false,
            actionSearch: null,
            ids:[],
            tabIndex: 0,
            activeFilter: false,
            activeStatus: 'Active',
            activeClass: false,
            allSelected: false,
            selected: [],
            otherParam:{
                page:1,
                filter:{}
            },
            optionsFilter: [
                {
                    name: 'All',
                    value: 'all'
                },
                {
                    name: "Contractor Required",
                    value: 'is_contractor'
                },
                {
                    name: 'Service Name',
                    value: 'service_name'
                },
                {
                    name: 'Type',
                    value: 'type'
                },
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
                        key: 'service_name',
                        label: 'Service / Product',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'category.reference_value_text',
                        label: 'Category',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'type.reference_value_text',
                        label: 'Sale / Rental',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'contractor.reference_value_text',
                        label: 'Contractor Required?',
                        isActive: 1,
                        isFilter: true
                    },
                ],
                show: [],
                hide: []
            },

        }
    },
    created(){
        if(this.admin_profile.roles_id != 1 ){
            this.showIconTrash = false
        }
        this.handlePanigate(this.otherParam)
    },
    computed: mapState({
        listOther: state => state.other.listOther,
    }),
    methods: {
        ...mapActions({
            getListOther: 'other/getListOther',
            deleteOther: 'other/deleteOther',            
        }),
       gotoOthersInfo(item){
           this.$router.push({name:"AdminOtherInfo", params: { id: item.id }})
       },
        handlePanigate(params){
            this.isLoading = true;
            this.getListOther(params).then((response)=>{
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
                name: "CreateOther"
            })
        },
        prevPanigate() {
            let {current_page} = this.listOther;
            if (current_page != 1) {
                this.otherParam.page = current_page - 1
                this.handlePanigate(this.otherParam)
            }
        },
        nextPanigate() {
            let {current_page, last_page} = this.listOther;
            if (current_page != last_page) {
                this.otherParam.page = current_page + 1
                this.handlePanigate(this.otherParam)
            }
        },
        onChangeSearch(valueSearch, typeSearch){
            let {current_page, last_page} = this.listOther;
            clearTimeout(this.actionSearch)
            this.otherParam.filter = {};
            if(!valueSearch){
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.otherParam)
                },300)
            }else{
                
                this.otherParam.filter[typeSearch.value] = valueSearch
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.otherParam)
                },300)
            }
            
        },
        deleteItem(){
            if(this.ids.length == 0){
                 this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Can not find other."
                    });
            }
            else{
                 this.$swal({
                    title: 'Permanently delete?',
                    text: 'This action is irreversible.',
                    icon: 'warning',
                    customClass: {
                        container: "swal-del-item"
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.value) {
                        this.isLoading = true;
                        let prms = {ids:this.ids}
                        this.deleteOther(prms)
                        .then(res => {
                            this.$swal({
                                icon: 'success',
                                title: 'Success!',
                                text: res.data.status
                            });
                            this.handlePanigate(this.otherParam)
                            this.$refs.adminOtherTable.reloadData();
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
