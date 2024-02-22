<template>
  <b-container fluid="lg" class="booking-service-other-field">
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Booking Date <span class="_require">*</span></label>
          <div class="position-relative input-date">
            <Datepicker
              placeholder="dd/mm/yyyy"
              :class="[
                'choose-date',
                {
                  'form-group--error': $v.ohtersParams.booking_date.$error,
                  'bg-white': !isAdmin && !isInvoice,
                  'bg-gray-disable': isAdmin && isInvoice,
                },
              ]"
              v-model.trim="$v.ohtersParams.booking_date.$model"
              :format="customFormatter"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></Datepicker>
            <div class="error-date" v-if="!$v.ohtersParams.booking_date.required && $v.ohtersParams.booking_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Service Type <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.ohtersParams.service_id.$error, 'bg-white': !isAdmin && !isInvoice, 'bg-gray': isAdmin && isInvoice }"
            v-model.trim="$v.ohtersParams.service_id.$model"
            :options="listOthers"
            placeholder="Select one"
            label="service_name"
            track-by="id"
            @select="resetDescription"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
          ></multiselect>
          <div class="error" v-if="!$v.ohtersParams.service_id.required && $v.ohtersParams.service_id.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="6">
        <b-form-group>
          <label class="_label_input">Description <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{
              'form-group--error': $v.ohtersParams.service_type_id.$error,
              'bg-white': !isAdmin && !isInvoice,
              'bg-gray': isAdmin && isInvoice,
            }"
            v-model.trim="$v.ohtersParams.service_type_id.$model"
            :options="optionServiceType"
            placeholder="Select one"
            label="service_name"
            track-by="id"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
          ></multiselect>
          <div class="error" v-if="!$v.ohtersParams.service_type_id.required && $v.ohtersParams.service_type_id.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Price <span class="_require">*</span></label>
          <masked-input
            type="text"
            class="form-control"
            v-model.trim="$v.ohtersParams.amount.$model"
            :mask="numberAmountMask()"
            :guide="true"
            :disabled="true"
            placeholder="$0.00"
          >
          </masked-input>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3" :class="{ 'd-none': isType }">
        <b-form-group>
          <label class="_label_input">Start Date <span class="_require">*</span></label>
          <div class="position-relative input-date">
            <Datepicker
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :class="{
                'form-group--error': $v.ohtersParams.start_date.$error,
                'bg-white': !isAdmin && !isInvoice,
                'bg-gray-disable': isAdmin && isInvoice,
              }"
              v-model.trim="$v.ohtersParams.start_date.$model"
              :format="customFormatter"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></Datepicker>
            <div class="error-date" v-if="!$v.ohtersParams.start_date.required && $v.ohtersParams.start_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3" :class="{ 'd-none': isType }">
        <b-form-group>
          <label class="_label_input">End Date <span class="_require">*</span></label>
          <div class="position-relative input-date">
            <Datepicker
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :class="{
                'form-group--error': $v.ohtersParams.end_date.$error,
                'bg-white': !isAdmin && !isInvoice,
                'bg-gray-disable': isAdmin && isInvoice,
              }"
              v-model.trim="$v.ohtersParams.end_date.$model"
              :format="customFormatter"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></Datepicker>
            <div class="error-date" v-if="!$v.ohtersParams.end_date.required && $v.ohtersParams.end_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="6" :class="{ 'd-none': isCon }">
        <b-form-group>
          <label class="_label_input">Contractor <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{
              'form-group--error': $v.ohtersParams.contractor_id.$error,
              'bg-white': !isAdmin && !isInvoice,
              'bg-gray': isAdmin && isInvoice,
            }"
            v-model.trim="$v.ohtersParams.contractor_id.$model"
            :options="listAllContracotr"
            placeholder="Select contractor"
            label="company_name"
            track-by="id"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
          ></multiselect>
          <div class="error" v-if="!$v.ohtersParams.contractor_id.required && $v.ohtersParams.contractor_id.$error">Field is required</div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="9">
        <b-form-group label="Remarks">
          <b-form-input class="input-form" v-model="ohtersParams.remarks" :disabled="isDisibleSA"></b-form-input>
        </b-form-group>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import Calendar from "@/components/Icons/Calendar";
import Multiselect from "vue-multiselect";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
var accounting = require("accounting");
import { EventBus } from "../../../event-bus";
import moment from "moment";
import { mapActions, mapState } from "vuex";
import { required, minLength, between } from "vuelidate/lib/validators";
import { isNil } from "lodash";

export default {
  name: "ServiceNichesBookingFields",
  components: {
    Datepicker,
    Calendar,
    Multiselect,
    MaskedInput,
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
      optionsNiche: [{ name: "R001" }, { name: "R002" }, { name: "R003" }, { name: "R004" }, { name: "R005" }],
      otherParams: {
        booking_date: "",
        service_id: "",
        amount: "",
      },
      ohtersParams: {
        booking_date: "",
        service_id: "",
        remarks: "",
        start_date: "",
        end_date: "",
        amount: "",
        service_type_id: "",
        contractor_id: "",
      },
      optionServiceType: [],
      isType: false,
      isCon: false,
    };
  },
  validations() {
    if (this.isType == true && this.isCon == true) {
      return {
        ohtersParams: {
          booking_date: {
            required,
          },
          service_id: {
            required,
          },
          remarks: {
            // required
          },
          start_date: {
            // required
          },
          end_date: {
            // required
          },
          amount: {
            // required
          },
          service_type_id: {
            required,
          },
          contractor_id: {
            // required
          },
        },
      };
    } else {
      if (this.isCon == false && this.isType == true) {
        return {
          ohtersParams: {
            booking_date: {
              required,
            },
            service_id: {
              required,
            },
            remarks: {
              // required
            },
            start_date: {
              // required
            },
            end_date: {
              // required
            },
            amount: {
              // required
            },
            service_type_id: {
              // required
            },
            contractor_id: {
              required,
            },
          },
        };
      } else {
        if (this.isCon == true && this.isType == false) {
          return {
            ohtersParams: {
              booking_date: {
                required,
              },
              service_id: {
                required,
              },
              start_date: {
                required,
              },
              end_date: {
                required,
              },
              amount: {
                // required
              },
              service_type_id: {
                required,
              },
              contractor_id: {
                // required
              },
            },
          };
        } else {
          return {
            ohtersParams: {
              booking_date: {
                required,
              },
              service_id: {
                required,
              },
              start_date: {
                required,
              },
              end_date: {
                required,
              },
              amount: {
                // required
              },
              service_type_id: {
                required,
              },
              contractor_id: {
                required,
              },
            },
          };
        }
      }
    }
  },
  computed: mapState({
    listOthers: (state) => state.booking.listOthers,
    listAllContracotr: (state) => state.booking.listAllContracotr,
  }),
  created() {
    this.getItem(this.item);
    this.getListService();
    this.getListAllContractor();
    EventBus.$on("save-service-booking", this.saveService);
    if (!this.ohtersParams.booking_date) {
      this.ohtersParams.booking_date = moment().format("MM/DD/YYYY");
    }
  },
  methods: {
    ...mapActions({
      getListService: "booking/getListService",
      getListAllContractor: "booking/getListAllContractor",
    }),
    getItem(val) {
      if (Object.keys(val).length > 3) {
        this.ohtersParams.id = val.id;
        this.ohtersParams.booking_no = val.booking.booking_no;
        this.ohtersParams.booking_type_id = val.booking_type_id;
        this.ohtersParams.user_id = val.client.id;
        this.ohtersParams.booking_date = val.booking_date;
        this.ohtersParams.service_id = val.other;
        this.ohtersParams.remarks = val.remarks;
        this.ohtersParams.start_date = val.start_date;
        this.ohtersParams.end_date = val.end_date;
        this.ohtersParams.amount = val.amount;
        this.ohtersParams.service_type_id = val.service_type;
        this.ohtersParams.contractor_id = val.contractor;
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
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    customFormatForSave(date) {
      return moment(date).format("YYYY-MM-DD");
    },
    formatMoney(value) {
      let val = 0;
      val = accounting.unformat(value);
      return val;
    },
    saveService() {
      this.$v.ohtersParams.$touch();
      if (this.$v.ohtersParams.$anyError) {
        this.$swal({
          title: "Warning!",
          text: "Some field are blank. Please fill them up",
          icon: "warning",
        });
        return;
      } else {
        let prms = { ...this.ohtersParams };

        prms.amount = this.formatMoney(prms.amount);
        prms.booking_date = this.customFormatForSave(prms.booking_date);
        if (this.isType == false) {
          prms.start_date = this.customFormatForSave(prms.start_date);
          prms.end_date = this.customFormatForSave(prms.end_date);
        }
        if (this.ohtersParams.contractor_id) {
          prms.contractor_id = prms.contractor_id.id;
        }
        prms.service_id = prms.service_id.id;
        prms.service_type_id = prms.service_type_id.id;
        this.$emit("serviceItem", prms, this.$v.ohtersParams.$anyError);
      }
    },
    resetDescription() {
      this.ohtersParams.service_type_id = null;
    },
    checkDateOther(check_out_date, checkin_date) {
      if (!isNil(check_out_date) && !isNil(checkin_date)) {
        if (check_out_date.getDay() >= checkin_date.getDay()) {
          let checkOutDay = check_out_date.getDay();
          let checkInDay = checkin_date.getDay();
          this.ohtersParams.amount = Math.round(this.ohtersParams.service_type_id.price * (checkOutDay - checkInDay + 1)).toFixed(2);
        }
      }
    },
  },
  watch: {
    "ohtersParams.service_id": function (val) {
      val != null
        ? val && val.children.length > 0
          ? ((this.optionServiceType = val.children),
            val.contractor.reference_value_text === "No" ? (this.isCon = true) : (this.isCon = false),
            val.type.reference_value_text === "Sale" ? (this.isType = true) : (this.isType = false))
          : ((this.ohtersParams.amount = ""), (this.optionServiceType = []), (this.ohtersParams.service_type_id = ""))
        : ((this.ohtersParams.amount = ""),
          (this.optionServiceType = []),
          (this.ohtersParams.service_type_id = ""),
          (this.isType = false),
          (this.isCon = false));
    },
    "ohtersParams.service_type_id": function (val) {
      if (val != null) {
        this.ohtersParams.amount = val.price;
        const end_date = new Date(this.ohtersParams.end_date);
        const start_date = new Date(this.ohtersParams.start_date);
        this.checkDateOther(end_date, start_date);
      }
    },
    "ohtersParams.start_date": function (val) {
      if (val) {
        const start_date = new Date(val);
        const end_date = new Date(this.ohtersParams.end_date);
        this.checkDateOther(end_date, start_date);
      }
    },
    "ohtersParams.end_date": function (val) {
      if (val) {
        const end_date = new Date(val);
        const start_date = new Date(this.ohtersParams.start_date);
        this.checkDateOther(end_date, start_date);
      }
    },
    // "ohtersParams.return_date": function(val) {
    //   if (val != null) {
    //     var renting_day = new Date(this.ohtersParams.start_date);

    //     var return_day = new Date(val);

    //     if (
    //       return_day.getTime() < renting_day.getTime() ||
    //       return_day.getDate() != renting_day.getDate() ||
    //       return_day.getMonth() != renting_day.getMonth() ||
    //       return_day.getFullYear() != renting_day.getFullYear()
    //     ) {
    //       this.$nextTick(() => {
    //         this.ohtersParams.return_date = null;
    //       });
    //       this.$swal({
    //         title: "Warning",
    //         text: "The return date  must be same as renting date",
    //         icon: "warning",
    //       });
    //     }
    //   }
    // },
    // "ohtersParams.renting_date": function(val) {
    //   if (val != null) {
    //     var renting_day = new Date(val);

    //     var return_day = new Date(this.ohtersParams.return_date);

    //     if (
    //       return_day.getTime() < renting_day.getTime() ||
    //       return_day.getDate() != renting_day.getDate() ||
    //       return_day.getMonth() != renting_day.getMonth() ||
    //       return_day.getFullYear() != renting_day.getFullYear()
    //     ) {
    //       this.$nextTick(() => {
    //         this.ohtersParams.return_date = null;
    //       });
    //     }
    //   }
    // },
  },
};
</script>
