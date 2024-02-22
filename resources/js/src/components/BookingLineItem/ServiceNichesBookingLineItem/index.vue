<template>
  <b-container fluid="lg" class="booking-service-niches-field">
    <b-row>
      <b-col cols="3">
        <b-form-group label="Booking #">
          <div class="position-relative input-date">
            <b-form-input v-model.trim="nicheParam.booking_no" class="input-form" :disabled="true"></b-form-input>
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <!-- <b-col cols="3">
        <b-form-group label="Application No">
          <div class="position-relative input-date">
            <Datepicker 
              :class="{'form-group--error': $v.nicheParam.application_date.$error }" 
              v-model.trim="$v.nicheParam.application_date.$model"
              :format="customFormatter" 
              class="choose-date" 
              placeholder="dd/mm/yyyy" 
              >
            </Datepicker>
            <div class="error-date" v-if="!$v.nicheParam.application_date.required && $v.nicheParam.application_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col> -->
      <b-col cols="3">
        <b-form-group label="Application Date">
          <div class="position-relative input-date">
            <Datepicker
              :class="{ 'form-group--error': $v.nicheParam.application_date.$error }"
              v-model.trim="$v.nicheParam.application_date.$model"
              :format="customFormatter"
              class="choose-date"
              placeholder="dd/mm/yyyy"
            >
            </Datepicker>
            <div class="error-date" v-if="!$v.nicheParam.application_date.required && $v.nicheParam.application_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Interment Date">
          <div class="position-relative input-date">
            <Datepicker v-model.trim="nicheParam.interment_date" :format="customFormatter" class="choose-date" placeholder="dd/mm/yyyy"> </Datepicker>
            <!-- <div class="error-date" v-if="!$v.nicheParam.interment_date.required && $v.nicheParam.interment_date.$error">Field is required</div> -->
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
        <b-form-group label="Niche Reference No.">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.nicheParam.reference_no.$error }"
            v-model.trim="$v.nicheParam.reference_no.$model"
            :options="listNicheForBooking"
            placeholder="Select one"
            label="reference_no"
            track-by="id"
            :searchable="true"
            :loading="isLoading"
            :options-limit="300"
            :limit="3"
            :limit-text="limitText"
            :max-height="600"
            :show-no-results="false"
            :hide-selected="true"
            @search-change="asyncFind"
          ></multiselect>

          <!-- <multiselect 
            :class="{'form-group--error': $v.nicheParam.reference_no.$error }" 
            v-model.trim="$v.nicheParam.reference_no.$model"
            label="reference_no" 
            track-by="id"
            :multiple="true"
            placeholder="Type to search"  :options="listNicheForBooking"  :searchable="true" :loading="isLoading" :internal-search="false" :clear-on-select="false" :close-on-select="false" :options-limit="300" :limit="3" :limit-text="limitText" :max-height="600" :show-no-results="false" :hide-selected="true" @search-change="asyncFind"> -->
          <div class="error" v-if="!$v.nicheParam.reference_no.required && $v.nicheParam.reference_no.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Type">
          <b-form-input
            :class="{ 'form-group--error': $v.nicheParam.type_id.$error }"
            v-model.trim="$v.nicheParam.type_id.$model"
            class="input-form"
          ></b-form-input>
          <div class="error" v-if="!$v.nicheParam.type_id.required && $v.nicheParam.type_id.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="6">
        <b-form-group label="Location">
          <b-form-input v-model.trim="nicheParam.location" class="input-form"></b-form-input>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Lease Start Date">
          <div class="position-relative input-date">
            <Datepicker
              :class="{ 'form-group--error': $v.nicheParam.start_date.$error }"
              v-model.trim="$v.nicheParam.start_date.$model"
              :format="customFormatter"
              class="choose-date"
              placeholder="DD/MM/YYYY H:mm:ss"
            ></Datepicker>
            <div class="error-date" v-if="!$v.nicheParam.start_date.required && $v.nicheParam.start_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Lease Expiry Date">
          <Datepicker
            :class="{ 'form-group--error': $v.nicheParam.expiry_date.$error }"
            v-model.trim="$v.nicheParam.expiry_date.$model"
            :format="customFormatter"
            class="choose-date"
            placeholder="DD/MM/YYYY H:mm:ss"
          ></Datepicker>
          <div class="error" v-if="!$v.nicheParam.expiry_date.required && $v.nicheParam.expiry_date.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Discount">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.nicheParam.discount.$error }"
            v-model.trim="$v.nicheParam.discount.$model"
            :options="listRate"
            placeholder="Select one"
            label="name"
            track-by="rate"
          ></multiselect>
          <div class="error" v-if="!$v.nicheParam.discount.required && $v.nicheParam.discount.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Nett Price (excl.GST)">
          <masked-input
            type="text"
            v-model.trim="$v.nicheParam.amount.$model"
            class="form-control"
            :mask="numberAmountMask()"
            :guide="true"
            placeholder="$0.00"
          >
          </masked-input>
        </b-form-group>
        <!-- :class="{'form-group--error': checkPrice }" -->
      </b-col>
    </b-row>
    <template v-for="(v, index) in $v.nicheParam.information.$each.$iter">
      <b-row class="mt" :key="index">
        <b-col cols="3">
          <b-form-group :label="`Occupant 0${+index + 1} First Name`">
            <b-form-input
              :class="{ 'form-group--error': v.first_name.$error }"
              v-model.trim="v.first_name.$model"
              class="input-form"
              placeholder="Name"
            ></b-form-input>
            <div class="error" v-if="!v.first_name.required && v.first_name.$error">Field is required</div>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group :label="`Occupant 0${+index + 1} Last Name`">
            <b-form-input
              :class="{ 'form-group--error': v.last_name.$error }"
              v-model.trim="v.last_name.$model"
              class="input-form"
              placeholder="Name"
            ></b-form-input>
            <div class="error" v-if="!v.last_name.required && v.last_name.$error">Field is required</div>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Relationship to Applicant">
            <multiselect
              :show-labels="false"
              deselect-label=""
              :class="{ 'form-group--error': v.relationship_to_applicant.$error }"
              v-model.trim="v.relationship_to_applicant.$model"
              :options="listRelationship"
              placeholder="Select one"
              label="reference_value_text"
              track-by="id"
            ></multiselect>
            <div class="error" v-if="!v.relationship_to_applicant.required && v.relationship_to_applicant.$error">Field is required</div>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Death Anniversary">
            <div class="position-relative input-date">
              <Datepicker
                :format="customFormatter"
                class="choose-date"
                placeholder="DD/MM/YYYY"
                :class="{ 'form-group--error': v.death_anniversary.$error }"
                v-model.trim="v.death_anniversary.$model"
              ></Datepicker>
              <div class="error-date" v-if="!v.death_anniversary.required && v.death_anniversary.$error">Field is required</div>
              <Calendar />
            </div>
          </b-form-group>
        </b-col>
      </b-row>
    </template>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Do you have Co-liense?">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.nicheParam.co_license.$error }"
            v-model.trim="$v.nicheParam.co_license.$model"
            :options="listCoLisesen"
            placeholder="Select one"
            label="reference_value_text"
            track-by="id"
          ></multiselect>
          <div class="error" v-if="!$v.nicheParam.co_license.required && $v.nicheParam.co_license.$error">Field is required</div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Name">
          <b-form-input
            :class="{ 'form-group--error': $v.nicheParam.co_license_name.$error }"
            v-model.trim="$v.nicheParam.co_license_name.$model"
            :disabled="isCoLisense"
            class="input-form"
          ></b-form-input>
          <div class="error" v-if="!$v.nicheParam.co_license_name.required && $v.nicheParam.co_license_name.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Email">
          <b-form-input
            :class="{ 'form-group--error': $v.nicheParam.co_license_email.$error }"
            v-model.trim="$v.nicheParam.co_license_email.$model"
            :disabled="isCoLisense"
            class="input-form"
          ></b-form-input>
          <div class="error" v-if="!$v.nicheParam.co_license_email.required && $v.nicheParam.co_license_email.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Phone Number">
          <b-form-input
            :class="{ 'form-group--error': $v.nicheParam.co_license_phone.$error }"
            v-model.trim="$v.nicheParam.co_license_phone.$model"
            :disabled="isCoLisense"
            class="input-form"
          ></b-form-input>
          <div class="error" v-if="!$v.nicheParam.co_license_phone.required && $v.nicheParam.co_license_phone.$error">Field is required</div>
          <div class="error" v-if="!$v.nicheParam.co_license_phone.decimal && $v.nicheParam.co_license_phone.$error">Please enter number phone</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Relationship with main Lessee">
          <multiselect
            :show-labels="false"
            deselect-label=""
            :disabled="isCoLisense"
            :class="{ 'form-group--error': $v.nicheParam.relationship_with_license.$error }"
            v-model.trim="$v.nicheParam.relationship_with_license.$model"
            :options="listRelationshipColisense"
            placeholder="Select"
            label="reference_value_text"
            track-by="name"
          ></multiselect>
          <div class="error" v-if="!$v.nicheParam.relationship_with_license.required && $v.nicheParam.relationship_with_license.$error">
            Field is required
          </div>
        </b-form-group>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import Calendar from "@/components/Icons/Calendar";
import moment from "moment";
import Multiselect from "vue-multiselect";
import { mapActions, mapState } from "vuex";
import { required, minLength, between, decimal } from "vuelidate/lib/validators";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
var accounting = require("accounting");
import { EventBus } from "../../../event-bus";

export default {
  name: "ServiceNichesBookingFields",
  components: { Datepicker, Calendar, Multiselect, MaskedInput },
  props: {
    nichesItem: {
      type: Object,
      default: {},
    },
  },
  data() {
    return {
      isLoading: false,
      valueNiche: null,
      optionsNiche: [],
      nicheParam: {
        id: "",
        booking_type_id: "",
        interment_date: "",
        user_id: "",
        reference_no: "",
        booking_no: "",
        type_id: "",
        amount: "",
        location: "",
        service_id: "",
        discount: "",
        start_date: "",
        expiry_date: "",
        application_date: "",
        co_license: "",
        co_license_name: "",
        co_license_email: "",
        co_license_phone: "",
        relationship_with_license: "",
        information: [
          {
            first_name: "",
            last_name: "",
            relationship_to_applicant: "",
            death_anniversary: "",
          },
        ],
      },
      isCoLisense: false,
    };
  },
  validations() {
    if (this.isCoLisense == false) {
      return {
        nicheParam: {
          reference_no: {
            required,
          },
          type_id: {
            required,
          },
          amount: {
            required,
          },
          co_license: {
            required,
          },
          co_license_name: {
            required,
          },
          co_license_email: {
            required,
          },
          co_license_phone: {
            required,
            decimal,
          },
          relationship_with_license: {
            required,
          },
          discount: {
            required,
          },
          application_date: {
            required,
          },
          expiry_date: {
            required,
          },
          start_date: {
            required,
          },
          information: {
            required,
            $each: {
              first_name: {
                required,
              },
              last_name: {
                required,
              },
              relationship_to_applicant: {
                required,
              },
              death_anniversary: {
                required,
              },
            },
          },
        },
      };
    } else {
      return {
        nicheParam: {
          reference_no: {
            required,
          },
          type_id: {
            required,
          },
          amount: {
            required,
          },
          co_license: {
            // required,
          },
          co_license_name: {
            // required,
          },
          co_license_email: {
            // required,
          },
          co_license_phone: {
            // required,
          },
          relationship_with_license: {
            // required,
          },
          discount: {
            required,
          },
          application_date: {
            required,
          },
          expiry_date: {
            required,
          },
          start_date: {
            required,
          },
          information: {
            required,
            $each: {
              first_name: {
                required,
              },
              last_name: {
                required,
              },
              relationship_to_applicant: {
                required,
              },
              death_anniversary: {
                required,
              },
            },
          },
        },
      };
    }
  },
  created() {
    this.getListNichForBooking();
    this.getListRate();
    this.getListRelationship();
    this.getListCoLisense();
    this.getListRelationshipCoLisense();
    EventBus.$on("save-service-niche", this.saveService);
  },
  computed: mapState({
    listNicheForBooking: (state) => state.niche.listNicheForBooking,
    listRate: (state) => state.booking.listRate,
    listRelationship: (state) => state.booking.listRelationship,
    listCoLisesen: (state) => state.booking.listCoLisesen,
    listRelationshipColisense: (state) => state.booking.listRelationshipColisense,
  }),
  methods: {
    ...mapActions({
      getListNichForBooking: "niche/getListNichForBooking",
      getListRate: "booking/getListRate",
      getListRelationship: "booking/getListRelationship",
      getListCoLisense: "booking/getListCoLisense",
      getListRelationshipCoLisense: "booking/getListRelationshipCoLisense",
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
    saveService() {
      this.$v.nicheParam.$touch();
      if (this.$v.nicheParam.$anyError) {
        return;
      } else {
        let prms = JSON.parse(JSON.stringify(this.nicheParam));

        prms.amount = this.formatMoney(prms.amount);
        prms.application_date = this.customFormatForSave(prms.application_date);
        prms.expiry_date = this.customFormatForSave(prms.expiry_date);
        prms.start_date = this.customFormatForSave(prms.start_date);
        if (prms.interment_date) {
          prms.interment_date = this.customFormatForSave(prms.interment_date);
        }
        prms.discount = prms.discount.id;
        prms.service_id = prms.reference_no.id;
        prms.co_license = prms.co_license.id;
        prms.relationship_with_license = prms.relationship_with_license.id;

        prms.reference_no = prms.reference_no.reference_no;

        prms.information.map((item, key) => {
          prms.information[key].relationship_to_applicant = item.relationship_to_applicant.id;
          prms.information[key].death_anniversary = this.customFormatForSave(item.death_anniversary);
        });

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
        // this.$emit('serviceItem',prms,this.$v.nicheParam.$anyError)
      }
    },
    addMoreInformation() {
      if (this.nicheParam.information.length == 2) {
        return;
      } else {
        if (
          this.nicheParam.reference_no.type.reference_value_text == "Double" ||
          this.nicheParam.reference_no.type.reference_value_text == "Premium double"
        ) {
          this.nicheParam.information.length == 0
            ? this.nicheParam.information.push(
                {
                  first_name: "",
                  last_name: "",
                  relationship_to_applicant: "",
                  death_anniversary: "",
                },
                {
                  first_name: "",
                  last_name: "",
                  relationship_to_applicant: "",
                  death_anniversary: "",
                }
              )
            : this.nicheParam.information.push({
                first_name: "",
                last_name: "",
                relationship_to_applicant: "",
                death_anniversary: "",
              });

          return;
        } else {
          if (this.nicheParam.information.length == 0) {
            this.nicheParam.information.push({
              first_name: "",
              last_name: "",
              relationship_to_applicant: "",
              death_anniversary: "",
            });
          }
        }
        return;
      }
    },
    removeInformation() {
      this.nicheParam.information.length >= 2
        ? this.nicheParam.information.pop()
        : this.nicheParam.reference_no.type.reference_value_text != "Double" ||
          this.nicheParam.reference_no.type.reference_value_text != "Premium double"
        ? this.addMoreInformation()
        : (this.nicheParam.information.length = 2);
    },
    formatMoney(value) {
      let val = 0;
      val = accounting.unformat(value);
      return val;
    },
    limitText(count) {
      return `and ${count} other countries`;
    },
    asyncFind(query) {
      this.isLoading = true;
      let prms = {
        filter: {
          name: query,
        },
      };
      this.getListNichForBooking(prms).then((response) => {
        this.isLoading = false;
      });
    },
  },
  watch: {
    "nicheParam.reference_no": function (val) {
      //  if(val != null){
      //     (val.type.id == 10 || val.type.id == 12) ? this.addMoreInformation() : this.removeInformation();

      //     this.nicheParam.type_id = val.type.reference_value_text
      //     this.nicheParam.amount = val.price
      //     this.nicheParam.location = val.location
      //     this.nicheParam.information.map((item,key) => {
      //       item.relationship_to_applicant = this.listRelationship[0]
      //   })
      //  }
      //  else{
      //    this.removeInformation();

      //     this.nicheParam.type_id = ''
      //     this.nicheParam.amount = ''
      //     this.nicheParam.location = ''
      //  }
      if (val != null) {
        if (this.arr_niches) {
          if (this.arr_niches.length == 0) {
            this.$store.commit("booking/updateArrNiches", val.id);
          } else {
            let isCheck = this.arr_niches.includes(val.id);
            if (!isCheck) {
              this.$store.commit("booking/updateArrNiches", val.id);
            } else {
              //
              this.$store.commit("booking/updateArrNiches", val.id);
            }
          }
        }

        val.type.reference_value_text == "Double" || val.type.reference_value_text == "Premium double"
          ? this.addMoreInformation()
          : this.removeInformation();

        this.nicheParam.type_id = val.type.reference_value_text;
        this.nicheParam.amount = val.price;
        this.nicheParam.location = val.location;
        // this.nicheParam.information.map((item,key) => {
        //   item.relationship_to_applicant = this.listRelationship[0]
        // })
      } else {
        this.removeInformation();

        this.nicheParam.type_id = "";
        this.nicheParam.amount = "";
        this.nicheParam.location = "";
      }
    },
    "nicheParam.co_license": function (val) {
      val != null ? (val.reference_value_text == "Yes" ? (this.isCoLisense = false) : (this.isCoLisense = true)) : (this.isCoLisense = false);
    },
    listCoLisesen: function (val) {
      this.nicheParam.co_lisense = val[0];
    },
    listRelationship: function (val) {
      this.nicheParam.information.map((item, key) => {
        item.relationship_to_applicant = val[0];
      });
    },
    nichesItem: function (val) {
      this.nicheParam.id = val.id;
      this.nicheParam.booking_no = val.booking.booking_no;
      this.nicheParam.booking_type_id = val.booking_type_id;
      this.nicheParam.user_id = val.client.id;
      this.nicheParam.application_date = val.application_date;
      this.nicheParam.id = val.id;
      this.nicheParam.reference_no = val.niche;
      this.nicheParam.interment_date = val.interment_date;
      this.nicheParam.location = val.niche.full_location;
      this.nicheParam.amount = val.niche.price;
      this.nicheParam.start_date = val.start_date;
      this.nicheParam.expiry_date = val.expiry_date;
      this.nicheParam.discount = val.booking_discount;
      this.nicheParam.information = val.information;
      this.nicheParam.co_license = val.co_license;
      this.nicheParam.co_license_name = val.co_license_name;
      this.nicheParam.co_license_email = val.co_license_email;
      this.nicheParam.co_license_phone = val.co_license_phone;
      this.nicheParam.relationship_with_license = val.relationship_with_license;
    },
  },
};
</script>
