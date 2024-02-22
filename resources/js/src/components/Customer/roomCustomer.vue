<template>
    <div class="content-tab-table">
        <div class="outside-tab-table" v-bind:class="{'run-loading': isLoading}">
           <ControllTable :isShowIconAdd="false" :isShowIconTrash="false" :optionSearch="optionsFilter" :onChangeSearch='onChangeSearch' @deleteItems="deleteItem"/>
                <TableCustom ref="adminRoomServiceTable" @Items="getItems" :tableFields="columnActive.fields" :tableItems="listServiceRoom.data" >
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
      optionsNiche: [
        {
          name:"30 years",
          value:30
        },
        {
          name:"50 years",
          value:50
        }
      ],
      serviceNicheParams:{
          page:1,
          type:'Niche',
          filter:{},
          user_id: this.$router.history.current.params.id
      },
      extensionParam:{
          information:[
          {
            id:'',
            reference_no:'',
            time:'',
            exten_year:'',
            extension:'',
            renew_price:''
          }
        ]
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
                key: 'booking.booking_no',
                label: 'Booking #',
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
            // {
            //     key: 'status.reference_value_text',
            //     label: 'Status',
            //     isActive: 1,
            //     isFilter: true
            // },
            
        ],
        show: [],
        hide: []
    },
    isLoading:false,
    serviceRoomParams:{
        page:1,
        type:'Room',
        filter:{},
        user_id: this.$router.history.current.params.id,
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
        gotoServiceNiches(item){
            // this.$router.push({name:"BookingInfoServiceNiches", params: { id: item.id }})
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
        customFormatter(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        customFormatDeath(date) {
            return moment(date).format('DD/MM');
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
                    let prms = {type:'Niche',ids:this.ids}
                    this.deleteService(prms)
                    .then(res => {
                        this.$swal({
                            icon: 'success',
                            title: 'Success!',
                            text: res.data.status
                        });
                        this.handlePanigate(this.serviceNicheParams)
                        this.$refs.adminNicheServiceTable.reloadData();
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
          let prms = {
            arr_id: this.ids
          }
          // this.sumTotalNiches(prms).then(res => {
          //   this.extensionParam.amount = res.data.data.total
          // })
          this.totalAmount = '';
          if(this.ids.length)
          {
            this.allowExtent = true;
            this.getInfoExtensionNiche(prms).then(res => {
              this.extensionParam.information = res.data.data
              this.extensionParam.information.map((item,i) => {
                this.extensionParam.information[i].renew_price = ''
              })
            })
            .catch(error => {
                  this.allowExtent = false;
                  this.$swal({
                      icon: 'error',
                      title: 'Oops...',
                      text: error.response.data.errors
                  });
            })
          }

          // console.log(this.extensionParam)
      },
      exportNichesService(){
        this.exportNiches().then(res => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'export_niches.xlsx');
          document.body.appendChild(link);
          link.click();
        }) 
      },
      extensionNiches(){
          if(this.ids.length == 0){
              this.$swal({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Please select booking.'
              });
              return;
          }
          this.$refs.extensionNiche.show()
      },
      onSubmit(){
        let extent_arr = [];

        this.extensionParam.information.map((item,i) => {
          let prms = {
            arr_id : item.line_id,
            duration : item.time?.item.time.id,
            user_id : this.$router.history.current.params.id,
            niche_id : item.id
          }

          extent_arr.push(prms);
        })
        let params = {
          extent_arr:extent_arr,
          user_id: this.$router.history.current.params.id,
        }
    
        this.extensionMutipleNiche(params).then(res => {
            this.$refs.extensionNiche.hide();
            // console.log(res)
            this.$store.commit('booking/updateNRS')
            this.$router.push({name:"BookingGeneralInfo", params: { id: res.data.data.id }})
            this.$swal({
                icon: 'success',
                title: 'Success',
                text: res.data.status
            });
        })
        .catch(error => {
              this.$refs.extensionNiche.hide();
              // console.log(error)
              this.$swal({
                  icon: 'error',
                  title: 'Oops...',
                  text: error.response.data.errors
              });
        })
    },
    priceOfInfo(value,index){

      if(value != null)
      { 
         this.extensionParam.information[index].extension.map((item,i)=> {

          if(item.exten_year == value.exten_year)
          { 
            this.extensionParam.information[index].renew_price = value.exten_price
          }

      })
      }
      else{ 
          this.extensionParam.information[index].renew_price = ''
      }
      var total = 0; 

      this.extensionParam.information.map((v,i) =>{           
          total += (this.formatMoney(v.renew_price));
      })
     this.totalAmount = total;
    },
    numberAmountMask() {
      return createNumberMask({
        prefix: '$',
        suffix: '',
        allowDecimal: true,
        includeThousandsSeparator: true,
        allowLeadingZeroes: true,
        allowNegative: false,
        integerLimit: 8,
        decimalLimit : 2
      });
    },
    formatMoney(value) {
      let val = 0;
      val =  accounting.unformat(value);
      return val;
    },

  },
  filters: {
      formatMoney(val) {
          return accounting.formatMoney(val, { format: { pos : "%s %v", neg : "%s (%v)", zero: "--" } })

      }
  },
}
</script>