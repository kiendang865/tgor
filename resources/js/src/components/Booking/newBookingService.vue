<template>
    <b-container fluid="lg" >
        <div class="booking-service-new">
            <div class="booking-service">
                <b-row>
                    <b-col cols='12'>
                        <div class="title-input">
                            Booking #
                        </div>
                    </b-col>
                    <b-col cols='3'>
                        <b-form-input class="input-form" v-model="infoBooking.booking_no" disabled  placeholder="BK-N0001"></b-form-input>
                    </b-col>
                </b-row>
            </div>
        <!-- <div class="line-horizontal"></div> -->
            <!-- <template v-if="renderComponent"> -->
                <AddService v-for="(item, key) in arrServe" @chooseType="(...args) => reviceType(...args, key)" :itemService="item" :serviceType="listTypeBooking" :key="item.index" class="mt" @removeService="removeService(key)"/>
            <!-- </template> -->
            <b-button class="btn-service" @click="arrServe.push({index:Math.floor((Math.random() * 100) + 1)})">
                <div class="btn-create">
                    <b-img src="images/Create.png" fluid alt="Responsive image"></b-img>
                </div>
                <div class="pd-3 text-btn">   
                    Add Service
                </div>
            </b-button>
        </div>
        
    </b-container>
</template>

<script>
import AddService from "../Service/addService"
import {
    mapActions,
    mapState
} from 'vuex'
import {
    required,
    minLength,
    between
} from 'vuelidate/lib/validators'

export default {
    components:{
        AddService
    },
    props: {
    infoBooking: {
      type: Object,
      default: () => {},
    },
  },
    data() {
        return {
            arrServe: [{index:Math.floor((Math.random() * 100) + 1)}],
            renderComponent: true
        }
    },
    created(){
        this.getListTypeBooking();
    },
    computed: mapState({
        listTypeBooking: state => state.contractor.listTypeBooking
    }),
    methods:{
        ...mapActions({
            getListTypeBooking: "contractor/getListTypeBooking",
            createBooking: "booking/createBooking"
        }),
        removeService (index){ 
            if(index != 0)
            {
               this.$nextTick(() => {
                    this.arrServe.splice(index, 1);
               })
            }
            
           
            
        },
        reviceType(){  
            let niches = [];
            let room = [];
            let other = [];
            for(let item of this.arrServe){
                if(Object.keys(item).length <3){ 
                    return;
                }
                else{

                } 
            }
            let prms = {
                booking: this.arrServe,
                status: this.infoBooking.status,
                id_booking: this.infoBooking.id,
            }
            this.$emit('bookingInfo',prms)
        }
    }
}
</script>
