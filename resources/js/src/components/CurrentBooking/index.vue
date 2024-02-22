<template>
<div v-bind:class="{'run-loading':isLoading}" class="booking-info-niches">
    <b-container fluid="lg">
        <div class="content-admin-niches">
            <b-container v-if="form && form.booking_line_item!=null" fluid="lg" class="currently-booking">
                <b-row>
                    <b-col cols="10">
                        <b-row>
                            <b-col cols="6">
                                <div class="title-booking-left">Currenly Booking</div>
                            </b-col>
                            <b-col cols="6">
                                <div class="title-booking-right" @click="editBooking(form.booking_id)">edit</div>
                            </b-col>
                        </b-row>
                        <b-row class="frame-info-booking">
                            <b-col cols="12" >
                                <div v-if="form.booking_line_item.information && form.booking_line_item.information.length > 0">
                                     <b-row class="row-item-info-booking" v-for="(item,key) in form.booking_line_item.information" :key="key">
                                        <b-col cols="3">
                                            <ItemInfoCurrentBooking :value="`${item.first_name ? item.first_name+' '+(item.last_name ? item.last_name: ''): '--' }`" :title="`Occupant 0${+key+1} Name`" />
                                        </b-col>
                                        <b-col cols="3">
                                            <ItemInfoCurrentBooking :value="item.death_anniversary ? customFormatter(item.death_anniversary): '--'" title="Death Anniversary" />
                                        </b-col>
                                        <b-col cols="6">
                                            <ItemInfoCurrentBooking :value="form.booking_line_item && form.booking_line_item.duration_of_lease ? form.booking_line_item.duration_of_lease: '--'" title="Duration of Lease" />
                                        </b-col>
                                    </b-row>
                                </div>
                                <div v-else>
                                    <b-row class="row-item-info-booking">
                                        <b-col cols="3">
                                            <ItemInfoCurrentBooking value="--" title="Occupant 01 Name" />
                                        </b-col>
                                        <b-col cols="3">
                                            <ItemInfoCurrentBooking value="--" title="Death Anniversary" />
                                        </b-col>
                                        <b-col cols="6">
                                            <ItemInfoCurrentBooking value="--" title="Duration of Lease" />
                                        </b-col>
                                    </b-row>
                                </div>
   

                                <b-row>
                                    <b-col cols="3">
                                        <ItemInfoCurrentBooking :value="form.client ? form.client.display_name: '--'" title="Client Name" />
                                    </b-col>
                                    <b-col cols="3">
                                        <ItemInfoCurrentBooking :value="form.client && form.client.phone ? form.client.phone: '--'" title="Phone No." />
                                    </b-col>
                                    <b-col cols="3">
                                        <ItemInfoCurrentBooking :value="form.client && form.client.email!= null ? form.client.email: '--'" title="Email" />
                                    </b-col>
                                    <b-col cols="3">
                                        <ItemInfoCurrentBooking :value="form.client && form.client.preferred_contact_by ? form.client.preferred_contact_by.reference_value_text: '--'" title="Preferred of Contact" />
                                    </b-col>
                                </b-row>

                            </b-col>
                        </b-row>
                    </b-col>
                </b-row>
            </b-container>

        </div>
    </b-container>
</div>
</template>

<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import OtherInfo from "@/components/OtherInfo"
import Calendar from '@/components/Icons/Calendar';
import ServiceNichesBookingFields from '@/components/BookingInfo/ServiceNichesBookingFields';
import ItemInfoCurrentBooking from '../../components/AdminNiches/ItemInfoCurrentBooking'
import createNumberMask from 'text-mask-addons/dist/createNumberMask'
import moment from 'moment'
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from '../../components/Table'

import {
    required,
    minLength,
    between
} from 'vuelidate/lib/validators'
import {
    validationMixin
} from "vuelidate";
import {
    mapActions,
    mapState
} from 'vuex';
import Multiselect from 'vue-multiselect'
import MaskedInput from 'vue-text-mask'
var accounting = require('accounting');

export default {
    mixins: [validationMixin],
    name: "AdminNichesInfo",
    components: {
        ChevronLeft,
        OtherInfo,
        ItemInfoCurrentBooking,
        Multiselect,
        MaskedInput,
        ControllTable,
        TableCustom
    },
    props: {
      bookingData: {
        type: Object,
        default: () => {},
      },
    },
    metaInfo: {
        title: 'Niche Info',
        meta: [{
        vmid: 'description',
        name: 'description',
        content: 'Niche Info Description'
        }]
    },
    data() {
        return {
            tabIndex: 0,
            titleNiches: "Add Niche",
            isLoading: false,
            Calendar,
            checkPrice: false,
            checkPriceThirtyYear:false,
            checkPriceFiftyYear:false,
            statusNiches: [
                'Available',
                'Sold - Occupied',
                'Sold - Unoccupied',
            ],
            form: {
                id: null,
                reference_no: '',
                type_id: '',
                category_id:'',
                price: "",
                price_thirty_years:"",
                price_fifty_years:"",
                bay: '',
                wing: '',
                floor: '',
                block: '',
                level: '',
                unit: '',
                status: '',
                booking_line_item:''
            },
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
                    name: 'Service Type Name',
                    value: 'service_type_name'
                },
                {
                    name: 'Price',
                    value: 'price'
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
                        key: 'id',
                        label: '#',
                        isActive: 1,
                        keySearch: 'id',
                        type: 'text',
                        isFilter: true,

                    },
                    {
                        key: 'exten_year',
                        label: 'Years Extent',
                        isActive: 1,
                        isFilter: true
                    },
                    {
                        key: 'exten_price',
                        label: 'Price',
                        isActive: 1,
                        isFilter: true
                    },
                    
                        ],
                show: [],
                hide: []
            },
            durationParams:{
                page:1,
                filter:{}
            },
        }
    },
    validations: {
        form: {
            reference_no: {
                required,
            },
            type_id: {
                required,
            },
            category_id:{
                required,
            },
            price: {
                required,
                max: 6
            },
            status: {
                required,
            }
        },
    },
    computed: mapState({
        listTypes: state => state.niche.listTypeNiches,
        nicheDetail: state => state.niche.nicheDetail,
        listNicheCategory: state => state.niche.listNicheCategory,
        listDuration: state => state.niche.listDuration,
        
    }),
    created() {
      if(Object.keys(this.bookingData).length > 0){ 
          this.form = this.bookingData;
      }
    },
    watch: {

    },

    methods: {
        linkClass(idx) {
            if (this.tabIndex === idx) {
                return;
            } else {
                return '';
            }
        },
        goBack(){
            this.$router.push('/admin-niches')
        },
        customFormatter(date) {
        return moment(date).format('DD/MM/YYYY');
        },
        editBooking(id_booking){
            this.$router.push({name:"BookingGeneralInfo", params: { id: id_booking }})
        }
    },
}
</script>
