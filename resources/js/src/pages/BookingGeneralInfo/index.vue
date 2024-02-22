<template>
  <div class="booking-info-general">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Booking Info
          </span>
        </div>
        <div class="wrapper-btn">
          <template v-if="isDisableSA">
            <b-button class="btn-change" :disabled="true">Generate SA</b-button>
            <b-button class="btn-save" :disabled="true">Save</b-button>
          </template>
          <template v-else>
            <b-button class="btn-change" :disabled="isStatus || isDisableSA" @click="caculateCharge">Generate SA</b-button>
            <b-button class="btn-save" :disabled="isStatus" @click="onSaveCustomer">Save</b-button>
          </template>
        </div>
      </div>
      <b-tabs class="tabs-index" v-model="tabIndex" nav-class="nav-cus">
        <b-tab
          nav-class="client-info"
          class="client-info"
          title="Client Info"
          v-bind:class="linkClass(0)"
        >
          <ClientInfo :customerItem="userParams" />
        </b-tab>
        <b-tab
          nav-class="booking-service-tab"
          class="booking-general-tab-index"
          title="Service Type"
          v-bind:class="linkClass(1)"
        >
          <BookingServiceInfo
            @customerInfo="getUser"
            @bookingInfo="getBooking"
            @checkExitsSA = "checkExitsSA"
            ref="BookingServiceInfo"
          />
        </b-tab>
      </b-tabs>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import ClientInfo from "@/components/BookingGeneralInfo/ClientInfo";
import BookingServiceInfo from "@/components/BookingGeneralInfo/BookingServiceInfo";
import Calendar from "@/components/Icons/Calendar";
import { EventBus } from "../../event-bus";
import { mapActions, mapState } from "vuex";
import {isObject} from "lodash"
export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    ClientInfo,
    BookingServiceInfo,
    Calendar,
  },
  metaInfo: {
    title: "Booking Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Booking Info Description",
      },
    ],
  },
  data() {
    return {
      tabIndex: 0,
      isSave: false,
      userParams: {},
      bookingParams: [],
      isCharge: false,
      isStatus: true,
      isSale: false,
      isDisableSA: true,
      checkSA: false,
      btnSave:false
    };
  },
  methods: {
    ...mapActions({
      // createCustomer: 'customer/createCustomer',
      updateBooking: "booking/updateBooking",
      caculateChageBooking: "saleareement/caculateChageBooking",
      updateStatusBooking: "booking/updateStatusBooking",
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return "";
      }
    },
    checkExitsSA(val){
      this.isDisableSA  = val
    },
    goBack() {
      // @click="goBack"
      this.$router.push("/booking-general");
    },
    onSaveCustomer() {
      if (this.tabIndex == 1) {
        EventBus.$emit("save-customer-booking");
        EventBus.$emit("save-service-booking");
        EventBus.$emit("save-status-booking");
      }
    },
    getUser(item, status, isSale) {
      this.userParams = item;
      if(status.reference_value_text == "Booked"){
        this.isDisableSA = false;
        this.isStatus = false;
      }
      if(status.reference_value_text != "Booked"){ 
        this.isCharge = true;
        this.isStatus = false;
      }
      if(status.reference_value_text == "Cancelled")
      {
        this.isStatus = true;
      }
      if(status.reference_value_text == "Draft"){
        this.isDisableSA = true;
        this.isStatus = false;
      }
      this.isSale = isSale;
    },
    getBooking(item) {
      this.bookingParams = item;
    },
    caculateCharge() {
      let prms = {
        id: this.$router.history.current.params.id,
        is_sale: this.isSale,
      };
      this.caculateChageBooking(prms)
        .then((res) => {
          this.$router.push({ name: "BookingGeneral" });
        })
        .catch((error) => {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
    buildFormData(formData, data, parentKey) {
      if (
        data &&
        typeof data === "object" &&
        !(data instanceof Date) &&
        !(data instanceof File) &&
        !(data instanceof Blob)
      ) {
        Object.keys(data).forEach((key) => {
          this.buildFormData(
            formData,
            data[key],
            parentKey ? `${parentKey}[${key}]` : key
          );
        });
      } else {
        const value = data == null ? "" : data;

        formData.append(parentKey, value);
      }
      return formData;
    },
  },
  watch:{
    'bookingParams': function (val) { 
      if(val.status.reference_value_text== "Cancelled")
      {
        this.$nextTick(() => {
          this.isStatus = true;
        });
      }

      val.status = val.status.id;
      val.id_booking = this.$router.history.current.params.id;

      // one or more object in this prms booking
      if (val?.booking.length > 0) {
        val?.booking.map((item, index) => {
          if (isObject(item.relationship_to_applicant)) {
            item.relationship_to_applicant = item.relationship_to_applicant.id;
          }
        });
      }

      let formData = new FormData();
      let data = this.buildFormData(formData, val);
      let params = {
        id_booking: val.id_booking,
        booking: data,
      };
      this.updateBooking(params)
        .then((response) => {
          this.$swal({
            icon: "success",
            title: "Success!",
            text: response.data.status,
          });
          this.$refs.BookingServiceInfo.setStatus();
          this.$refs.BookingServiceInfo.getData();
        })
        .catch((error) => {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
  },
};
</script>