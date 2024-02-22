<template>
<b-container fluid="lg">
    <div class="columbarium-niches">
        <div class="title">
            <span class="title-name">Memorial Rooms</span>
        </div>
    </div>

    <div class="others-table">
        <div class='content' v-bind:class="{'run-loading': isLoading}">
            <div class="outside-table">
                <ControllTable :isShowIconTrash="showIconTrash" :optionSearch="optionsFilter" :isShowIconExport="false" :onChangeSearch='onChangeSearch' @deleteItems="deleteItem" :onCreate="onCreateNiche"/>
                <TableCustom ref="adminRoomTable" :tableFields="columnActive.fields" @Items="getItems" :tableItems="listRoom.data" @rowClicked="gotoRoomInfo">
                    <template slot="tgor_table:price_daily" slot-scope="data">
                          {{data.item.price_daily && data.item.price_daily != '' ? +data.item.price_daily : 0 | formatMoney}}
                    </template>
                    <template slot="tgor_table:price_hourly" slot-scope="data">
                          {{data.item.price_hourly && data.item.price_hourly != ''? data.item.price_hourly : 0 | formatMoney }}
                    </template>
                </TableCustom>
                <b-row class="pagination">
                    <b-col md="12" class="end">
                        <span>
                             {{listRoom.from ? `${listRoom.from}-${listRoom.to} of ${listRoom.total}` : '0-0 of 0'}}
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
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from '../../components/Table'
import {
    mapActions,
    mapState
} from 'vuex'
const accounting = require('accounting');
export default {
    components: {
        ControllTable,
        TableCustom
    },
    metaInfo: {
        title: 'Memorial Rooms',
        meta: [{
        vmid: 'description',
        name: 'description',
        content: 'Memorial Rooms Description'
        }]
    },

    data() {
        return {
            showIconTrash: true,
            admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
            isLoading:false,
            roomParams:{
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
            optionsFilter: [
                {
                    name: 'All',
                    value: 'all'
                },
                {
                    name: 'Room Name',
                    value: 'room_no'
                },
                {
                    name: 'Price - Daily',
                    value: 'price_daily'
                },
                {
                    name: 'Price - Hourly',
                    value: 'price_hourly'
                },
                {
                    name: 'Status',
                    value: 'status'
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
                        key: 'room_no',
                        label: 'Room Name',
                        isActive: 1,
                        keySearch: 'id',
                        type: 'text',
                        thStyle: "width: 150px;",
                        isFilter: true,

                    },
                    {
                        key: 'price_daily',
                        label: 'Daily Rate',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: `price_hourly`,
                        label: 'Hourly Rate',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'status.reference_value_text',
                        label: 'Status',
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
        this.handlePanigate(this.roomParams)
    },
    computed: mapState({
        listRoom: state => state.room.listRoom,
    }),
    methods: {
        ...mapActions({
            getListRoom: 'room/getListRoom',   
            deleteRoom: 'room/deleteRoom',      
        }),
        gotoRoomInfo(item){
           this.$router.push({name:"AdminRoomInfo", params: { id: item.id }})
        },
        handlePanigate(params){
            this.isLoading = true;
            this.getListRoom(params).then((response)=>{
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
            let {current_page} = this.listRoom;
            if (current_page != 1) {
                this.roomParams.page = current_page - 1
                this.handlePanigate(this.roomParams)
            }
        },
        nextPanigate() {
            let {current_page, last_page} = this.listRoom;
            if (current_page != last_page) {
                this.roomParams.page = current_page + 1
                this.handlePanigate(this.roomParams)
            }
        },
        onChangeSearch(valueSearch, typeSearch){
            let {current_page, last_page} = this.listRoom;
            clearTimeout(this.actionSearch)
            this.roomParams.filter = {};
            if(!valueSearch){
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.roomParams)
                },300)
            }else{
                
                this.roomParams.filter[typeSearch.value] = valueSearch
                this.actionSearch = setTimeout(()=>{
                    this.handlePanigate(this.roomParams)
                },300)
            }
            
        },
        deleteItem(){
            if(this.ids.length == 0){
                 this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Can not find memorial room."
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
                        this.deleteRoom(prms)
                        .then(res => {
                            this.$swal({
                                icon: 'success',
                                title: 'Success!',
                                text: res.data.status
                            });
                            this.handlePanigate(this.roomParams)
                            this.$refs.adminRoomTable.reloadData();
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
        }

    },
    filters: {
        formatMoney(val) {
            return accounting.formatMoney(val, { format: { pos : "%s %v", neg : "%s (%v)", zero: "--" } })

        }
    },
    watch: {

    }
}
</script>

<style lang="scss" scoped>

</style>
