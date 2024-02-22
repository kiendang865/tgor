<template>
  <b-container fluid="lg" class="booking-service-room-field">
    <b-row>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Booking Date <span class="_require">*</span></label>
          <div class="position-relative input-date">
            <Datepicker
              :class="{
                'form-group--error': $v.bookingParam.booking_date.$error,
                'bg-white': !isAdmin && !isInvoice,
                'bg-gray-disable': isAdmin && isInvoice,
              }"
              v-model.trim="$v.bookingParam.booking_date.$model"
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :format="customFormatter"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></Datepicker>
            <div class="error-date" v-if="!$v.bookingParam.booking_date.required && $v.bookingParam.booking_date.$error">Field is required</div>
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
              'bg-white': !isAdmin && !isInvoice,
              'bg-gray': isAdmin && isInvoice,
            }"
            v-model.trim="$v.bookingParam.event_id.$model"
            :options="listEvent"
            placeholder="Select one"
            label="reference_value_text"
            track-by="id"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
          ></multiselect>
          <div class="error-date" v-if="!$v.bookingParam.event_id.required && $v.bookingParam.event_id.$error">Field is required</div>
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
              'bg-white': !isAdmin && !isInvoice,
              'bg-gray': isAdmin && isInvoice,
            }"
            v-model.trim="$v.bookingParam.service_id.$model"
            :options="listRoomForBooking"
            placeholder="Select one"
            label="room_no"
            track-by="id"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
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
              'bg-white': !isAdmin && !isInvoice,
              'bg-gray': isAdmin && isInvoice,
            }"
            v-model.trim="$v.bookingParam.room_type.$model"
            :options="listRoomType"
            placeholder="Select one"
            label="reference_value_text"
            track-by="id"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
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
            v-model="bookingParam.amount"
            placeholder="$0.00"
            :disabled="true"
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
                'bg-white': !isAdmin && !isInvoice,
                'bg-gray-disable': isAdmin && isInvoice,
              }"
              v-model.trim="$v.bookingParam.check_in_date.$model"
              :format="customFormatter"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></Datepicker>
            <div class="error-date" v-if="!$v.bookingParam.check_in_date.required && $v.bookingParam.check_in_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
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
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></vue-timepicker>
            <div class="error-date" v-if="!$v.bookingParam.check_in_time.required && $v.bookingParam.check_in_time.$error">Field is required</div>
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
              :class="{
                'form-group--error': $v.bookingParam.check_out_date.$error,
                'bg-white': !isAdmin && !isInvoice,
                'bg-gray-disable': isAdmin && isInvoice,
              }"
              placeholder="dd/mm/yyyy"
              v-model.trim="$v.bookingParam.check_out_date.$model"
              :format="customFormatter"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></Datepicker>
            <div class="error-date" v-if="!$v.bookingParam.check_out_date.required && $v.bookingParam.check_out_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Check-out Time <span class="_require">*</span></label>
          <div class="position-relative input-time">
            <!-- <Datepicker class="choose-date" placeholder="dd/mm/yyyy" v-model="state.date3"></Datepicker> -->
            <vue-timepicker
              class="choose-date"
              :class="{
                'form-group--error': $v.bookingParam.check_out_time.$error,
              }"
              v-model.trim="$v.bookingParam.check_out_time.$model"
              format="HH:mm"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></vue-timepicker>
            <div class="error-date" v-if="!$v.bookingParam.check_out_time.required && $v.bookingParam.check_out_time.$error">Field is required</div>
            <Clock />
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="6">
        <b-form-group label="Departed Full Name">
          <b-form-input class="input-form" v-model="bookingParam.departed_full_name" :disabled="isDisibleSA || (isAdmin && isInvoice)"></b-form-input>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Date of Death">
          <div class="position-relative input-date">
            <Datepicker
              :class="{ 'bg-white': !isAdmin && !isInvoice, 'bg-gray-disable': isAdmin && isInvoice }"
              class="choose-date"
              placeholder="dd/mm/yyyy"
              v-model="bookingParam.date_of_death"
              :format="customFormatter"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></Datepicker>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="6">
        <b-form-group label="Church Attended">
          <b-form-input
            class="input-form"
            v-model="bookingParam.church_attended"
            placeholder="Name"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
          ></b-form-input>
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
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
            :class="{ 'bg-white': !isAdmin && !isInvoice, 'bg-gray': isAdmin && isInvoice }"
          ></multiselect>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="6">
        <b-form-group label="Remarks">
          <b-form-input class="input-form" v-model="bookingParam.remarks" :disabled="isDisibleSA || (isAdmin && isInvoice)"></b-form-input>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">F.D. Referral? <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{
              'form-group--error': $v.bookingParam.book_funeral_director.$error,
              'bg-white': !isAdmin && !isInvoice,
              'bg-gray': isAdmin && isInvoice,
            }"
            v-model.trim="$v.bookingParam.book_funeral_director.$model"
            :options="yesNoOptions"
            placeholder="Select one"
            label="name"
            track-by="name"
            @select="changeIsReferral"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
          >
          </multiselect>
          <div class="error" v-if="!$v.bookingParam.book_funeral_director.required && $v.bookingParam.book_funeral_director.$error">
            Field is required
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="6" v-if="isFuneral">
        <b-form-group>
          <label class="_label_input">Funeral Director <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{
              'form-group--error': $v.bookingParam.funeral_director_id.$error,
              'bg-white': !isAdmin && !isInvoice,
              'bg-gray': isAdmin && isInvoice,
            }"
            v-model.trim="$v.bookingParam.funeral_director_id.$model"
            :options="listDirector"
            placeholder="Select one"
            label="company_name"
            track-by="name"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
          >
          </multiselect>
          <div class="error" v-if="!$v.bookingParam.funeral_director_id.required && $v.bookingParam.funeral_director_id.$error">
            Field is required
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Is Applicant the Co-ordinator? <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{
              'form-group--error': $v.bookingParam.applicant_is_coordinator.$error,
              'bg-white': !isAdmin && !isInvoice,
              'bg-gray': isAdmin && isInvoice,
            }"
            v-model.trim="$v.bookingParam.applicant_is_coordinator.$model"
            :options="yesNoOptions"
            placeholder="Select one"
            label="name"
            track-by="name"
            @select="changeIsApplicant"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
          >
          </multiselect>
          <div class="error" v-if="!$v.bookingParam.applicant_is_coordinator.required && $v.bookingParam.applicant_is_coordinator.$error">
            Field is required
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <template v-if="isCood">
      <b-row class="mt">
        <b-col cols="6">
          <b-form-group label="Co-ordinator Full Name">
            <b-form-input
              class="input-form"
              v-model="bookingParam.coordinator_full_name"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Mobile">
            <b-form-input class="input-form" v-model="bookingParam.mobile" :disabled="isDisibleSA || (isAdmin && isInvoice)"></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
    </template>
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
          <label for="upload-photo" class="btn-file-report">Select File</label>
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
    <b-row> </b-row>
  </b-container>
</template>

<script>
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
import { isNil } from "lodash";

export default {
  name: "ServiceNichesBookingFields",
  components: {
    Datepicker,
    Calendar,
    Multiselect,
    MaskedInput,
    Clock,
    VueTimepicker,
  },
  props: {
    item: {
      type: Object,
      default: () => {},
    },
    hasSaleAgreement: {
      type: Boolean,
      default: false,
    },
    isAdmin: {
      type: Boolean,
      default: false,
    },
    isInvoice: {
      type: Boolean,
      default: false,
    },
    isDisibleSA: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      state: {
        date1: null,
        date2: null,
        date4: null,
        date5: null,
      },
      valueNiche: null,
      yesNoOptions: [{ name: "Yes" }, { name: "No" }],
      bookingParam: {
        id: "",
        event_id: "",
        service_id: "",
        amount: "",
        booking_date: "",
        room_type: "",
        check_in_date: "",
        check_in_time: "",
        check_out_date: "",
        check_out_time: "",
        departed_title: "",
        departed_full_name: "",
        departed_notes: "",
        tv_display_name: "",
        tv_photo: null,
        tv_life_years: null,
        tv_wake_service: null,
        tv_encoffin_service: null,
        tv_cottage_leaves: null,
        date_of_death: "",
        church_attended: "",
        relationship_to_applicant: "",
        book_funeral_director: "",
        funeral_director_id: "",
        applicant_is_coordinator: "",
        coordinator_full_name: "",
        mobile: "",
        is_referral: null,
        referral_name: "",
      },
      isFuneral: false,
      isCood: true,
      infoRoom: {},
      isReferral: false,
      isCheckTime: false,
      isEdit: false,
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
          funeral_director_id: {},
          applicant_is_coordinator: {
            required,
          },
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
            applicant_is_coordinator: {
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
              applicant_is_coordinator: {
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
  created() {
    this.getItem(this.item);
    this.getListRoomForBooking();
    this.getListRoomType();
    this.getListRelationship();
    this.getListTypeSalutation();
    this.getListDirector();
    this.getListEvent();
    this.getListReferral();
    // this.getListStatusRoom();
    EventBus.$on("save-service-booking", this.saveService);
    if (this.bookingParam.book_funeral_director == "" || this.bookingParam.book_funeral_director == null) {
      this.bookingParam.book_funeral_director = this.yesNoOptions[1];
    }
    this.bookingParam.applicant_is_coordinator = this.yesNoOptions[1];
    if (!this.bookingParam.booking_date) {
      this.bookingParam.booking_date = moment().format("MM/DD/YYYY");
    }
  },
  computed: mapState({
    listRoomForBooking: (state) => state.room.listRoomForBooking,
    listRoomType: (state) => state.booking.listRoomType,
    listRelationship: (state) => state.booking.listRelationship,
    listTypesSalutation: (state) => state.customer.listTypeSalutation,
    listDirector: (state) => state.booking.listDirector,
    arr_rooms: (state) => state.booking.arr_rooms,
    listEvent: (state) => state.booking.listEvent,
    listStatusRoom: (state) => state.booking.listStatusRoom,
    listReferral: (state) => state.booking.listReferral,
  }),
  methods: {
    ...mapActions({
      getListRoomForBooking: "room/getListRoomForBooking",
      getListRoomType: "booking/getListRoomType",
      getListRelationship: "booking/getListRelationship",
      getListTypeSalutation: "customer/getListTypeSalutation",
      getListDirector: "booking/getListDirector",
      getListEvent: "booking/getListEvent",
      getListStatusRoom: "booking/getListStatusRoom",
      getListReferral: "booking/getListReferral",
    }),
    getItem(val) {
      if (Object.keys(val).length > 3) {
        this.isEdit = true;
        this.bookingParam.id = val.id;
        this.bookingParam.booking_type_id = val.booking_type_id;
        this.bookingParam.user_id = val.client.id;
        this.bookingParam.booking_no = val.booking.booking_no;
        this.bookingParam.booking_date = val.booking_date;
        this.bookingParam.event_id = val.event;
        this.bookingParam.service_id = val.room;
        this.bookingParam.amount = val.amount;
        this.bookingParam.room_type = val.room_type;
        this.bookingParam.check_in_date = val.check_in_date;
        this.bookingParam.check_in_time = val?.check_in_time;
        this.bookingParam.check_out_date = val?.check_out_date;
        this.bookingParam.check_out_time = val?.check_out_time;
        this.bookingParam.departed_full_name = val?.departed_full_name;
        this.bookingParam.date_of_death = val.date_of_death ? val.date_of_death : null;
        this.bookingParam.church_attended = val.church_attended ? val.church_attended : null;
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
        this.bookingParam.coordinator_full_name = val.coordinator_full_name ? val.coordinator_full_name : null;
        this.bookingParam.tv_display_name = val.tv_display_name ? val.tv_display_name : null;
        this.bookingParam.tv_photo = val.tv_photo ? val.tv_photo : null;
        this.bookingParam.tv_life_years = val.tv_life_years ? val.tv_life_years : null;
        this.bookingParam.tv_wake_service = val.tv_wake_service ? val.tv_wake_service : null;
        this.bookingParam.tv_encoffin_service = val.tv_encoffin_service ? val.tv_encoffin_service : null;
        this.bookingParam.tv_cottage_leaves = val.tv_cottage_leaves ? val.tv_cottage_leaves : null;
        this.bookingParam.remarks = val.remarks ? val.remarks : "";
        this.bookingParam.mobile = val.mobile ? val.mobile : "";
        this.bookingParam.is_referral = val.referral;
        this.bookingParam.referral_name = val.referral_name;
        this.bookingParam.tv_photo = val.tv_photo_of_departed ? this.getFile(val.tv_photo_of_departed) : null;
      }
    },
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    customFormatForSave(date) {
      return moment(date).format("YYYY-MM-DD");
    },
    customFormatTime(date) {
      return moment(date).format("hh:mm");
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
    saveService() {
      this.$v.bookingParam.$touch();
      if (this.$v.bookingParam.$anyError) {
        this.$swal({
          title: "Warning!",
          text: "Some field are blank. Please fill them up",
          icon: "warning",
        });
        return;
      } else if (this.bookingParam.check_out_time.substring(3).trim() == "mm") {
        this.$swal({
          title: "Warning!",
          text: "Please check the start time and end time fields again.",
          icon: "warning",
        });
        return;
      } else {
        if (!this.isEdit) {
          let prms = { ...this.bookingParam };
          prms.booking_date = this.customFormatForSave(prms.booking_date);
          prms.check_in_date = this.customFormatForSave(prms.check_in_date);
          if (prms.check_out_date !== "") {
            prms.check_out_date = this.customFormatForSave(prms.check_out_date);
          }
          if (prms.date_of_death) {
            prms.date_of_death = this.customFormatForSave(prms.date_of_death);
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
          if (prms.funeral_director_id) {
            prms.funeral_director_id = prms.funeral_director_id.id;
          }
          if (prms.check_out_time == "HH:mm") {
            prms.check_out_time = "";
          }
          if (prms.check_in_time == "HH:mm") {
            prms.check_in_time = "";
          }
          prms.is_referral = prms.is_referral.id;
          prms.amount = this.formatMoney(prms.amount);
          this.$emit("serviceItem", prms, this.$v.bookingParam.$anyError);
        } else {
          let prms = {};
          prms.booking_date = this.customFormatForSave(this.bookingParam.booking_date);
          prms.check_in_date = this.customFormatForSave(this.bookingParam.check_in_date);
          if (prms.check_out_date !== "") {
            prms.check_out_date = this.customFormatForSave(this.bookingParam.check_out_date);
          }
          if (prms.date_of_death) {
            prms.date_of_death = this.customFormatForSave(this.bookingParam.date_of_death);
          }
          prms.book_funeral_director = this.bookingParam.book_funeral_director.name;
          if (prms.applicant_is_coordinator) {
            prms.applicant_is_coordinator = this.bookingParam.applicant_is_coordinator.name;
          }
          prms.room_type = this.bookingParam.room_type.id;
          prms.event_id = this.bookingParam.event_id.id;
          if (prms.relationship_to_applicant) {
            prms.relationship_to_applicant = this.bookingParam.relationship_to_applicant.id;
          }
          prms.service_id = this.bookingParam.service_id.id;
          if (this.bookingParam.funeral_director_id) {
            prms.funeral_director_id = this.bookingParam.funeral_director_id.id;
          }
          if (this.bookingParam.check_out_time == "HH:mm") {
            prms.check_out_time = "";
          } else {
            prms.check_out_time = this.bookingParam.check_out_time;
          }
          if (this.bookingParam.check_in_time == "HH:mm") {
            prms.check_in_time = "";
          } else {
            prms.check_in_time = this.bookingParam.check_in_time;
          }
          prms.is_referral = this.bookingParam.is_referral.id;
          prms.amount = this.formatMoney(this.bookingParam.amount);
          prms.id = this.bookingParam.id;
          prms.departed_title = this.bookingParam.departed_title;
          prms.departed_full_name = this.bookingParam.departed_full_name;
          prms.departed_notes = this.bookingParam.departed_notes;
          prms.tv_display_name = this.bookingParam.tv_display_name;
          prms.tv_photo = this.bookingParam.tv_photo;
          prms.tv_life_years = this.bookingParam.tv_life_years;
          prms.tv_wake_service = this.bookingParam.tv_wake_service;
          prms.tv_encoffin_service = this.bookingParam.tv_encoffin_service;
          prms.tv_cottage_leaves = this.bookingParam.tv_cottage_leaves;
          prms.date_of_death = this.bookingParam.date_of_death;
          prms.church_attended = this.bookingParam.church_attended;
          prms.coordinator_full_name = this.bookingParam.coordinator_full_name;
          prms.mobile = this.bookingParam.mobile;
          prms.referral_name = this.bookingParam.referral_name;
          this.$emit("serviceItem", prms, this.$v.bookingParam.$anyError);
        }
      }
    },
    changeIsApplicant(data) {
      this.bookingParam.mobile = "";
      this.bookingParam.coordinator_full_name = "";
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
    changeIsReferral(data) {
      this.bookingParam.funeral_director_id = "";
      this.bookingParam.mobile = "";
      this.bookingParam.coordinator_full_name = "";
    },
    checkTimeRoom(val) {
      if (val != "" && val !== null) {
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
            } else if (this.bookingParam.room_type?.reference_value_text == "Hourly (Min 4 Hours)") {
              this.bookingParam.amount = Math.round(this.infoRoom.price_hourly * ms).toFixed(2);
            }
          }
        }
      }
    },
    checkDateRoom(check_out_date, checkin_date) {
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
        if (check_out_date.getTime() >= checkin_date.getTime()) {
          if (this.bookingParam.check_out_time != "" && this.bookingParam.check_in_time != "") {
            var then = moment(checkin_date).format("DD/MM/YYYY") + " " + this.bookingParam.check_in_time;
            var now = moment(check_out_date).format("DD/MM/YYYY") + " " + this.bookingParam.check_out_time;

            var s = moment(now, "DD/MM/YYYY HH:mm").diff(moment(then, "DD/MM/YYYY HH:mm"));
            var d = moment.duration(s);
            var ms = Math.floor(d.asHours()) + moment.utc(s).format(".mm");
            if (ms < 4 || ms == 23) {
              this.bookingParam.check_out_time = "HH:mm";

              this.$swal({
                title: "Warning",
                text: "Minimum Booking - 4 Hours",
                icon: "warning",
              });
              return;
            } else if (ms > 4 && this.bookingParam.room_type?.reference_value_text == "Hourly (Min 4 Hours)") {
              this.bookingParam.amount = Math.round(this.infoRoom.price_hourly * ms).toFixed(2);
              return;
            }
          }
        }
        if (check_out_date && checkin_date) {
          let checkOutDay = moment(check_out_date).format("yyyy/MM/DD");
          let checkInDay = moment(checkin_date).format("yyyy/MM/DD");
          let checkDay = moment(checkInDay).isBefore(checkOutDay);
          if (checkDay) {
            if (this.bookingParam.room_type?.reference_value_text == "Daily") {
              let calculateDay = moment(checkOutDay).diff(checkInDay, "days");
              this.bookingParam.amount = Math.round(this.infoRoom.price_daily * (calculateDay + 1)).toFixed(2);
            }
          }
        }
      }
    },
  },
  watch: {
    listRoomForBooking: function (val) {
      if (!isNil(val)) {
        const findData = val.find((item) => item.room_no === "Parousia");
        if (!isNil(findData)) {
          this.bookingParam.service_id = findData;
        } else {
          this.bookingParam.service_id = val[1];
        }
      }
    },
    listRoomType: function (val) {
      if (val) this.bookingParam.room_type = this.listRoomType[0];
    },

    "bookingParam.book_funeral_director": function (val) {
      if (val != "" || val != null) {
        if (val.name == "No") {
          this.isFuneral = false;
        } else {
          this.isFuneral = true;
        }
      } else {
        this.bookingParam.book_funeral_director = this.yesNoOptions[0];
      }
    },
    "bookingParam.applicant_is_coordinator": function (val) {
      if (val.name == "No") {
        this.isCood = true;
      } else {
        this.isCood = false;
      }
    },
    "bookingParam.service_id": function (val) {
      if (val != null) {
        if (this.arr_rooms) {
          if (this.arr_rooms.length == 0) {
            this.$store.commit("booking/updateArrRoom", val.id);
          }
        }
        this.infoRoom = val;
        this.bookingParam.room_type.reference_value_text == "Daily" ? (this.bookingParam.amount = this.infoRoom.price_daily) : "";

        let checkin_date = new Date(this.bookingParam.check_in_date);
        var check_out_date = new Date(this.bookingParam.check_out_date);
        this.checkDateRoom(check_out_date, checkin_date);
      }
    },
    "bookingParam.room_type": function (val) {
      if (val != null) {
        if (val.reference_value_text == "Hourly (Min 4 Hours)") {
          this.bookingParam.amount = this.infoRoom.price_hourly;
          this.checkTimeRoom(this.bookingParam.check_out_time);
        } else if (val.reference_value_text == "Complimentary") {
          this.bookingParam.amount = "0.00";
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
    "bookingParam.check_in_time": function (val) {
      if (val && val != "") {
        this.checkTimeRoom(this.bookingParam.check_out_time);
      }
    },
    "bookingParam.check_out_time": function (val) {
      this.checkTimeRoom(val);
    },
    "bookingParam.check_out_date": function (val) {
      if (val !== "" && val !== null) {
        var checkin_date = new Date(this.bookingParam.check_in_date);

        var check_out_date = new Date(val);
        this.checkDateRoom(check_out_date, checkin_date);
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
        this.checkDateRoom(check_out_date, checkin_date);
      }
    },
    listEvent: function (val) {
      if (!this.bookingParam.event_id) this.bookingParam.event_id = val[0];
    },
    "bookingParam.event_id": function (val) {
      if (!this.bookingParam.check_in_date) {
        if (val.reference_value_text == "Funeral Wake") {
          this.bookingParam.check_in_date = moment().format("MM/DD/YYYY");
        }
      }
    },
    "bookingParam.is_referral": function (val) {
      val != null ? (val.reference_value_text == "Yes" ? (this.isReferral = true) : (this.isReferral = false)) : (this.isReferral = false);
    },
    listReferral: function (val) {
      if (!this.bookingParam.is_referral) this.bookingParam.is_referral = val[1];
    },
  },
};
</script>
<style lang="scss">
#upload-photo {
  opacity: 0;
  position: absolute;
  z-index: -1;
}
.btn-file-report {
  padding: 0 17px;
  border: 1px solid #000000;
  box-sizing: border-box;
  border-radius: 5px;
  line-height: 2.5 !important;
}
</style>
