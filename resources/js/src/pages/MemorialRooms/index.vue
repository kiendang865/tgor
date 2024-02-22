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
                <ControllTable :isShowIconAdd="false" :isShowIconTrash="false" :optionSearch="optionsFilter" :onChangeSearch='onChangeSearch' @deleteItems="deleteItem"/>
                <TableCustom ref="adminRoomServiceTable" @Items="getItems" :tableFields="columnActive.fields" :tableItems="listServiceRoom.data" @rowClicked="gotoServiceRoom">
                    <template slot="tgor_table:check_out_date" slot-scope="data">
                      {{data.item.check_in_date ? customFormatter(data.item.check_in_date) + " " + (data.item.check_in_time ? data.item.check_in_time : '' ) : '--'}}
                    </template>
                    <template slot="tgor_table:check_out_date" slot-scope="data">
                      {{data.item.check_out_date ? customFormatter(data.item.check_out_date) + " " + (data.item.check_out_time ? data.item.check_out_time : '' ) : '--'}}
                    </template>
                </TableCustom>
                <b-row class="pagination">
                    <b-col md="12" class="end">
                        <span>
                           {{listServiceRoom.from ? `${listServiceRoom.from}-${listServiceRoom.to} of ${listServiceRoom.total}` : '0-0 of 0'}}
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
//import
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from '../../components/Table'
import moment from 'moment';
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
        title: 'Memorial Rooms',
        meta: [{
        vmid: 'description',
        name: 'description',
        content: 'Memorial Rooms Description'
        }]
    },    
    data() {
        return {
            ids:[],
            isLoading: false,
            serviceRoomParams:{
                page:1,
                type:'Room',
                filter:{}
            },
            optionsFilter: [
                {
                    name: 'All',
                    value: 'all'
                },
                {
                    name: 'Client Name',
                    value: 'client_name'
                },
                {
                    name: 'Check-in Date',
                    value: 'check_in_date'
                },  
                {
                    name: 'Departed Full Name',
                    value: 'departed_full_name'
                },
                {
                    name: 'Funeral Director',
                    value: 'funeral_director'
                },
                {
                    name: 'Room Name',
                    value: 'room_name'
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
                        key: 'index',
                        label: '',
                        thClass: 'checkbox-column text-center',
                        tdClass: 'checkbox-column text-center',
                        thStyle: "width: 20px",
                        isActive: 1
                    },
                    {
                        key: 'booking_no',
                        label: 'Item #',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'room.room_no',
                        label: 'Room Name',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'departed_full_name',
                        label: `Departed Full Name`,
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'check_in_date',
                        label: 'Check-in Date',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'check_out_date',
                        label: 'Check-out Date',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'funeral_director.company_name',
                        label: 'Funeral Director',
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
            isLoading:false,
            roomParams:{
                page:1,
                filter:{}
            }

        }
    },
    created(){
        this.handlePanigate(this.serviceRoomParams)
    },
    computed: mapState({
        listServiceRoom: state => state.service.listServiceRoom,
    }),
    methods: {
        ...mapActions({
            getListServiceRoom: 'service/getListServiceRoom',
            deleteService: 'service/deleteService'          
        }),
        gotoServiceRoom(item){
            this.$router.push({name:"BookingInfoServiceRoom", params: { id: item.id }})
        },
        handlePanigate(params){
            this.isLoading = true;
            this.getListServiceRoom(params).then((response)=>{
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
          let {current_page} = this.listServiceRoom;
          if (current_page != 1) {
              this.serviceRoomParams.page = current_page - 1
              this.handlePanigate(this.serviceRoomParams)
          }
        },
        nextPanigate() {
            let {current_page, last_page} = this.listServiceRoom;
            if (current_page != last_page) {
                this.serviceRoomParams.page = current_page + 1
                this.handlePanigate(this.serviceRoomParams)
            }
        },
        customFormatter(date) {
         return moment(date).format('DD/MM/YYYY');
        },
        onChangeSearch(valueSearch, typeSearch){
          let {current_page, last_page} = this.listServiceRoom;
          clearTimeout(this.actionSearch)
          this.serviceRoomParams.filter = {};
          if(!valueSearch){
              this.actionSearch = setTimeout(()=>{
                  this.handlePanigate(this.serviceRoomParams)
              },300)
          }else{
              
              this.serviceRoomParams.filter[typeSearch.value] = valueSearch
              this.actionSearch = setTimeout(()=>{
                  this.handlePanigate(this.serviceRoomParams)
              },300)
          }
          
        },
        deleteItem(){
        if(this.ids.length == 0){
              this.$swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: "Can not find service."
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
                    let prms = {type:'Room',ids:this.ids}
                    this.deleteService(prms)
                    .then(res => {
                        this.$swal({
                            icon: 'success',
                            title: 'Success!',
                            text: res.data.status
                        });
                        this.handlePanigate(this.serviceRoomParams)
                        this.$refs.adminRoomServiceTable.reloadData();
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
