<template>
  <div class="admin-room-info">
    <b-container fluid="lg">
      <div class="contractors d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Add Room
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div>
        <b-tabs class="tabs-index" v-model="tabIndex" nav-class="nav-cus">
          <b-tab nav-class="client-info" class="basic-info" title="Basic Info" v-bind:class="linkClass(0)">
            <b-container fluid="lg" class="admin-setup-basic-info">
              <b-row>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Room Name <span class="_require">*</span></label>
                    <b-form-input
                      autofocus
                      :class="{
                        'form-group--error': $v.roomInfoParams.room_no.$error,
                      }"
                      v-model.trim="$v.roomInfoParams.room_no.$model"
                      class="input-form"
                    ></b-form-input>
                    <div class="error" v-if="!$v.roomInfoParams.room_no.required && $v.roomInfoParams.room_no.$error">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Daily Rate <span class="_require">*</span></label>
                    <masked-input
                      type="text"
                      :class="{ 'form-group--error': checkPriceDaily }"
                      v-model.trim="$v.roomInfoParams.price_daily.$model"
                      class="form-control"
                      :mask="numberAmountMask()"
                      :guide="true"
                      placeholder="$0.00"
                    >
                    </masked-input>
                    <div class="error" v-if="checkPriceDaily">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Hourly Rate <span class="_require">*</span></label>
                    <masked-input
                      type="text"
                      :class="{ 'form-group--error': checkPriceOurly }"
                      v-model.trim="$v.roomInfoParams.price_hourly.$model"
                      class="form-control"
                      :mask="numberAmountMask()"
                      :guide="true"
                      placeholder="$0.00"
                    >
                    </masked-input>
                    <div class="error" v-if="checkPriceOurly">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Status <span class="_require">*</span></label>
                    <multiselect
                      :class="{
                        'form-group--error': $v.roomInfoParams.status.$error,
                      }"
                      v-model.trim="$v.roomInfoParams.status.$model"
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :options="listStatusRoom"
                      track-by="id"
                      label="reference_value_text"
                      placeholder="Select one"
                    ></multiselect>
                    <div class="error" v-if="!$v.roomInfoParams.status.required && $v.roomInfoParams.status.$error">Field is required</div>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-container>
          </b-tab>
          <!-- <b-tab nav-class="booking-service" class="booking-log" title="Booking Log" v-bind:class="linkClass(1)">
                <BookingLog/>
            </b-tab> -->
        </b-tabs>
      </div>
    </b-container>
  </div>
</template>

<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import BasicInfo from "@/components/BasicInfo";
import BookingLog from "@/components/BookingLog";
import Datepicker from "vuejs-datepicker";
import Multiselect from "vue-multiselect";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
import MaskedInput from "vue-text-mask";
var accounting = require("accounting");

import { required, minLength, between } from "vuelidate/lib/validators";
import { mapActions, mapState } from "vuex";

export default {
  components: {
    ChevronLeft,
    BasicInfo,
    BookingLog,
    Datepicker,
    Multiselect,
    MaskedInput,
  },
  metaInfo: {
    title: "Room Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Room Info Description",
      },
    ],
  },
  data() {
    return {
      tabIndex: 0,
      valueNiche: null,
      checkPrice: false,
      statusRoom: ["Booked", "Agreement", "Partially Invoiced", "Fully Invoiced", "Partially Paid", "Fully Paid"],
      roomInfoParams: {
        room_no: "",
        price_daily: "",
        price_hourly: "",
        status: "",
      },
      checkPriceDaily: false,
      checkPriceOurly: false,
    };
  },
  validations: {
    roomInfoParams: {
      room_no: {
        required,
      },
      price_daily: {
        required,
      },
      price_hourly: {
        required,
      },
      status: {
        required,
      },
    },
  },
  created() {
    this.getListTypeTV();

    this.getListStatusAdminRoom();
  },
  computed: mapState({
    listTypeTV: (state) => state.room.listTypeTV,
    listStatusRoom: (state) => state.room.listStatusRoom,
  }),
  methods: {
    ...mapActions({
      getListTypeTV: "room/getListTypeTV",
      createRoom: "room/createRoom",
      getListStatusAdminRoom: "room/getListStatusAdminRoom",
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
    goBack() {
      // @click="goBack"
      this.$router.push("/admin-memorial-room");
    },
    getRoomDetail(idRoom) {
      this.roomDetail(idRoom).then((res) => {
        this.roomInfoParams = res;
        this.roomInfoParams.tv_dimention_id = res.tv_dimention;
      });
    },
    onSave() {
      this.$v.roomInfoParams.$touch();
      if (this.$v.roomInfoParams.$anyError) {
        if (this.roomInfoParams.price_daily == "") {
          this.checkPriceDaily = true;
        }
        if (this.roomInfoParams.price_hourly == "") {
          this.checkPriceOurly = true;
        }
        return;
      } else {
        let prms = { ...this.roomInfoParams };

        prms.price_daily = this.formatMoney(prms.price_daily);

        prms.price_hourly = this.formatMoney(prms.price_hourly);

        prms.status = prms.status.id;
        this.createRoom(prms)
          .then((res) => {
            this.$router.replace(`admin-room-info/${res.data.data.id}`);
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });
          })
          .catch((error) => {
            this.isLoading = false;
            this.$swal({
              icon: "error",
              title: "Oops...",
              text: error.response.data.errors,
            });
          });
      }
    },
    numberAmountMask() {
      return createNumberMask({
        prefix: "$",
        suffix: "",
        allowDecimal: true,
        includeThousandsSeparator: true,
        allowLeadingZeroes: true,
        allowNegative: false,
        integerLimit: 8,
        decimalLimit: 2,
      });
    },
    formatMoney(value) {
      let val = 0;
      val = accounting.unformat(value);
      return val;
    },
  },
  watch: {
    listTypeTV: function (val) {
      this.roomInfoParams.tv_dimention_id = val[0];
    },
    "roomInfoParams.price_daily": function (val) {
      if (val == "") {
        this.checkPriceDaily = true;
      } else {
        this.checkPriceDaily = false;
      }
    },
    "roomInfoParams.price_hourly": function (val) {
      if (val == "") {
        this.checkPriceOurly = true;
      } else {
        this.checkPriceOurly = false;
      }
    },
    listStatusRoom: function (val) {
      this.roomInfoParams.status = val[0];
    },
  },
};
</script>

<style lang="scss" scoped></style>
