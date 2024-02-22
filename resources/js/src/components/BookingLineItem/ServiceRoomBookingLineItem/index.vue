<template>
  <b-container fluid="lg" class="booking-service-room-field">
    <b-row>
      <b-col cols="3">
        <b-form-group label="Booking #">
          <div class="position-relative input-date">
            <b-form-input v-model.trim="bookingParam.booking_no" class="input-form" :disabled="true"></b-form-input>
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Booking Date">
          <div class="position-relative input-date">
            <Datepicker
              :class="{ 'form-group--error': $v.bookingParam.booking_date.$error }"
              v-model.trim="$v.bookingParam.booking_date.$model"
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :format="customFormatter"
            ></Datepicker>
            <div class="error-date" v-if="!$v.bookingParam.booking_date.required && $v.bookingParam.booking_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Event">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.bookingParam.event_id.$error }"
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
            :options="optionsNiche"
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
        <b-form-group label="Room No.">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.bookingParam.service_id.$error }"
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
        <b-form-group label="Booking Type">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.bookingParam.room_type.$error }"
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
        <b-form-group label="Price">
          <masked-input type="text" class="form-control" :mask="numberAmountMask()" :guide="true" v-model="bookingParam.amount" placeholder="$0.00">
          </masked-input>
          <!-- :class="{'form-group--error': checkPrice }"
            v-model.trim="$v.otherParams.price.$model" -->
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Check-in Date">
          <div class="position-relative input-date">
            <Datepicker
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :class="{ 'form-group--error': $v.bookingParam.check_in_date.$error }"
              v-model.trim="$v.bookingParam.check_in_date.$model"
              :format="customFormatter"
            ></Datepicker>
            <div class="error-date" v-if="!$v.bookingParam.check_in_date.required && $v.bookingParam.check_in_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Check-in Time">
          <div class="position-relative input-time">
            <!-- <Datepicker class="choose-date" placeholder="dd/mm/yyyy" v-model="state.date2"></Datepicker> -->
            <vue-timepicker
              class="choose-date"
              :class="{ 'form-group--error': $v.bookingParam.check_in_time.$error }"
              v-model.trim="$v.bookingParam.check_in_time.$model"
              format="HH:mm"
            ></vue-timepicker>
            <div class="error-date" v-if="!$v.bookingParam.check_in_time.required && $v.bookingParam.check_in_time.$error">Field is required</div>
            <Clock />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Check-out Date">
          <div class="position-relative input-date">
            <Datepicker
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :class="{ 'form-group--error': $v.bookingParam.check_out_date.$error }"
              v-model.trim="$v.bookingParam.check_out_date.$model"
              :format="customFormatter"
            ></Datepicker>
            <div class="error-date" v-if="!$v.bookingParam.check_out_date.required && $v.bookingParam.check_out_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Check-out Time">
          <div class="position-relative input-time">
            <!-- <Datepicker class="choose-date" placeholder="dd/mm/yyyy" v-model="state.date3"></Datepicker> -->
            <vue-timepicker
              class="choose-date"
              :class="{ 'form-group--error': $v.bookingParam.check_out_time.$error }"
              v-model.trim="$v.bookingParam.check_out_time.$model"
              format="HH:mm"
            ></vue-timepicker>
            <div class="error-date" v-if="!$v.bookingParam.check_out_time.required && $v.bookingParam.check_out_time.$error">Field is required</div>
            <Clock />
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Departed Title">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.bookingParam.departed_title.$error }"
            v-model.trim="$v.bookingParam.departed_title.$model"
            :options="listTypesSalutation"
            placeholder="Select one"
            label="reference_value_text"
            track-by="id"
          ></multiselect>
          <div class="error" v-if="!$v.bookingParam.departed_title.required && $v.bookingParam.departed_title.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Departed First Name">
          <b-form-input
            class="input-form"
            :class="{ 'form-group--error': $v.bookingParam.departed_first_name.$error }"
            v-model.trim="$v.bookingParam.departed_first_name.$model"
          ></b-form-input>
          <div class="error" v-if="!$v.bookingParam.departed_first_name.required && $v.bookingParam.departed_first_name.$error">
            Field is required
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Departed Last Name">
          <b-form-input
            class="input-form"
            :class="{ 'form-group--error': $v.bookingParam.departed_last_name.$error }"
            v-model.trim="$v.bookingParam.departed_last_name.$model"
          ></b-form-input>
          <div class="error" v-if="!$v.bookingParam.departed_last_name.required && $v.bookingParam.departed_last_name.$error">Field is required</div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Departed Display Name - TV">
          <b-form-input
            class="input-form"
            :class="{ 'form-group--error': $v.bookingParam.tv_departed_display_name.$error }"
            v-model.trim="$v.bookingParam.tv_departed_display_name.$model"
          ></b-form-input>
          <div class="error" v-if="!$v.bookingParam.tv_departed_display_name.required && $v.bookingParam.tv_departed_display_name.$error">
            Field is required
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Departed Notes - TV">
          <b-form-input
            class="input-form"
            :class="{ 'form-group--error': $v.bookingParam.tv_departed_notes.$error }"
            v-model.trim="$v.bookingParam.tv_departed_notes.$model"
            placeholder="Name"
          ></b-form-input>
          <div class="error" v-if="!$v.bookingParam.tv_departed_notes.required && $v.bookingParam.tv_departed_notes.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Date of Death">
          <div class="position-relative input-date">
            <Datepicker
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :class="{ 'form-group--error': $v.bookingParam.death_date.$error }"
              v-model.trim="$v.bookingParam.death_date.$model"
              :format="customFormatter"
            ></Datepicker>
            <div class="error-date" v-if="!$v.bookingParam.death_date.required && $v.bookingParam.death_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Church Attended">
          <b-form-input
            class="input-form"
            :class="{ 'form-group--error': $v.bookingParam.church_attended.$error }"
            v-model.trim="$v.bookingParam.church_attended.$model"
            placeholder="Name"
          ></b-form-input>
          <div class="error" v-if="!$v.bookingParam.church_attended.required && $v.bookingParam.church_attended.$error">Field is required</div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Departed Notes">
          <b-form-input
            class="input-form"
            :class="{ 'form-group--error': $v.bookingParam.departed_notes.$error }"
            v-model.trim="$v.bookingParam.departed_notes.$model"
          ></b-form-input>
          <div class="error" v-if="!$v.bookingParam.departed_notes.required && $v.bookingParam.departed_notes.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Relationship to Applicant">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.bookingParam.relationship_to_applicant.$error }"
            v-model.trim="$v.bookingParam.relationship_to_applicant.$model"
            :options="listRelationship"
            placeholder="Select one"
            label="reference_value_text"
            track-by="id"
          ></multiselect>
          <div class="error" v-if="!$v.bookingParam.relationship_to_applicant.required && $v.bookingParam.relationship_to_applicant.$error">
            Field is required
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Book Funerial Director">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.bookingParam.book_funeral_director.$error }"
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
      <b-col cols="3">
        <b-form-group label="Funeral Director">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.bookingParam.funeral_director_id.$error, 'bg-white': !isFuneral, 'bg-gray': isFuneral }"
            v-model.trim="$v.bookingParam.funeral_director_id.$model"
            :options="listDirector"
            :disabled="isFuneral"
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
      <b-col cols="3">
        <b-form-group label="Application is Coordinator?">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.bookingParam.applicant_is_coordinator.$error }"
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
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Coord Title">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.bookingParam.coord_title.$error, 'bg-white': !isCood, 'bg-gray': isCood }"
            v-model.trim="$v.bookingParam.coord_title.$model"
            :options="listTypesSalutation"
            placeholder="Select one"
            :disabled="isCood"
            label="reference_value_text"
            track-by="id"
          >
          </multiselect>
          <div class="error" v-if="!$v.bookingParam.coord_title.required && $v.bookingParam.coord_title.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Coord First Name">
          <b-form-input
            class="input-form"
            :class="{ 'form-group--error': $v.bookingParam.coord_first_name.$error }"
            v-model.trim="$v.bookingParam.coord_first_name.$model"
            :disabled="isCood"
          ></b-form-input>
          <div class="error" v-if="!$v.bookingParam.coord_first_name.required && $v.bookingParam.coord_first_name.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Coord Last Name">
          <b-form-input
            class="input-form"
            :class="{ 'form-group--error': $v.bookingParam.coord_last_name.$error }"
            v-model.trim="$v.bookingParam.coord_last_name.$model"
            :disabled="isCood"
          ></b-form-input>
          <div class="error" v-if="!$v.bookingParam.coord_last_name.required && $v.bookingParam.coord_last_name.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Coord Contact No.">
          <b-form-input
            class="input-form"
            :class="{ 'form-group--error': $v.bookingParam.coord_contact_no.$error }"
            v-model.trim="$v.bookingParam.coord_contact_no.$model"
            :disabled="isCood"
          ></b-form-input>
          <div class="error" v-if="!$v.bookingParam.coord_contact_no.required && $v.bookingParam.coord_contact_no.$error">Field is required</div>
        </b-form-group>
      </b-col>
    </b-row>
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
    roomItem: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
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
        departed_title: "",
        departed_first_name: "",
        departed_last_name: "",
        departed_notes: "",
        tv_departed_display_name: "",
        tv_departed_notes: "",
        death_date: "",
        church_attended: "",
        relationship_to_applicant: "",
        book_funeral_director: "",
        funeral_director_id: "",
        applicant_is_coordinator: "",
        coord_title: "",
        coord_first_name: "",
        coord_last_name: "",
        coord_contact_no: "",
      },
      isFuneral: false,
      isCood: true,
      infoRoom: {},
    };
  },
  validations() {
    if (this.isFuneral == false && this.isCood == false) {
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
          check_out_time: {
            required,
          },
          check_out_date: {
            required,
          },
          departed_title: {
            required,
          },
          departed_first_name: {
            required,
          },
          departed_last_name: {
            required,
          },
          departed_notes: {
            required,
          },
          tv_departed_display_name: {
            required,
          },
          tv_departed_notes: {
            required,
          },
          death_date: {
            required,
          },
          church_attended: {
            required,
          },
          relationship_to_applicant: {
            required,
          },
          book_funeral_director: {
            required,
          },
          funeral_director_id: {
            required,
          },
          applicant_is_coordinator: {
            required,
          },
          coord_title: {
            required,
          },
          coord_first_name: {
            required,
          },
          coord_last_name: {
            required,
          },
          coord_contact_no: {
            required,
          },
        },
      };
    } else {
      if (this.isFuneral == true && this.isCood == false) {
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
            check_out_time: {
              required,
            },
            check_out_date: {
              required,
            },
            departed_title: {
              required,
            },
            departed_first_name: {
              required,
            },
            departed_last_name: {
              required,
            },
            departed_notes: {
              required,
            },
            tv_departed_display_name: {
              required,
            },
            tv_departed_notes: {
              required,
            },
            death_date: {
              required,
            },
            church_attended: {
              required,
            },
            relationship_to_applicant: {
              required,
            },
            book_funeral_director: {
              required,
            },
            funeral_director_id: {},
            applicant_is_coordinator: {
              required,
            },
            coord_title: {
              required,
            },
            coord_first_name: {
              required,
            },
            coord_last_name: {
              required,
            },
            coord_contact_no: {
              required,
            },
          },
        };
      } else {
        if (this.isFuneral == false && this.isCood == true) {
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
              check_out_time: {
                required,
              },
              check_out_date: {
                required,
              },
              departed_title: {
                required,
              },
              departed_first_name: {
                required,
              },
              departed_last_name: {
                required,
              },
              departed_notes: {
                required,
              },
              tv_departed_display_name: {
                required,
              },
              tv_departed_notes: {
                required,
              },
              death_date: {
                required,
              },
              church_attended: {
                required,
              },
              relationship_to_applicant: {
                required,
              },
              book_funeral_director: {
                required,
              },
              funeral_director_id: {
                required,
              },
              applicant_is_coordinator: {
                required,
              },
              coord_title: {
                // required,
              },
              coord_first_name: {
                // required,
              },
              coord_last_name: {
                // required,
              },
              coord_contact_no: {
                // required,
              },
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
              check_out_time: {
                required,
              },
              check_out_date: {
                required,
              },
              departed_title: {
                required,
              },
              departed_first_name: {
                required,
              },
              departed_last_name: {
                required,
              },
              departed_notes: {
                required,
              },
              tv_departed_display_name: {
                required,
              },
              tv_departed_notes: {
                required,
              },
              death_date: {
                required,
              },
              church_attended: {
                required,
              },
              relationship_to_applicant: {
                required,
              },
              book_funeral_director: {
                required,
              },
              funeral_director_id: {
                // required,
              },
              applicant_is_coordinator: {
                required,
              },
              coord_title: {
                // required,
              },
              coord_first_name: {
                // required,
              },
              coord_last_name: {
                // required,
              },
              coord_contact_no: {
                // required,
              },
            },
          };
        }
      }
    }
  },
  created() {
    this.getListRoomForBooking();
    this.getListRoomType();
    this.getListRelationship();
    this.getListTypeSalutation();
    this.getListDirector();
    this.getListEvent();
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
  }),
  methods: {
    ...mapActions({
      getListRoomForBooking: "room/getListRoomForBooking",
      getListRoomType: "booking/getListRoomType",
      getListRelationship: "booking/getListRelationship",
      getListTypeSalutation: "customer/getListTypeSalutation",
      getListDirector: "booking/getListDirector",
      getListEvent: "booking/getListEvent",
      updateService: "booking/updateService",
    }),
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
    saveService() {
      this.$v.bookingParam.$touch();
      if (this.$v.bookingParam.$anyError) {
        return;
      } else {
        var now = this.bookingParam.check_out_time;
        var then = this.bookingParam.check_in_time;

        var s = moment.utc(moment(now, "HH:mm").diff(moment(then, "HH:mm"))).format("HH:mm");

        var d = moment.duration(s);

        var ms = Math.floor(d.asHours());

        if (ms < 4) {
          this.$swal({
            title: "Warning",
            text: "Minimum Booking - 4 Hours",
            icon: "warning",
          });
          return;
        }

        let prms = { ...this.bookingParam };
        prms.booking_date = this.customFormatForSave(prms.booking_date);
        prms.check_in_date = this.customFormatForSave(prms.check_in_date) + " " + prms.check_in_time;
        prms.check_out_date = this.customFormatForSave(prms.check_out_date) + " " + prms.check_out_time;
        prms.death_date = this.customFormatForSave(prms.death_date);
        prms.book_funeral_director = prms.book_funeral_director.name;
        prms.applicant_is_coordinator = prms.applicant_is_coordinator.name;
        prms.room_type = prms.room_type.id;
        prms.departed_title = prms.departed_title.id;
        prms.event_id = prms.event_id.id;
        prms.relationship_to_applicant = prms.relationship_to_applicant.id;
        prms.service_id = prms.service_id.id;
        prms.coord_title = prms.coord_title.id;
        if (prms.funeral_director_id) {
          prms.funeral_director_id = prms.funeral_director_id.id;
        }
        prms.amount = this.formatMoney(prms.amount);

        this.updateService(prms)
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
  },
  watch: {
    "bookingParam.book_funeral_director": function (val) {
      if (val.name == "No") {
        this.isFuneral = true;
      } else {
        this.isFuneral = false;
      }
    },
    "bookingParam.applicant_is_coordinator": function (val) {
      if (val.name == "No") {
        this.isCood = false;
      } else {
        this.isCood = true;
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
    listRoomType: function (val) {
      this.bookingParam.room_type = val[0];
    },
    roomItem: function (val) {
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
      this.bookingParam.check_out_date = val.check_out_date;
      this.bookingParam.check_in_time = this.customFormatTime(val.check_in_date);
      this.bookingParam.check_out_time = this.customFormatTime(val.check_out_date);
      this.bookingParam.departed_title = val.departed_title;
      this.bookingParam.departed_first_name = val.departed_first_name;
      this.bookingParam.departed_last_name = val.departed_last_name;
      this.bookingParam.departed_notes = val.departed_notes;
      this.bookingParam.tv_departed_display_name = val.tv_departed_display_name;
      this.bookingParam.tv_departed_notes = val.tv_departed_notes;
      this.bookingParam.death_date = val.death_date;
      this.bookingParam.church_attended = val.church_attended;
      this.bookingParam.relationship_to_applicant = val.relationship_to_applicant;
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
      this.bookingParam.coord_first_name = val.coord_first_name;
      this.bookingParam.coord_title = val.coord_title;
      this.bookingParam.coord_last_name = val.coord_last_name;
      this.bookingParam.coord_contact_no = val.coord_contact_no;
    },
  },
};
</script>
