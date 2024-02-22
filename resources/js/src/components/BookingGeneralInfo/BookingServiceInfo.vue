<template>
  <b-container fluid="lg">
    <div class="booking-service-general">
      <div class="booking-service">
        <b-row>
          <b-col cols="3">
            <b-form-group label=" Booking #">
              <b-form-input class="input-form" :disabled="true" v-model="booking_no"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="3">
            <b-form-group label="Status">
              <multiselect
                :show-labels="false"
                :disabled="isDisible || statusbooking"
                :class="{ 'bg-gray': statusbooking }"
                deselect-label=""
                v-model="status"
                :options="listStatusBooking"
                placeholder="Select one"
                label="reference_value_text"
                track-by="id"
              ></multiselect>
            </b-form-group>
          </b-col>
        </b-row>
      </div>
      <AddService
        v-for="(item, key) in arrServe"
        @chooseType="(...args) => reviceType(...args, key)"
        :itemService="item"
        :serviceType="listTypeBooking"
        :key="item.index"
        class="mt"
        @removeService="removeService(key)"
        :status="status.reference_value_text"
        :hasSaleAgreement="hasSaleAgreement"
        :isDisibleSA="isDisible"
      />

      <b-button :disabled="isDisible || isStatus" class="btn-service" @click="arrServe.push({ index: Math.floor(Math.random() * 100 + 1) })">
        <div class="btn-create">
          <b-img src="/images/Create.png" fluid alt="Responsive image"></b-img>
        </div>
        <div class="pd-3">Add Service</div>
      </b-button>
    </div>
  </b-container>
</template>

<script>
import AddService from "../Service/addService";
import Multiselect from "vue-multiselect";
import { EventBus } from "../../event-bus";
import { mapActions, mapState } from "vuex";
export default {
  components: {
    AddService,
    Multiselect,
  },
  data() {
    return {
      arrServe: [],
      id: this.$router.history.current.params.id,
      booking_no: "",
      isStatus: false,
      status: "",
      statusbooking: false,
      dataStatus: "",
      hasSaleAgreement: false,
      isDisible: false,
    };
  },
  created() {
    this.getListStatusBooking({ id: this.id });
    this.getData();
    this.getListTypeBooking();
    this.arrServe.map((item) => {
      item.index = Math.floor(Math.random() * 100 + 1);
    });
    // EventBus.$on('save-status-booking', this.saveStatus);
  },
  computed: mapState({
    listTypeBooking: (state) => state.contractor.listTypeBooking,
    listServiceBooking: (state) => state.booking.listServiceBooking,
    listStatusBooking: (state) => state.booking.listStatusBooking,
  }),
  methods: {
    ...mapActions({
      getListTypeBooking: "contractor/getListTypeBooking",
      getListBookingGeneral: "booking/getListBookingGeneral",
      getListStatusBooking: "booking/getListStatusBooking",
      updateStatusBooking: "booking/updateStatusBooking",
    }),
    removeService(index) {
      !this.isStatus && this.arrServe.splice(index, 1);
    },
    getData() {
      this.getListBookingGeneral({ id: this.id }).then((res) => {
        this.arrServe = res.data.data.booking_line_items;
        this.booking_no = res.data.data.booking_no;
        this.status = res.data.data.status;
        this.dataStatus = res.data.data.status;
        this.hasSaleAgreement = res.data.data.sale_agreement_count > 0 ? true : false;
        this.isDisible = res.data.data.sale_agreement_count > 0 ? true : false;
        this.$emit("customerInfo", res.data.data.clients, res.data.data.status, res.data.data.is_sale);
        this.$emit("checkExitsSA", this.isDisible);
        console.log(this.isDisible, "9999999999999999999");
        // if(res.data.data.status){
        //     if(res.data.data.status.reference_value_text != "Booked"){
        //         this.isStatus = true;
        //     }
        // }
      });
    },
    reviceType(data) {
      let niches = [];
      let room = [];
      let other = [];
      for (let item of this.arrServe) {
        if (Object.keys(item).length < 3) {
          return;
        }
      }
      let prms = {
        booking: this.arrServe,
        status: this.status,
      };
      this.$emit("bookingInfo", prms);
    },
    setStatus() {
      this.statusbooking = true;
    },
  },
  watch: {
    status: function (val) {
      if (val.reference_value_text == "Cancelled") {
        if (this.dataStatus.reference_value_text == "Cancelled") {
          this.statusbooking = true;
        }
        this.isStatus = true;
      }
    },
  },
};
</script>
