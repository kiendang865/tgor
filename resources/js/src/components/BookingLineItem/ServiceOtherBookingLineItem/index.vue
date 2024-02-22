<template>
  <b-container fluid="lg" class="booking-service-other-field">
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Booking #">
          <div class="position-relative input-date">
            <b-form-input v-model.trim="ohtersParams.booking_no" class="input-form" :disabled="true"></b-form-input>
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Booking Date">
          <div class="position-relative input-date">
            <Datepicker
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :class="{ 'form-group--error': $v.ohtersParams.booking_date.$error }"
              v-model.trim="$v.ohtersParams.booking_date.$model"
              :format="customFormatter"
            ></Datepicker>
            <div class="error-date" v-if="!$v.ohtersParams.booking_date.required && $v.ohtersParams.booking_date.$error">Field is required</div>
            <Calendar />
          </div>
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
        <b-form-group label="Service Name">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.ohtersParams.service_id.$error }"
            v-model.trim="$v.ohtersParams.service_id.$model"
            :options="listOthers"
            placeholder="Select one"
            label="service_name"
            track-by="id"
          ></multiselect>
          <div class="error" v-if="!$v.ohtersParams.service_id.required && $v.ohtersParams.service_id.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Service Type Name">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.ohtersParams.service_type_id.$error }"
            v-model.trim="$v.ohtersParams.service_type_id.$model"
            :options="optionServiceType"
            placeholder="Select one"
            label="service_name"
            track-by="id"
          ></multiselect>
          <div class="error" v-if="!$v.ohtersParams.service_type_id.required && $v.ohtersParams.service_type_id.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Price">
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
      <b-col cols="9">
        <b-form-group label="Remarks">
          <b-form-input
            class="input-form"
            :class="{ 'form-group--error': $v.ohtersParams.remarks.$error }"
            v-model.trim="$v.ohtersParams.remarks.$model"
          ></b-form-input>
          <div class="error" v-if="!$v.ohtersParams.remarks.required && $v.ohtersParams.remarks.$error">Field is required</div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3" :class="{ 'd-none': isType }">
        <b-form-group label="Renting Date">
          <div class="position-relative input-date">
            <Datepicker
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :class="{ 'form-group--error': $v.ohtersParams.renting_date.$error }"
              v-model.trim="$v.ohtersParams.renting_date.$model"
            ></Datepicker>
            <div class="error-date" v-if="!$v.ohtersParams.renting_date.required && $v.ohtersParams.renting_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3" :class="{ 'd-none': isType }">
        <b-form-group label="Return Date">
          <div class="position-relative input-date">
            <Datepicker
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :class="{ 'form-group--error': $v.ohtersParams.return_date.$error }"
              v-model.trim="$v.ohtersParams.return_date.$model"
            ></Datepicker>
            <div class="error-date" v-if="!$v.ohtersParams.return_date.required && $v.ohtersParams.return_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3" :class="{ 'd-none': isType }">
        <b-form-group label="Duration">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.ohtersParams.duration.$error }"
            v-model.trim="$v.ohtersParams.duration.$model"
            :options="listDuration"
            placeholder="Select one"
            label="reference_value_text"
            track-by="id"
          ></multiselect>
          <div class="error" v-if="!$v.ohtersParams.duration.required && $v.ohtersParams.duration.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3" :class="{ 'd-none': isCon }">
        <b-form-group label="Contractor">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.ohtersParams.contractor_id.$error }"
            v-model.trim="$v.ohtersParams.contractor_id.$model"
            :options="listAllContracotr"
            placeholder="Select one"
            label="company_name"
            track-by="id"
          ></multiselect>
          <div class="error" v-if="!$v.ohtersParams.contractor_id.required && $v.ohtersParams.contractor_id.$error">Field is required</div>
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

export default {
  name: "ServiceNichesBookingFields",
  components: {
    Datepicker,
    Calendar,
    Multiselect,
    MaskedInput,
  },
  props: {
    otherItem: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      optionsNiche: [],
      otherParams: {
        booking_date: "",
        service_id: "",
        amount: "",
      },
      ohtersParams: {
        id: "",
        booking_no: "",
        booking_type_id: "",
        user_id: "",
        booking_date: "",
        service_id: "",
        remarks: "",
        renting_date: "",
        return_date: "",
        duration: "",
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
            required,
          },
          renting_date: {
            // required
          },
          return_date: {
            // required
          },
          duration: {
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
              required,
            },
            renting_date: {
              // required
            },
            return_date: {
              // required
            },
            duration: {
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
              remarks: {
                required,
              },
              renting_date: {
                required,
              },
              return_date: {
                required,
              },
              duration: {
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
              remarks: {
                required,
              },
              renting_date: {
                required,
              },
              return_date: {
                required,
              },
              duration: {
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
    listDuration: (state) => state.booking.listDuration,
  }),
  created() {
    this.getListService();
    this.getListAllContractor();
    this.getListDuration();
    EventBus.$on("save-service-other", this.saveService);
  },
  methods: {
    ...mapActions({
      getListService: "booking/getListService",
      getListAllContractor: "booking/getListAllContractor",
      getListDuration: "booking/getListDuration",
      updateService: "booking/updateService",
    }),
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
        return;
      } else {
        let prms = { ...this.ohtersParams };

        prms.amount = this.formatMoney(prms.amount);
        prms.booking_date = this.customFormatForSave(prms.booking_date);
        if (this.isType == false) {
          prms.renting_date = this.customFormatForSave(prms.renting_date);
          prms.return_date = this.customFormatForSave(prms.return_date);
          prms.duration = prms.duration.id;
        }

        prms.contractor_id = prms.contractor_id.id;

        prms.service_id = prms.service_id.id;
        prms.service_type_id = prms.service_type_id.id;
        // this.$emit('serviceItem',prms,this.$v.ohtersParams.$anyError)
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
      val != null ? (this.ohtersParams.amount = val.price) : "";
    },
    otherItem: function (val) {
      this.ohtersParams.id = val.id;
      this.ohtersParams.booking_no = val.booking.booking_no;
      this.ohtersParams.booking_type_id = val.booking_type_id;
      this.ohtersParams.user_id = val.client.id;
      this.ohtersParams.booking_date = val.booking_date;
      this.ohtersParams.service_id = val.other;
      this.ohtersParams.remarks = val.remarks;
      this.ohtersParams.renting_date = val.renting_date;
      this.ohtersParams.return_date = val.return_date;
      this.ohtersParams.duration = val.duration;
      this.ohtersParams.amount = val.amount;
      this.ohtersParams.service_type_id = val.service_type;
      this.ohtersParams.contractor_id = val.contractor;
    },
  },
};
</script>
