<template>
  <div class="booking-info-room">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Memorial Rooms Booking Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div>
        <b-tabs class="tabs-index" v-model="tabIndex" nav-class="nav-cus">
          <b-tab nav-class="booking-service" class="booking-service" title="Booking Details" v-bind:class="linkClass(0)">
            <b-container fluid="lg" class="booking-service-room-field">
              <b-row>
                <b-col cols="3">
                  <b-form-group label="Item #">
                    <div class="position-relative input-date">
                      <b-form-input v-model.trim="bookingParam.booking_no" class="input-form" :disabled="true"></b-form-input>
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Booking Date <span class="_require">*</span></label>
                    <div class="position-relative input-date">
                      <Datepicker
                        :class="{
                          'form-group--error': $v.bookingParam.booking_date.$error,
                        }"
                        v-model.trim="$v.bookingParam.booking_date.$model"
                        class="choose-date"
                        placeholder="dd/mm/yyyy"
                        :format="customFormatter"
                      ></Datepicker>
                      <div class="error-date" v-if="!$v.bookingParam.booking_date.required && $v.bookingParam.booking_date.$error">
                        Field is required
                      </div>
                      <Calendar />
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Event Type <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.bookingParam.event_id.$error,
                      }"
                      v-model.trim="$v.bookingParam.event_id.$model"
                      :options="listEvent"
                      placeholder="Select one"
                      label="reference_value_text"
                      track-by="id"
                    ></multiselect>
                    <div class="error-date" v-if="!$v.bookingParam.event_id.required && $v.bookingParam.event_id.$error">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group label="Status">
                    <multiselect
                      :show-labels="false"
                      deselect-label=""
                      class="bg-gray"
                      v-model.trim="bookingParam.status"
                      :disabled="true"
                      :options="listStatusRoom"
                      placeholder="Select one"
                      track-by="id"
                      label="reference_value_text"
                    ></multiselect>
                    <!-- <div class="error" v-if="!$v.adminParams.status.required && $v.adminParams.status.$error">Status is required</div>  -->
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="mt">
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Room Name <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.bookingParam.service_id.$error,
                      }"
                      v-model.trim="$v.bookingParam.service_id.$model"
                      :options="listRoomForBooking"
                      placeholder="Select one"
                      label="room_no"
                      track-by="id"
                    ></multiselect>
                    <div class="error" v-if="!$v.bookingParam.service_id.required && $v.bookingParam.service_id.$error">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Booking Type <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.bookingParam.room_type.$error,
                      }"
                      v-model.trim="$v.bookingParam.room_type.$model"
                      :options="listRoomType"
                      placeholder="Select one"
                      label="reference_value_text"
                      track-by="id"
                    ></multiselect>
                    <div class="error" v-if="!$v.bookingParam.room_type.required && $v.bookingParam.room_type.$error">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Price <span class="_require">*</span></label>
                    <masked-input
                      type="text"
                      class="form-control"
                      :mask="numberAmountMask()"
                      :guide="true"
                      :disabled="true"
                      v-model="bookingParam.amount"
                      placeholder="$0.00"
                    >
                    </masked-input>
                    <!-- :class="{'form-group--error': checkPrice }"
                      v-model.trim="$v.otherParams.price.$model" -->
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="mt">
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Check-in Date <span class="_require">*</span></label>
                    <div class="position-relative input-date">
                      <Datepicker
                        class="choose-date"
                        placeholder="dd/mm/yyyy"
                        :class="{
                          'form-group--error': $v.bookingParam.check_in_date.$error,
                        }"
                        v-model.trim="$v.bookingParam.check_in_date.$model"
                        :format="customFormatter"
                      ></Datepicker>
                      <div class="error-date" v-if="!$v.bookingParam.check_in_date.required && $v.bookingParam.check_in_date.$error">
                        Field is required
                      </div>
                      <Calendar />
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group label="Check-in Time">
                    <label class="_label_input">Check-in Time <span class="_require">*</span></label>
                    <div class="position-relative input-time">
                      <!-- <Datepicker class="choose-date" placeholder="dd/mm/yyyy" v-model="state.date2"></Datepicker> -->
                      <vue-timepicker
                        class="choose-date"
                        :class="{
                          'form-group--error': $v.bookingParam.check_in_time.$error,
                        }"
                        v-model.trim="$v.bookingParam.check_in_time.$model"
                        format="HH:mm"
                      ></vue-timepicker>
                      <div class="error-date" v-if="!$v.bookingParam.check_in_time.required && $v.bookingParam.check_in_time.$error">
                        Field is required
                      </div>
                      <Clock />
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Check-out Date <span class="_require">*</span></label>
                    <div class="position-relative input-date">
                      <Datepicker
                        class="choose-date"
                        placeholder="dd/mm/yyyy"
                        :class="{
                          'form-group--error': $v.bookingParam.check_out_date.$error,
                        }"
                        v-model.trim="$v.bookingParam.check_out_date.$model"
                        :format="customFormatter"
                      ></Datepicker>
                      <div class="error-date" v-if="!$v.bookingParam.check_out_date.required && $v.bookingParam.check_out_date.$error">
                        Field is required
                      </div>
                      <Calendar />
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Check-out Time <span class="_require">*</span></label>
                    <div class="position-relative input-time">
                      <vue-timepicker
                        class="choose-date"
                        :class="{
                          'form-group--error': $v.bookingParam.check_out_time.$error,
                        }"
                        v-model.trim="$v.bookingParam.check_out_time.$model"
                        format="HH:mm"
                      ></vue-timepicker>
                      <div class="error-date" v-if="!$v.bookingParam.check_out_time.required && $v.bookingParam.check_out_time.$error">
                        Field is required
                      </div>
                      <Clock />
                    </div>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="mt">
                <b-col cols="6">
                  <b-form-group label="Departed Full Name">
                    <b-form-input class="input-form" v-model="bookingParam.departed_full_name"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group label="Date of Death">
                    <div class="position-relative input-date">
                      <Datepicker
                        class="choose-date"
                        placeholder="dd/mm/yyyy"
                        v-model="bookingParam.death_date"
                        :format="customFormatter"
                      ></Datepicker>
                      <Calendar />
                    </div>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="mt">
                <b-col cols="6">
                  <b-form-group label="Church Attended">
                    <b-form-input class="input-form" v-model="bookingParam.church_attended" placeholder="Name"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group label="Relationship to Applicant">
                    <multiselect
                      :show-labels="false"
                      deselect-label=""
                      v-model="bookingParam.relationship_to_applicant"
                      :options="listRelationship"
                      placeholder="Select one"
                      label="reference_value_text"
                      track-by="id"
                    ></multiselect>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="mt">
                <b-col cols="6">
                  <b-form-group label="Remarks">
                    <b-form-input class="input-form" v-model="bookingParam.remarks"></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="mt">
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">FD Referral? <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.bookingParam.book_funeral_director.$error,
                      }"
                      v-model.trim="$v.bookingParam.book_funeral_director.$model"
                      :options="yesNoOptions"
                      placeholder="Select one"
                      label="name"
                      track-by="name"
                    >
                    </multiselect>
                    <div class="error" v-if="!$v.bookingParam.book_funeral_director.required && $v.bookingParam.book_funeral_director.$error">
                      Field is required
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3" v-if="isFuneral">
                  <b-form-group>
                    <label class="_label_input">Funeral Director <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.bookingParam.funeral_director_id.$error,
                      }"
                      v-model.trim="$v.bookingParam.funeral_director_id.$model"
                      :options="listDirector"
                      placeholder="Select one"
                      label="company_name"
                      track-by="name"
                    >
                    </multiselect>
                    <div class="error" v-if="!$v.bookingParam.funeral_director_id.required && $v.bookingParam.funeral_director_id.$error">
                      Field is required
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3" v-if="isFuneral">
                  <b-form-group>
                    <label class="_label_input">Is Applicant the Co-ordinator? <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.bookingParam.applicant_is_coordinator.$error,
                      }"
                      v-model.trim="$v.bookingParam.applicant_is_coordinator.$model"
                      :options="yesNoOptions"
                      placeholder="Select one"
                      label="name"
                      track-by="name"
                    >
                    </multiselect>
                    <div class="error" v-if="!$v.bookingParam.applicant_is_coordinator.required && $v.bookingParam.applicant_is_coordinator.$error">
                      Field is required
                    </div>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="mt" v-if="isCood">
                <b-col cols="6">
                  <b-form-group label="Co-ordinator Full Name">
                    <b-form-input class="input-form" v-model="bookingParam.coordinator_full_name"></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group label="Mobile">
                    <b-form-input class="input-form" v-model="bookingParam.mobile"></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <!-- <b-row class="mt">
                <b-col cols="6">
                  <b-form-group label="TV - Display Name">
                    <b-form-input
                      class="input-form"
                      v-model="bookingParam.tv_display_name"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col cols="6">
                  <b-form-group label="TV- Photo of Departed">
                    <b-form-file
                      v-model="bookingParam.tv_photo"
                      class="mt-3"
                      id="upload-photo"
                      accept="image/jpeg, image/png, image/gif"
                      plain
                    ></b-form-file>
                    <label for="upload-photo" class="btn-file-report"
                      >Select File</label
                    >
                    {{
                      bookingParam.tv_photo && bookingParam.tv_photo.name
                        ? bookingParam.tv_photo.name
                        : ""
                    }}
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="mt">
                <b-col cols="3">
                  <b-form-group label="TV - Life Years">
                    <b-form-input
                      class="input-form"
                      v-model="bookingParam.tv_life_years"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group label="TV  - Wake Service">
                    <b-form-input
                      class="input-form"
                      v-model="bookingParam.tv_wake_service"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group label="TV - Encoffin Service">
                    <b-form-input
                      class="input-form"
                      v-model="bookingParam.tv_encoffin_service"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group label="TV - Cortage Leaves">
                    <b-form-input
                      class="input-form"
                      v-model="bookingParam.tv_cottage_leaves"
                    ></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row> -->
              <b-row>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Do you have a referral? <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.bookingParam.is_referral.$error,
                      }"
                      v-model.trim="$v.bookingParam.is_referral.$model"
                      :options="listReferral"
                      placeholder="Select one"
                      label="reference_value_text"
                      track-by="id"
                    ></multiselect>
                    <div class="error-date" v-if="!$v.bookingParam.is_referral.required && $v.bookingParam.is_referral.$error">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="6" v-if="isReferral">
                  <b-form-group>
                    <label class="_label_input">Name <span class="_require">*</span></label>
                    <b-form-input
                      :class="{
                        'form-group--error': $v.bookingParam.referral_name.$error,
                      }"
                      v-model.trim="$v.bookingParam.referral_name.$model"
                      class="input-form"
                    ></b-form-input>
                    <div class="error" v-if="!$v.bookingParam.referral_name.required && $v.bookingParam.referral_name.$error">Field is required</div>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-container>
          </b-tab>
          <b-tab nav-class="client-info" class="client-info" title="Client Info" v-bind:class="linkClass(1)">
            <CustomerInfo :customerItem="data.client" />
          </b-tab>
          <b-tab nav-class="client-info" class="client-info" title="Remarks" v-bind:class="linkClass(1)">
            <Remarks />
          </b-tab>
        </b-tabs>
      </div>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import Remarks from "@/components/Remarks";
import CustomerInfo from "@/components/CustomerInfo";
import Datepicker from "vuejs-datepicker";
import Calendar from "@/components/Icons/Calendar";
import Clock from "@/components/Icons/Clock";
import Multiselect from "vue-multiselect";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
import VueTimepicker from "vue2-timepicker";
var accounting = require("accounting");
import moment from "moment";
import { mapActions, mapState } from "vuex";
import { required, minLength, between } from "vuelidate/lib/validators";
import { EventBus } from "../../../event-bus";
import axios from "axios";
export default {
  name: "ServiceRoomBooking",
  components: {
    ChevronLeft,
    Remarks,
    CustomerInfo,
    Datepicker,
    Calendar,
    Clock,
    Multiselect,
    MaskedInput,
    VueTimepicker,
  },
  data() {
    return {
      data: {},
      id: this.$router.history.current.params.id,
      tabIndex: 0,
      optionsNiche: [],
      eventOptions: [{ name: "Event 1" }, { name: "Event 2" }],
      yesNoOptions: [{ name: "Yes" }, { name: "No" }],
      bookingParam: {
        id: "",
        booking_type_id: "",
        user_id: "",
        booking_no: "",
        event_id: "",
        service_id: "",
        amount: "",
        booking_date: "",
        room_type: "",
        check_in_date: "",
        check_in_time: "",
        check_out_date: "",
        check_out_time: "",
        departed_full_name: "",
        death_date: "",
        church_attended: "",
        relationship_to_applicant: "",
        book_funeral_director: null,
        funeral_director_id: null,
        applicant_is_coordinator: null,
        coordinator_full_name: "",
        mobile: "",
        status: "",
        remarks: "",
        tv_life_years: "",
        tv_wake_service: "",
        tv_encoffin_service: "",
        tv_cottage_leaves: "",
        tv_photo: null,
        is_referral: null,
        referral_name: "",
      },
      isFuneral: false,
      isCood: true,
      infoRoom: {},
      isReferral: false,
    };
  },
  validations() {
    if (this.isFuneral == false && this.isReferral == false) {
      return {
        bookingParam: {
          event_id: {
            required,
          },
          service_id: {
            required,
          },
          booking_date: {
            required,
          },
          room_type: {
            required,
          },
          check_in_date: {
            required,
          },
          check_in_time: {
            required,
          },
          check_out_date: {
            required,
          },
          check_out_time: {
            required,
          },
          book_funeral_director: {
            required,
          },
          is_referral: {},
          referral_name: {},
        },
      };
    } else {
      if (this.isFuneral == true && this.isReferral == false) {
        return {
          bookingParam: {
            event_id: {
              required,
            },
            service_id: {
              required,
            },
            booking_date: {
              required,
            },
            room_type: {
              required,
            },
            check_in_date: {
              required,
            },
            check_in_time: {
              required,
            },
            check_out_date: {
              required,
            },
            check_out_time: {
              required,
            },
            book_funeral_director: {
              required,
            },
            applicant_is_coordinator: {
              required,
            },
            is_referral: {},
            referral_name: {},
            funeral_director_id: {
              required,
            },
          },
        };
      } else {
        if (this.isFuneral == false && this.isReferral == true) {
          return {
            bookingParam: {
              event_id: {
                required,
              },
              service_id: {
                required,
              },
              booking_date: {
                required,
              },
              room_type: {
                required,
              },
              check_in_date: {
                required,
              },
              check_in_time: {
                required,
              },
              check_out_date: {
                required,
              },
              check_out_time: {
                required,
              },
              book_funeral_director: {
                required,
              },
              is_referral: {
                required,
              },
              referral_name: {
                required,
              },
              funeral_director_id: {},
            },
          };
        } else {
          return {
            bookingParam: {
              event_id: {
                required,
              },
              service_id: {
                required,
              },
              booking_date: {
                required,
              },
              room_type: {
                required,
              },
              check_in_date: {
                required,
              },
              check_in_time: {
                required,
              },
              check_out_date: {
                required,
              },
              check_out_time: {
                required,
              },
              book_funeral_director: {
                required,
              },
              applicant_is_coordinator: {
                required,
              },
              is_referral: {
                required,
              },
              referral_name: {
                required,
              },
              funeral_director_id: {},
            },
          };
        }
      }
    }
  },
  metaInfo: {
    title: "Memorial Rooms Booking Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Memorial Rooms Booking Info Description",
      },
    ],
  },
  created() {
    this.getItem();
    this.getListRoomForBooking();
    this.getListRoomType();
    this.getListRelationship();
    this.getListTypeSalutation();
    this.getListDirector();
    this.getListEvent();
    this.getListStatusRoom();
    this.getListReferral();
    // EventBus.$on('save-service-room', this.saveService);
    this.bookingParam.book_funeral_director = this.yesNoOptions[0];
    this.bookingParam.applicant_is_coordinator = this.yesNoOptions[0];
  },
  computed: mapState({
    listRoomForBooking: (state) => state.room.listRoomForBooking,
    listRoomType: (state) => state.booking.listRoomType,
    listRelationship: (state) => state.booking.listRelationship,
    listTypesSalutation: (state) => state.customer.listTypeSalutation,
    listDirector: (state) => state.booking.listDirector,
    listEvent: (state) => state.booking.listEvent,
    listStatusRoom: (state) => state.booking.listStatusRoom,
    listReferral: (state) => state.booking.listReferral,
  }),
  methods: {
    ...mapActions({
      getItemServiceBooking: "service/getItemServiceBooking",
      getListRoomForBooking: "room/getListRoomForBooking",
      getListRoomType: "booking/getListRoomType",
      getListRelationship: "booking/getListRelationship",
      getListTypeSalutation: "customer/getListTypeSalutation",
      getListDirector: "booking/getListDirector",
      getListEvent: "booking/getListEvent",
      updateRoomService: "booking/updateRoomService",
      getListStatusRoom: "booking/getListStatusRoom",
      getListReferral: "booking/getListReferral",
    }),
    getItem() {
      let prms = {
        id: this.id,
      };
      this.getItemServiceBooking(prms).then((res) => {
        this.data = res.data.data;
      });
    },
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return "";
      }
    },
    goBack() {
      // @click="goBack"
      this.$router.push("/memorial-rooms");
    },
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    customFormatForSave(date) {
      return moment(date).format("YYYY-MM-DD");
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
    onSave() {
      this.$v.bookingParam.$touch();
      if (this.$v.bookingParam.$anyError) {
        return;
      } else {
        // var now = moment(this.bookingParam.check_out_time, "HH:mm");
        // var then = moment(this.bookingParam.check_in_time, "HH:mm");
        // var s = moment
        //   .utc(moment(now, "HH:mm").diff(moment(then, "HH:mm")))
        //   .format("HH:mm");

        // var d = moment.duration(s);

        // var ms = Math.floor(d.asHours());

        // if (ms < 4) {
        //   this.$swal({
        //     title: "Warning",
        //     text: "The check-out time 4hr from the check-in time.",
        //     icon: "warning",
        //   });
        //   return;
        // }

        let prms = { ...this.bookingParam };
        prms.booking_date = this.customFormatForSave(prms.booking_date);
        prms.check_in_date = this.customFormatForSave(prms.check_in_date);
        if (prms.check_out_date !== "") {
          prms.check_out_date = this.customFormatForSave(prms.check_out_date);
        }
        if (prms.death_date) {
          prms.death_date = this.customFormatForSave(prms.death_date);
        }
        prms.book_funeral_director = prms.book_funeral_director.name;
        if (prms.applicant_is_coordinator) {
          prms.applicant_is_coordinator = prms.applicant_is_coordinator.name;
        }
        prms.room_type = prms.room_type.id;
        prms.event_id = prms.event_id.id;
        if (prms.relationship_to_applicant) {
          prms.relationship_to_applicant = prms.relationship_to_applicant.id;
        }

        prms.service_id = prms.service_id.id;
        prms.is_referral = prms.is_referral.id;
        if (prms.status) {
          prms.status = prms.status.id;
        }
        if (prms.funeral_director_id) {
          prms.funeral_director_id = prms.funeral_director_id.id;
        }
        prms.amount = this.formatMoney(prms.amount);
        var formData = new FormData();

        for (var key in prms) {
          var val = prms[key] == null ? "" : prms[key];
          formData.append(key, val);
        }
        let data = {
          id: prms.id,
          data: formData,
        };
        this.updateRoomService(data)
          .then((res) => {
            this.$swal({
              icon: "success",
              title: "Success!",
              text: res.data.status,
            });
          })
          .catch((error) => {
            this.$swal({
              icon: "error",
              title: "Oops...",
              text: error.response.data.errors,
            });
          });
        // this.$emit('serviceItem',prms,this.$v.bookingParam.$anyError)
      }
    },
    customFormatTime(date) {
      return moment(date).format("hh:mm");
    },
    getFile(url) {
      axios
        .get(url, {
          responseType: "arraybuffer",
        })
        .then((result) => {
          let parts = url.split("/");
          let name = parts[parts.length - 1];
          let blob = new Blob([result.data], {
            type: result.headers["content-type"],
          });
          let file = new File([blob], name, blob);
          this.bookingParam.tv_photo = file;
        });
    },
  },
  watch: {
    "bookingParam.book_funeral_director": function (val) {
      if (val.name == "No") {
        this.isFuneral = false;
        this.bookingParam.funeral_director_id = "";
      } else {
        this.isFuneral = true;
      }
    },
    "bookingParam.applicant_is_coordinator": function (val) {
      if (val.name == "No") {
        this.isCood = true;
      } else {
        this.isCood = false;
        this.bookingParam.coord_first_name = "";
        this.bookingParam.coord_title = "";
        this.bookingParam.coord_last_name = "";
        this.bookingParam.coord_contact_no = "";
      }
    },
    "bookingParam.service_id": function (val) {
      val != null
        ? ((this.infoRoom = val),
          this.bookingParam.room_type.reference_value_text == "Daily" ? (this.bookingParam.amount = this.infoRoom.price_daily) : "")
        : "";
    },
    "bookingParam.room_type": function (val) {
      if (val != null) {
        if (val.reference_value_text == "Hourly") {
          this.bookingParam.amount = this.infoRoom.price_hourly;
        } else {
          this.bookingParam.amount = this.infoRoom.price_daily;
        }
      } else {
        if (bookingParam.room_type.reference_value_text == "Daily") {
          this.bookingParam.amount = this.infoRoom.price_daily;
        } else {
          this.bookingParam.amount = "";
        }
      }
    },
    "bookingParam.check_out_time": function (val) {
      if (val != "") {
        var mm = val.substring(3);

        var checkin_date = new Date(this.bookingParam.check_in_date);

        if (this.bookingParam.check_in_time === "") {
          this.bookingParam.check_out_time = "HH:mm";
          this.$swal({
            title: "Warning",
            text: "Please choose the checkin time.",
            icon: "warning",
          });
          return;
        }

        var check_out_date = new Date(this.bookingParam.check_out_date);
        // console.log(check_out_date.getTime(),checkin_date.getTime(),'dsdsa')
        // return_day.getTime() < renting_day.getTime()
        if (
          check_out_date.getDate() == checkin_date.getDate() &&
          check_out_date.getMonth() == checkin_date.getMonth() &&
          check_out_date.getFullYear() == checkin_date.getFullYear()
        ) {
          if (mm != "mm") {
            var now = val;

            var then = this.bookingParam.check_in_time;

            var s = moment.utc(moment(now, "HH:mm").diff(moment(then, "HH:mm"))).format("HH:mm");

            var d = moment.duration(s);

            var ms = Math.floor(d.asHours());

            if (ms < 4 || val.substring(0, 2) == "00" || val == "00:00" || ms == 23) {
              this.bookingParam.check_out_time = "HH:mm";

              this.$swal({
                title: "Warning",
                text: "Minimum Booking - 4 Hours",
                icon: "warning",
              });
              return;
            }
          }
          // else{

          // }
        }
      }
    },
    "bookingParam.check_out_date": function (val) {
      if (val != "") {
        var checkin_date = new Date(this.bookingParam.check_in_date);

        var check_out_date = new Date(val);
        if (check_out_date.getTime() < checkin_date.getTime()) {
          this.$nextTick(() => {
            this.bookingParam.check_out_date = "";
            this.bookingParam.check_out_time = "HH:mm";
          });
          this.$swal({
            title: "Warning",
            text: "The checkout date  must be greater or equal to the checkin date",
            icon: "warning",
          });
        } else {
          if (check_out_date.getTime() == checkin_date.getTime()) {
            if (this.bookingParam.check_out_time != "" && this.bookingParam.check_in_time != "") {
              var now = this.bookingParam.check_out_time;

              var then = this.bookingParam.check_in_time;

              var s = moment.utc(moment(now, "HH:mm").diff(moment(then, "HH:mm"))).format("HH:mm");

              var d = moment.duration(s);

              var ms = Math.floor(d.asHours());

              if (ms < 4 || val == "00:mm" || ms == 23) {
                this.bookingParam.check_out_time = "HH:mm";

                this.$swal({
                  title: "Warning",
                  text: "Minimum Booking - 4 Hours",
                  icon: "warning",
                });
                return;
              }
            }
          }
        }
      }
    },
    "bookingParam.check_in_date": function (val) {
      if (val != "") {
        var checkin_date = new Date(val);

        var check_out_date = new Date(this.bookingParam.check_out_date);

        if (check_out_date.getTime() < checkin_date.getTime()) {
          this.$nextTick(() => {
            this.bookingParam.check_in_time = "HH:mm";
            this.bookingParam.check_out_date = "";
            this.bookingParam.check_out_time = "HH:mm";
          });
        }
      }
    },
    listRoomType: function (val) {
      if (this.bookingParam.room_type == "") {
        this.bookingParam.room_type = val[0];
      }
    },
    data: function (val) {
      this.bookingParam.id = val.id;
      this.bookingParam.booking_type_id = val.booking_type_id;
      this.bookingParam.user_id = val.client.id;
      this.bookingParam.booking_no = val.booking_no;
      this.bookingParam.booking_date = val.booking_date;
      this.bookingParam.event_id = val.event;
      this.bookingParam.service_id = val.room;
      this.bookingParam.amount = val.amount;
      this.bookingParam.room_type = val.room_type;
      this.bookingParam.check_in_date = val.check_in_date;
      this.bookingParam.check_in_time = val.check_in_time ? val.check_in_time : "";
      this.bookingParam.check_out_date = val.check_out_date ? val.check_out_date : "";
      this.bookingParam.check_out_time = val.check_out_time ? val.check_out_time : "";
      this.bookingParam.departed_full_name = val.departed_full_name ? val.departed_full_name : null;
      this.bookingParam.death_date = val.death_date ? val.death_date : null;
      this.bookingParam.church_attended = val.church_attended ? val.church_attended : null;
      this.bookingParam.mobile = val.mobile ? val.mobile : "";
      this.bookingParam.relationship_to_applicant = val.relationship_to_applicant ? val.relationship_to_applicant : null;
      if (val.book_funeral_director == "Yes") {
        this.bookingParam.book_funeral_director = this.yesNoOptions[0];
      } else {
        this.bookingParam.book_funeral_director = this.yesNoOptions[1];
      }
      this.bookingParam.funeral_director_id = val.funeral_director;

      if (val.applicant_is_coordinator == "Yes") {
        this.bookingParam.applicant_is_coordinator = this.yesNoOptions[0];
      } else {
        this.bookingParam.applicant_is_coordinator = this.yesNoOptions[1];
      }
      this.bookingParam.coordinator_full_name = val.coordinator_full_name ? val.coordinator_full_name : "";
      this.bookingParam.tv_display_name = val.tv_display_name ? val.tv_display_name : "";
      this.bookingParam.tv_wake_service = val.tv_wake_service ? val.tv_wake_service : "";
      this.bookingParam.tv_life_years = val.tv_life_years ? val.tv_life_years : "";
      this.bookingParam.tv_encoffin_service = val.tv_encoffin_service ? val.tv_encoffin_service : "";
      this.bookingParam.tv_cottage_leaves = val.tv_cottage_leaves ? val.tv_cottage_leaves : "";
      (this.bookingParam.remarks = val.remarks ? val.remarks : ""),
        (this.bookingParam.tv_photo = val.tv_photo_of_departed ? this.getFile(val.tv_photo_of_departed) : null);
      this.bookingParam.status = val.status;
      this.bookingParam.is_referral = val.referral;
      this.bookingParam.referral_name = val.referral_name;
    },
    "bookingParam.is_referral": function (val) {
      val != null ? (val.reference_value_text == "Yes" ? (this.isReferral = true) : (this.isReferral = false)) : (this.isReferral = false);
    },
  },
};
</script>
