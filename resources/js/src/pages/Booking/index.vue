<template>
  <b-container fluid="lg">
    <div class="new-booking justify-content-between">
      <div class="title">
        <span class="title-name">New Booking</span>
      </div>
      <div class="wrapper-btn">
        <b-button class="btn-save" disabled>Calculate Charge</b-button>
        <b-button class="btn-save" @click="onSaveCustomer()">Save</b-button>
      </div>
    </div>
    <div>
      <b-tabs class="tabs-index" v-model="tabIndex" nav-class="nav-cus">
        <b-tab
          nav-class="client-info"
          class="client-info"
          title="Client Info"
          v-bind:class="linkClass(0)"
        >
          <AddBooking @userInfo="getUser" @checkNewCustomer="newCustomer" :disableForm="disableInput" />
        </b-tab>
        <b-tab
          v-if="isSave"
          active
          nav-class="booking-service"
          class="booking-service-index-tab"
          title="Service Type"
          v-bind:class="linkClass(1)"
        >
          <AddBookingService :infoBooking="booking" @bookingInfo="getBooking" />
        </b-tab>
      </b-tabs>
    </div>
  </b-container>
</template>

<script>
import AddBooking from "../../components/Booking/addBooking.vue";
import AddBookingService from "../../components/Booking/newBookingService";
import { EventBus } from "../../event-bus";
import { mapActions, mapState } from "vuex";

export default {
  components: {
    AddBooking,
    AddBookingService,
  },
  metaInfo: {
    title: "New Booking",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "New Booking Description",
      },
    ],
  },
  data() {
    return {
      tabIndex: 0,
      isSave: false,
      userParams: {},
      bookingParams: [],
      isNewCustomer: false,
      isShowServiceProduct: false,
      booking_id:'',
      booking:{},
      disableInput:false,
      onCreate: true
    };
  },
  methods: {
    ...mapActions({
      createCustomer: "customer/createCustomer",
      createBooking: "booking/createBooking",
      updateBooking: 'booking/updateBooking'
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return "";
      } else {
        return "";
      }
    },
    filterActive() {
      this.activeClass = !this.activeClass;
    },
    onSaveCustomer() { 
      if (this.tabIndex == 1) {
        // EventBus.$emit("save-customer-booking");
        EventBus.$emit("save-service-booking");
      }
      else{
         EventBus.$emit("save-customer-booking");
         var flag = false;
         if(!this.isNewCustomer){
            if(this.userParams.id === "")
            {
              this.createCustomer(this.userParams).then(res => {
                this.$swal({
                  icon: "success",
                  title: "Success",
                  text: res.data.status,
                });
                let prms = {'user_id': res.data.data.id, 'is_draft': true};
                this.createBooking(prms).then(response => {
                  this.booking = response.data.data
                  this.booking_id = response.data.data.id
                  this.disableInput = true;
                  this.isSave = true;
                })
              }).catch(error => {
                this.$swal({
                  icon: "warning",
                  title: "Warning",
                  text: error.response.data.errors,
                });
              })
            }
            else{
              let prms = {'user_id': this.userParams.id, 'is_draft': true};
              this.createBooking(prms).then(response => {
                this.booking = response.data.data
                this.booking_id = response.data.data.id
                this.disableInput = true;
                this.isSave = true;
              })
            }
         }
        if(this.userParams.email !== "" && !flag){
          this.isShowServiceProduct = true;
        }
      }
    },
    getUser(item, isValidateError) {
      if(item.id == "" && !this.isNewCustomer){
        this.$swal({
          icon: "warning",
          title: "Warning",
          text: "Please choose a client or create a new client",
        });
        return;
      }
      if(isValidateError && this.isNewCustomer){
        this.$swal({
          icon: "warning",
          title: "Warning",
          text: "Some field are blank. Please fill them up",
        });
        return;
      }
      this.isNewCustomer = isValidateError;
      this.userParams = item;
    },
    getBooking(item) {
      this.bookingParams = item;
    },
    newCustomer(val) {
      this.isNewCustomer = val;
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
  watch: {
    bookingParams: function(val) {
      var arr = [];
      var arrRoom = [];
      var check = false;
      var formData = new FormData();
      val.booking.map((item, key) => {
        // item.id = this.booking_id;
        item.booking_id = this.booking_id;
        item.status = this.booking.status;
        if (item.reference_no) {
          if (arr.length == 0) {
            arr.push(item.reference_no);
          } else {
            let isCheck = arr.includes(item.reference_no);
            if (isCheck) {
              check = true;

              this.$swal({
                icon: "error",
                title: "Oops...",
                text: "Booking niches were duplicated.",
              });
              return;
            } else {
              arr.push(item.reference_no);
            }
          }
        }
        if (item.room_type) {
          if (arrRoom.length == 0) {
            arrRoom.push(item.service_id);
          } else {
            let isCheck = arrRoom.includes(item.service_id);
            if (isCheck) {
              check = true;

              this.$swal({
                icon: "error",
                title: "Oops...",
                text: "Booking rooms were duplicated.",
              });
              return;
            } else {
              arrRoom.push(item.service_id);
            }
          }
        }
      });
      if (!check) {
          let data = this.buildFormData(formData, val);
          let prms = {
            id_booking: this.booking_id,
            booking: data
          }
          this.updateBooking(prms)
            .then((response) => {
              this.$swal({
                icon: "success",
                title: "Success!",
                text: response.data.status,
              });
              this.$store.commit("booking/updateNRS");
              this.onCreate = false
              this.$router.push({
                name: "BookingGeneralInfo",
                params: { id: response.data.data.id },
              });
            })
            .catch((error) => {
              this.$swal({
                icon: "error",
                title: "Oops...",
                text: error.response.data.errors,
              });
            });
        }
    },
  },
  beforeRouteLeave (to, from, next) {
    if(this.isSave && this.onCreate){
      this.$swal({
        title: "Warning",
        text: "Your changes haven't saved yet and it will disappear if proceed. Are you sure to continue?",
        icon: "warning",
        customClass: {
          container: "swal-del-item",
        },
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
      }).then((result) => {
        if (result.value) {
          return next();
        }
      });
    }else{
      return next();
    }
  }
};
</script>

<style lang="scss" scoped></style>
