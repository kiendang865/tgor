<template>
  <div class="booking-info-other">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Additional Service Booking Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div>
        <b-tabs class="tabs-index" v-model="tabIndex" nav-class="nav-cus">
          <b-tab nav-class="booking-service" class="booking-service" title="Booking Details" v-bind:class="linkClass(0)">
            <b-container fluid="lg" class="booking-service-other-field">
              <b-row class="mt">
                <b-col cols="3">
                  <b-form-group label="Item #">
                    <div class="position-relative input-date">
                      <b-form-input v-model.trim="ohtersParams.booking_no" class="input-form" :disabled="true"></b-form-input>
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Booking Date <span class="_require">*</span></label>
                    <div class="position-relative input-date">
                      <Datepicker
                        class="choose-date"
                        placeholder="dd/mm/yyyy"
                        :class="{
                          'form-group--error': $v.ohtersParams.booking_date.$error,
                        }"
                        v-model.trim="$v.ohtersParams.booking_date.$model"
                        :format="customFormatter"
                      ></Datepicker>
                      <div class="error-date" v-if="!$v.ohtersParams.booking_date.required && $v.ohtersParams.booking_date.$error">
                        Field is required
                      </div>
                      <Calendar />
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group label="Status">
                    <multiselect
                      :show-labels="false"
                      deselect-label=""
                      class="bg-gray"
                      v-model.trim="ohtersParams.status"
                      :disabled="true"
                      :options="listStatusOther"
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
                    <label class="_label_input">Service Type <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.ohtersParams.service_id.$error,
                      }"
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
                  <b-form-group>
                    <label class="_label_input">Description <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.ohtersParams.service_type_id.$error,
                      }"
                      v-model.trim="$v.ohtersParams.service_type_id.$model"
                      :options="optionServiceType"
                      placeholder="Select one"
                      label="service_name"
                      track-by="id"
                    ></multiselect>
                    <div class="error" v-if="!$v.ohtersParams.service_type_id.required && $v.ohtersParams.service_type_id.$error">
                      Field is required
                    </div>
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
                        }"
                        v-model.trim="$v.ohtersParams.start_date.$model"
                        :format="customFormatter"
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
                        }"
                        v-model.trim="$v.ohtersParams.end_date.$model"
                        :format="customFormatter"
                      ></Datepicker>
                      <div class="error-date" v-if="!$v.ohtersParams.end_date.required && $v.ohtersParams.end_date.$error">Field is required</div>
                      <Calendar />
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3" :class="{ 'd-none': isCon }">
                  <b-form-group>
                    <label class="_label_input">Contractor <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.ohtersParams.contractor_id.$error,
                      }"
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
              <b-row class="mt">
                <b-col cols="9">
                  <b-form-group label="Remarks">
                    <b-form-input
                      class="input-form"
                      :class="{
                        'form-group--error': $v.ohtersParams.remarks.$error,
                      }"
                      v-model.trim="$v.ohtersParams.remarks.$model"
                    ></b-form-input>
                    <div class="error" v-if="!$v.ohtersParams.remarks.required && $v.ohtersParams.remarks.$error">Field is required</div>
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
import Multiselect from "vue-multiselect";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
var accounting = require("accounting");
import moment from "moment";
import { required, minLength, between } from "vuelidate/lib/validators";
import { mapActions, mapState } from "vuex";
import { EventBus } from "../../../event-bus";

export default {
  name: "ServiceRoomBooking",
  components: {
    ChevronLeft,
    Remarks,
    CustomerInfo,
    MaskedInput,
    Datepicker,
    Calendar,
    Multiselect,
  },
  metaInfo: {
    title: "Other Services Booking Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Other Services Booking Info Description",
      },
    ],
  },
  data() {
    return {
      data: {},
      id: this.$router.history.current.params.id,
      tabIndex: 0,
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
        start_date: "",
        end_date: "",
        amount: "",
        service_type_id: "",
        contractor_id: "",
        status: "",
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
              remarks: {
                // required
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
              remarks: {
                // required
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
  created() {
    this.getItem();
    this.getListService();
    this.getListAllContractor();
    this.getListStatusOther();
  },
  computed: mapState({
    listOthers: (state) => state.booking.listOthers,
    listAllContracotr: (state) => state.booking.listAllContracotr,
    listStatusOther: (state) => state.booking.listStatusOther,
  }),
  methods: {
    ...mapActions({
      getItemServiceBooking: "service/getItemServiceBooking",
      getListService: "booking/getListService",
      getListAllContractor: "booking/getListAllContractor",
      updateService: "booking/updateService",
      getListStatusOther: "booking/getListStatusOther",
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
      this.$router.push("/others");
    },
    // onSave(){
    //   EventBus.$emit('save-service-other');
    // },
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
    onSave() {
      this.$v.ohtersParams.$touch();
      if (this.$v.ohtersParams.$anyError) {
        return;
      } else {
        let prms = { ...this.ohtersParams };

        prms.amount = this.formatMoney(prms.amount);
        prms.booking_date = this.customFormatForSave(prms.booking_date);
        if (this.isType == false) {
          prms.start_date = this.customFormatForSave(prms.start_date);
          prms.end_date = this.customFormatForSave(prms.end_date);
        }
        if (prms.contractor_id) {
          prms.contractor_id = prms.contractor_id.id;
        }

        if (prms.status) {
          prms.status = prms.status.id;
        }
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
          ? //  this.ohtersParams.service_type_id = '',
            //  this.ohtersParams.amount = '',
            ((this.optionServiceType = val.children),
            val.contractor.reference_value_text === "No" ? (this.isCon = true) : (this.isCon = false),
            val.type.reference_value_text === "Sale" ? (this.isType = true) : (this.isType = false))
          : ((this.ohtersParams.amount = ""), (this.optionServiceType = []), (this.ohtersParams.service_type_id = ""))
        : ((this.ohtersParams.amount = ""),
          (this.optionServiceType = []),
          (this.ohtersParams.service_type_id = ""),
          (this.isType = false),
          (this.isCon = false));
    },
    // "ohtersParams.service_type_id": function(val) {
    //   val != null ? (this.ohtersParams.amount = val.price) : "";
    // },
    // "ohtersParams.end_date": function(val) {
    //   if (val != null) {
    //     var renting_day = new Date(this.ohtersParams.renting_date);

    //     var return_day = new Date(val);
    //     if (return_day.getTime() < renting_day.getTime()) {
    //       this.$nextTick(() => {
    //         this.ohtersParams.return_date = "";
    //       });
    //       this.$swal({
    //         title: "Warning",
    //         text: "The return date  must be same as renting date",
    //         icon: "warning",
    //       });
    //     }
    //   }
    // },
    data: function (val) {
      this.ohtersParams.id = val.id;
      this.ohtersParams.booking_no = val.booking_no;
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
      this.ohtersParams.status = val.status;
    },
  },
};
</script>
