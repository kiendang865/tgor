<template>
  <b-container fluid="lg" class="booking-service-niches-field">
    <b-row>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Application Date <span class="_require">*</span></label>
          <div class="position-relative input-date">
            <Datepicker
              :class="{
                'form-group--error': $v.nicheParam.application_date.$error,
                'bg-white': !isAdmin && !isInvoice,
                'bg-gray-disable': isAdmin && isInvoice,
              }"
              v-model.trim="$v.nicheParam.application_date.$model"
              :format="customFormatter"
              class="choose-date"
              placeholder="dd/mm/yyyy"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            >
            </Datepicker>
            <div class="error-date" v-if="!$v.nicheParam.application_date.required && $v.nicheParam.application_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group class="list-niches">
          <label class="_label_input">Niche Reference No. <span class="_require">*</span></label>
          <multiselect
            open-direction="bottom"
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{ 'form-group--error': $v.nicheParam.reference_no.$error, 'bg-white': !isAdmin && !isInvoice, 'bg-gray': isAdmin && isInvoice }"
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
            @input="nextInputFocus"
            :disabled="isDisibleSA || (isAdmin && isInvoice)"
          ></multiselect>

          <!-- <multiselect
            :allow-empty="false"
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
        <b-row class="no-gutters">
          <b-col cols="6">
            <b-form-group class="mr-2">
              <label class="_label_input">Type <span class="_require">*</span></label>
              <b-form-input
                :class="{
                  'form-group--error': $v.nicheParam.type_id.$error,
                }"
                v-model.trim="$v.nicheParam.type_id.$model"
                class="input-form"
                :disabled="true"
              ></b-form-input>
              <div class="error" v-if="!$v.nicheParam.type_id.required && $v.nicheParam.type_id.$error">Field is required</div>
            </b-form-group>
          </b-col>
          <b-col cols="6">
            <b-form-group class="ml-2">
              <label class="_label_input">Occupancy <span class="_require">*</span></label>
              <b-form-input
                :class="{
                  'form-group--error': $v.nicheParam.occupancy.$error,
                }"
                v-model.trim="$v.nicheParam.occupancy.$model"
                class="input-form"
                :disabled="true"
              ></b-form-input>
              <div class="error" v-if="!$v.nicheParam.occupancy.required && $v.nicheParam.occupancy.$error">Field is required</div>
            </b-form-group>
          </b-col>
        </b-row>
      </b-col>
      <b-col cols="6">
        <b-form-group>
          <label class="_label_input">Location <span class="_require">*</span></label>
          <b-form-input :disabled="true" v-model.trim="nicheParam.location" class="input-form"></b-form-input>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Lease Start Date">
          <div class="position-relative input-date">
            <Datepicker
              v-model="nicheParam.lease_start_date"
              :format="customFormatter"
              :class="[
                'choose-date start-date',
                { 'input-disabled': isDisableExpriyDay != 1 || (isAdmin && isInvoice), 'bg-white': !isAdmin && !isInvoice },
              ]"
              placeholder="dd/mm/yyyy"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></Datepicker>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Lease Expiry Date <span class="_require">*</span></label>
          <div class="position-relative input-date">
            <Datepicker
              :class="{
                'form-group--error': $v.nicheParam.lease_expiry_date.$error,
                'bg-white': !isAdmin && !isInvoice,
                'bg-gray-disable': isAdmin && isInvoice,
              }"
              v-model.trim="$v.nicheParam.lease_expiry_date.$model"
              :format="customFormatter"
              class="choose-date input-disabled"
              placeholder="dd/mm/yyyy"
              :disabled="true"
            ></Datepicker>
            <div class="error-date" v-if="!$v.nicheParam.lease_expiry_date.required && $v.nicheParam.lease_expiry_date.$error">Field is required</div>
            <Calendar />
          </div>
        </b-form-group>
      </b-col>
      <!-- <b-col cols="3" >
        <b-form-group label="Discount">
          <multiselect :show-labels="false" deselect-label="" :class="{'form-group--error': $v.nicheParam.discount.$error }" v-model.trim="$v.nicheParam.discount.$model" :options="listRate" placeholder="Select one" label="name" track-by="rate"></multiselect>
          <div class="error" v-if="!$v.nicheParam.discount.required && $v.nicheParam.discount.$error">Field is required</div>
        </b-form-group>
      </b-col> -->
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Price (excl.GST) <span class="_require">*</span></label>
          <masked-input
            type="text"
            v-model.trim="$v.nicheParam.amount.$model"
            class="form-control"
            :mask="numberAmountMask()"
            :guide="true"
            :disabled="true"
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
          <b-form-group :label="`Occupant ${+index + 1} Full Name`">
            <b-form-input
              :class="{ 'form-group--error': v.full_name.$error }"
              v-model.trim="v.full_name.$model"
              class="input-form"
              placeholder="Name"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></b-form-input>
            <div class="error" v-if="!v.full_name.required && v.full_name.$error">Field is required</div>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Relationship to Applicant">
            <multiselect
              :show-labels="false"
              deselect-label=""
              v-model.trim="v.relationship_to_applicant.$model"
              :options="listRelationship"
              placeholder="Select one"
              label="reference_value_text"
              track-by="id"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
              :class="{ 'bg-white': !isAdmin && !isInvoice, 'bg-gray': isAdmin && isInvoice }"
            ></multiselect>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Date of Death">
            <div class="position-relative input-date">
              <Datepicker
                :format="customFormatter"
                :class="['choose-date', { 'input-disabled': isDisableExpriyDay != 1 || (isAdmin && isInvoice), 'bg-white': !isAdmin && !isInvoice }]"
                placeholder="dd/mm/yyyy"
                v-model.trim="v.death_anniversary.$model"
                :disabled="isDisibleSA || (isAdmin && isInvoice)"
              ></Datepicker>
              <Calendar />
            </div>
          </b-form-group>
        </b-col>
      </b-row>
    </template>
    <template>
      <b-row class="mt">
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">F.D. Referral? <span class="_require">*</span></label>
            <multiselect
              :show-labels="false"
              :allow-empty="false"
              deselect-label=""
              :class="{
                'form-group--error': $v.nicheParam.book_funeral_director.$error,
                'bg-white': !isAdmin && !isInvoice,
                'bg-gray': isAdmin && isInvoice,
              }"
              v-model.trim="$v.nicheParam.book_funeral_director.$model"
              :options="yesNoOptions"
              placeholder="Select one"
              label="name"
              track-by="name"
              @select="changeIsReferral"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            >
            </multiselect>

            <div class="error" v-if="!$v.nicheParam.book_funeral_director.required && $v.nicheParam.book_funeral_director.$error">
              Field is required
            </div>
          </b-form-group>
        </b-col>
        <b-col cols="6" v-if="isFuneral">
          <b-form-group label="">
            <label class="_label_input">Funeral Director <span class="_require">*</span></label>
            <multiselect
              :show-labels="false"
              :allow-empty="false"
              deselect-label=""
              v-model="nicheParam.funeral_director_id"
              :options="listDirector"
              placeholder="Select one"
              label="company_name"
              track-by="id"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
              :class="{ 'bg-white': !isAdmin && !isInvoice, 'bg-gray': isAdmin && isInvoice }"
            >
            </multiselect>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">Is there a Co-Leasee? <span class="_require">*</span></label>
            <multiselect
              :show-labels="false"
              :allow-empty="false"
              deselect-label=""
              :class="{ 'form-group--error': $v.nicheParam.co_license.$error, 'bg-white': !isAdmin && !isInvoice, 'bg-gray': isAdmin && isInvoice }"
              v-model.trim="$v.nicheParam.co_license.$model"
              :options="listCoLisesen"
              placeholder="Select one"
              label="reference_value_text"
              track-by="id"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></multiselect>
            <div class="error" v-if="!$v.nicheParam.co_license.required && $v.nicheParam.co_license.$error">Field is required</div>
          </b-form-group>
        </b-col>
      </b-row>
    </template>
    <template v-if="isCoLisense">
      <b-row class="mt">
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">Full Name <span class="_require">*</span></label>
            <b-form-input
              :class="{
                'form-group--error': $v.nicheParam.co_license_name.$error,
              }"
              v-model.trim="$v.nicheParam.co_license_name.$model"
              class="input-form"
              @keyup.enter="$refs.email.$el.focus()"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></b-form-input>
            <div class="error" v-if="!$v.nicheParam.co_license_name.required && $v.nicheParam.co_license_name.$error">Field is required</div>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">NRIC / Passport No. <span class="_require">*</span></label>
            <b-form-input
              :class="{
                'form-group--error': $v.nicheParam.co_license_passport.$error,
              }"
              v-model.trim="$v.nicheParam.co_license_passport.$model"
              class="input-form"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></b-form-input>
            <div class="error" v-if="!$v.nicheParam.co_license_passport.required && $v.nicheParam.co_license_passport.$error">Field is required</div>
            <div class="error" v-else-if="!$v.nicheParam.co_license_passport.decimal && $v.nicheParam.co_license_passport.$error">
              Please enter number phone
            </div>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">Mobile</label>
            <b-form-input v-model="nicheParam.co_license_phone" class="input-form" :disabled="isDisibleSA || (isAdmin && isInvoice)"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">Email</label>
            <b-form-input
              v-model="nicheParam.co_license_email"
              class="input-form"
              ref="email"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
    </template>
    <template v-if="isCoLisense">
      <b-row class="mt">
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">Same address with Applicant?</label>
            <multiselect
              :show-labels="false"
              deselect-label=""
              v-model="nicheParam.co_license_same_address"
              :options="yesNoOptions"
              placeholder="Select one"
              label="name"
              track-by="name"
              @select="handleShowSameAddress"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
              :class="{ 'bg-white': !isAdmin && !isInvoice, 'bg-gray': isAdmin && isInvoice }"
            >
            </multiselect>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Postal code">
            <b-form-input
              v-model.trim="$v.nicheParam.co_license_postal_code.$model"
              class="input-form"
              :class="{
                'form-group--error': $v.nicheParam.co_license_postal_code.$error,
              }"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
              maxlength="6"
            ></b-form-input>
            <div class="error" v-if="!$v.nicheParam.co_license_postal_code.decimal && $v.nicheParam.co_license_postal_code.$error">
              Please enter number
            </div>
          </b-form-group>
        </b-col>
        <b-col cols="6">
          <b-form-group>
            <label class="_label_input">Address <span class="_require">*</span></label>
            <b-form-input
              :class="{
                'form-group--error': $v.nicheParam.co_license_street_name.$error,
              }"
              v-model.trim="$v.nicheParam.co_license_street_name.$model"
              class="input-form"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></b-form-input>
            <div class="error" v-if="!$v.nicheParam.co_license_street_name.required && $v.nicheParam.co_license_street_name.$error">
              Field is required
            </div>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Relationship with main Lessee">
            <multiselect
              :show-labels="false"
              deselect-label=""
              :class="{
                'bg-white': !isCoLisense || (!isAdmin && !isInvoice),
                'bg-gray': isCoLisense || (isAdmin && isInvoice),
              }"
              v-model="nicheParam.relationship_with_license"
              :options="listRelationshipColisense"
              placeholder="Select"
              label="reference_value_text"
              track-by="id"
              :disabled="isDisibleSA || (isAdmin && isInvoice)"
            ></multiselect>
          </b-form-group>
        </b-col>
      </b-row>
    </template>
  </b-container>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import Calendar from "@/components/Icons/Calendar";
import moment from "moment";
import Multiselect from "vue-multiselect";
import { mapActions, mapState } from "vuex";
import { required, minLength, between, decimal, email } from "vuelidate/lib/validators";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
var accounting = require("accounting");
import { EventBus } from "../../../event-bus";
import $ from "jquery";
import _ from "lodash";

export default {
  name: "ServiceNichesBookingFields",
  components: { Datepicker, Calendar, Multiselect, MaskedInput },
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
      isLoading: false,
      valueNiche: null,
      nicheParam: {
        id: "",
        reference_no: "",
        type_id: "",
        occupancy: "",
        amount: "",
        location: "",
        service_id: "",
        discount: "",
        lease_start_date: "",
        lease_expiry_date: "",
        application_date: "",
        co_license: null,
        co_license_name: "",
        co_license_email: "",
        co_license_phone: "",
        //
        co_license_passport: "",
        co_license_postal_code: "",
        co_license_same_address: "",
        co_license_street_name: "",
        relationship_with_license: "",
        information: [
          {
            full_name: "",
            relationship_to_applicant: "",
            death_anniversary: "",
          },
        ],
        book_funeral_director: null,
        funeral_director_id: "",
      },
      isCoLisense: false,
      openPicker: false,
      old_postal: "",
      yesNoOptions: [{ name: "Yes" }, { name: "No" }],
      isFuneral: false,
      isDisableExpriyDay: 0,
      same_address: false,
      isEdit: false,
    };
  },
  validations() {
    if (this.isCoLisense == false && this.isFuneral == false) {
      return {
        nicheParam: {
          reference_no: {
            required,
          },
          type_id: {
            required,
          },
          occupancy: {
            required,
          },
          amount: {
            required,
          },
          co_license: {
            required,
          },
          application_date: {
            required,
          },
          lease_expiry_date: {
            required,
          },
          co_license_postal_code: {
            decimal,
          },
          information: {
            // required,
            $each: {
              full_name: {
                // required,
              },
              relationship_to_applicant: {
                // required,
              },
              death_anniversary: {
                // required,
              },
            },
          },
          book_funeral_director: {
            required,
          },
        },
      };
    } else {
      if (this.isCoLisense == true && this.isFuneral == false) {
        if (this.same_address == true) {
          return {
            nicheParam: {
              reference_no: {
                required,
              },
              type_id: {
                required,
              },
              occupancy: {
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
              co_license_passport: {
                required,
              },
              co_license_street_name: {},
              application_date: {
                required,
              },
              lease_expiry_date: {
                required,
              },
              co_license_postal_code: {
                decimal,
              },
              information: {
                // required,
                $each: {
                  full_name: {
                    // required,
                  },
                  relationship_to_applicant: {
                    // required,
                  },
                  death_anniversary: {
                    // required,
                  },
                },
              },
              book_funeral_director: {
                required,
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
              occupancy: {
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
              co_license_passport: {
                required,
              },
              co_license_street_name: {
                required,
              },
              application_date: {
                required,
              },
              lease_expiry_date: {
                required,
              },
              co_license_postal_code: {
                decimal,
              },
              information: {
                // required,
                $each: {
                  full_name: {
                    // required,
                  },
                  relationship_to_applicant: {
                    // required,
                  },
                  death_anniversary: {
                    // required,
                  },
                },
              },
              book_funeral_director: {
                required,
              },
            },
          };
        }
      } else {
        if (this.isCoLisense == false && this.isFuneral == true) {
          return {
            nicheParam: {
              reference_no: {
                required,
              },
              type_id: {
                required,
              },
              occupancy: {
                required,
              },
              amount: {
                required,
              },
              co_license: {
                required,
              },
              application_date: {
                required,
              },
              lease_expiry_date: {
                required,
              },
              co_license_postal_code: {
                decimal,
              },
              information: {
                // required,
                $each: {
                  full_name: {
                    // required,
                  },
                  relationship_to_applicant: {
                    // required,
                  },
                  death_anniversary: {
                    // required,
                  },
                },
              },
              book_funeral_director: {
                required,
              },
            },
          };
        } else {
          if (this.same_address == true) {
            return {
              nicheParam: {
                reference_no: {
                  required,
                },
                type_id: {
                  required,
                },
                occupancy: {
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
                co_license_passport: {
                  required,
                },
                co_license_street_name: {},
                application_date: {
                  required,
                },
                lease_expiry_date: {
                  required,
                },
                co_license_postal_code: {
                  decimal,
                },
                information: {
                  // required,
                  $each: {
                    full_name: {
                      // required,
                    },
                    relationship_to_applicant: {
                      // required,
                    },
                    death_anniversary: {
                      // required,
                    },
                  },
                },
                book_funeral_director: {
                  required,
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
                occupancy: {
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
                co_license_passport: {
                  required,
                },
                co_license_street_name: {
                  required,
                },
                application_date: {
                  required,
                },
                lease_expiry_date: {
                  required,
                },
                co_license_postal_code: {
                  decimal,
                },
                information: {
                  // required,
                  $each: {
                    full_name: {
                      // required,
                    },
                    relationship_to_applicant: {
                      // required,
                    },
                    death_anniversary: {
                      // required,
                    },
                  },
                },
                book_funeral_director: {
                  required,
                },
              },
            };
          }
        }
      }
    }
  },
  created() {
    this.getItem(this.item);
    this.getListNichForBooking();
    this.getListRate();
    this.getListRelationship();
    this.getListCoLisense();
    this.getListRelationshipCoLisense();
    this.getListReferral();
    EventBus.$on("save-service-booking", this.saveService);
    if (!this.nicheParam.application_date) {
      this.nicheParam.application_date = moment().format("MM/DD/YYYY");
    }
    if (!this.nicheParam.lease_expiry_date) {
      this.nicheParam.lease_expiry_date = moment("08/12/2059").format("MM/DD/YYYY");
    }
    if (!this.nicheParam.lease_start_date) {
      this.nicheParam.lease_start_date = moment().format("MM/DD/YYYY");
    }
    if (this.nicheParam.book_funeral_director == "" || this.nicheParam.book_funeral_director == null) {
      this.nicheParam.book_funeral_director = this.yesNoOptions[1];
    }
    if (this.nicheParam.co_license_same_address == "" || this.nicheParam.co_license_same_address == null) {
      this.nicheParam.co_license_same_address = this.yesNoOptions[1];
    }
    this.getListDirector();
    if (typeof localStorage !== "undefined") {
      this.isDisableExpriyDay = JSON.parse(localStorage.getItem("admin_profile")).roles_id;
    }
  },
  computed: mapState({
    listNicheForBooking: (state) => state.niche.listNicheForBooking,
    listRate: (state) => state.booking.listRate,
    listRelationship: (state) => state.booking.listRelationship,
    listCoLisesen: (state) => state.booking.listCoLisesen,
    listRelationshipColisense: (state) => state.booking.listRelationshipColisense,
    arr_niches: (state) => state.booking.arr_niches,
    listReferral: (state) => state.booking.listReferral,
    listDirector: (state) => state.booking.listDirector,
    clientAddress: (state) => state.booking.clientAddress,
    clientPostalCode: (state) => state.booking.clientPostalCode,
  }),
  methods: {
    ...mapActions({
      getListNichForBooking: "niche/getListNichForBooking",
      getListRate: "booking/getListRate",
      getListRelationship: "booking/getListRelationship",
      getListCoLisense: "booking/getListCoLisense",
      getListRelationshipCoLisense: "booking/getListRelationshipCoLisense",
      findAdress: "address/findAdress",
      getListReferral: "booking/getListReferral",
      getListDirector: "booking/getListDirector",
    }),
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    customFormatForSave(date) {
      if (date && date != "undefinded") return moment(date).format("YYYY-MM-DD");
      else return null;
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
        this.$swal({
          title: "Warning!",
          text: "Some field are blank. Please fill them up",
          icon: "warning",
        });
        return;
      } else {
        if (!this.isEdit) {
          let prms = JSON.parse(JSON.stringify(this.nicheParam));

          prms.amount = this.formatMoney(prms.amount);
          prms.application_date = this.customFormatForSave(prms.application_date);
          prms.lease_expiry_date = this.customFormatForSave(prms.lease_expiry_date);
          if (prms.lease_start_date) {
            prms.lease_start_date = this.customFormatForSave(prms.lease_start_date);
          }
          // prms.discount = prms.discount.id;
          prms.service_id = prms.reference_no.id;
          if (prms.co_license) {
            prms.co_license = prms.co_license.id;
          }
          if (prms.relationship_with_license) {
            prms.relationship_with_license = prms.relationship_with_license.id;
          }
          prms.reference_no = prms.reference_no.reference_no;
          if (prms.information) {
            prms.information.map((item, key) => {
              if (item.relationship_to_applicant) {
                prms.information[key].relationship_to_applicant = item.relationship_to_applicant.id;
              }
              if (item.death_anniversary) prms.information[key].death_anniversary = this.customFormatForSave(item.death_anniversary);
            });
          }
          prms.book_funeral_director = prms.book_funeral_director.name;
          if (prms.funeral_director_id) {
            prms.funeral_director_id = prms.funeral_director_id.id;
          }
          if (prms.co_license_same_address) {
            prms.co_license_same_address = prms.co_license_same_address.name;
          }
          this.$emit("serviceItem", prms, this.$v.nicheParam.$anyError);
        } else {
          let prms = {};
          const nicheParam = JSON.parse(JSON.stringify(this.nicheParam));
          prms.amount = this.formatMoney(nicheParam.amount);
          prms.application_date = this.customFormatForSave(nicheParam.application_date);
          prms.lease_expiry_date = this.customFormatForSave(nicheParam.lease_expiry_date);
          if (nicheParam.lease_start_date) {
            prms.lease_start_date = this.customFormatForSave(nicheParam.lease_start_date);
          }
          // prms.discount = prms.discount.id;
          prms.service_id = nicheParam.reference_no.id;
          if (nicheParam.co_license) {
            prms.co_license = nicheParam.co_license.id;
          }
          if (nicheParam.relationship_with_license) {
            prms.relationship_with_license = nicheParam.relationship_with_license.id;
          }
          if (nicheParam.information) {
            nicheParam.information.map((item, key) => {
              if (item.relationship_to_applicant) {
                nicheParam.information[key].relationship_to_applicant = item.relationship_to_applicant.id;
              }
              if (item.death_anniversary) nicheParam.information[key].death_anniversary = this.customFormatForSave(item.death_anniversary);
            });
            prms.information = nicheParam.information;
          }
          prms.book_funeral_director = nicheParam.book_funeral_director.name;
          if (prms.funeral_director_id) {
            prms.funeral_director_id = nicheParam.funeral_director_id.id;
          }
          if (prms.co_license_same_address) {
            prms.co_license_same_address = nicheParam.co_license_same_address.name;
          }
          prms.id = nicheParam.id;
          prms.type_id = nicheParam.type_id;
          prms.occupancy = nicheParam.occupancy;
          prms.location = nicheParam.location;
          prms.discount = nicheParam.discount.id;
          prms.co_license_name = nicheParam.co_license_name;
          prms.co_license_email = nicheParam.co_license_email;
          prms.co_license_phone = nicheParam.co_license_phone;
          prms.co_license_passport = nicheParam.co_license_passport;
          prms.co_license_postal_code = nicheParam.co_license_postal_code;
          prms.co_license_street_name = nicheParam.co_license_street_name;
          this.$emit("serviceItem", prms, this.$v.nicheParam.$anyError);
        }
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
                  full_name: "",
                  relationship_to_applicant: "",
                  death_anniversary: "",
                },
                {
                  full_name: "",
                  relationship_to_applicant: "",
                  death_anniversary: "",
                }
              )
            : this.nicheParam.information.push({
                full_name: "",
                relationship_to_applicant: "",
                death_anniversary: "",
              });

          return;
        } else {
          if (this.nicheParam.information.length == 0) {
            this.nicheParam.information.push({
              full_name: "",
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
    nextInputFocus() {
      $(".start-date input[type='text']").trigger("click");
    },
    getItem(val) {
      if (Object.keys(val).length > 3) {
        this.isEdit = true;
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
        this.nicheParam.type_id = val.niche.category.reference_value_text;
        this.nicheParam.category = val.niche.type.reference_value_text;
        if (val.lease_start_date) {
          this.nicheParam.lease_start_date = val.lease_start_date;
        }
        this.nicheParam.lease_expiry_date = val.lease_expiry_date;
        // this.nicheParam.discount = val.booking_discount;
        this.nicheParam.co_license_passport = val.co_license_passport;
        if (val.co_license_postal_code) {
          this.nicheParam.co_license_postal_code = val.co_license_postal_code;
          this.old_postal = val.co_license_postal_code;
        }
        this.nicheParam.co_license_street_name = val.co_license_street_name;
        if (val.information) {
          this.nicheParam.information = val.information;
        }
        this.nicheParam.co_license = val.co_license;
        this.nicheParam.co_license_name = val.co_license_name;
        this.nicheParam.co_license_email = val.co_license_email;
        this.nicheParam.co_license_phone = val.co_license_phone;
        if (val.relationship_with_license) {
          this.nicheParam.relationship_with_license = val.relationship_with_license;
        }
        if (val.book_funeral_director == "Yes") {
          this.nicheParam.book_funeral_director = this.yesNoOptions[0];
        } else {
          this.nicheParam.book_funeral_director = this.yesNoOptions[1];
        }
        if (val.same_address == "Yes") {
          this.nicheParam.co_license_same_address = this.yesNoOptions[0];
        } else {
          this.nicheParam.co_license_same_address = this.yesNoOptions[1];
        }
        this.nicheParam.funeral_director_id = val.funeral_director;
      }
    },
    changeIsReferral(data) {
      this.nicheParam.funeral_director_id = "";
    },
    handleShowSameAddress(selectedOption) {
      if (selectedOption) {
        if (selectedOption.name == "Yes") {
          this.nicheParam.co_license_street_name = this.clientAddress;
          this.same_address = true;
        } else {
          this.nicheParam.co_license_street_name = "";
          this.same_address = false;
        }
      } else {
        this.same_address = false;
      }
    },
  },
  watch: {
    "nicheParam.reference_no": function (val) {
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

        this.nicheParam.type_id = val.category.reference_value_text;
        this.nicheParam.occupancy = val.type.reference_value_text;
        this.nicheParam.amount = val.price;
        this.nicheParam.location = val.location;
        // this.nicheParam.information.map((item,key) => {
        //   item.relationship_to_applicant = this.listRelationship[0]
        // })
      } else {
        this.removeInformation();

        this.nicheParam.type_id = "";
        this.nicheParam.occupancy = "";
        this.nicheParam.amount = "";
        this.nicheParam.location = "";
      }
    },
    "nicheParam.co_license": function (val) {
      val != null ? (val.reference_value_text == "Yes" ? (this.isCoLisense = true) : (this.isCoLisense = false)) : (this.isCoLisense = false);
    },
    "nicheParam.co_license_same_address": function (val) {
      if (val) {
        if (val.name == "Yes") {
          this.nicheParam.co_license_street_name = this.clientAddress;
          this.nicheParam.co_license_postal_code = this.clientPostalCode;
          this.same_address = true;
        }
      } else {
        this.same_address = false;
      }
    },
    listCoLisesen: function (val) {
      if (!this.nicheParam.co_license) this.nicheParam.co_license = val[1];
    },
    listRelationship: function (val) {
      // this.nicheParam.information.map((item,key) => {
      //   item.relationship_to_applicant = val[0]
      // })
    },
    // item: function (val) {
    //   this.nicheParam.id = val.id
    //   this.nicheParam.booking_no = val.booking.booking_no
    //   this.nicheParam.booking_type_id = val.booking_type_id
    //   this.nicheParam.user_id = val.client.id
    //   this.nicheParam.application_date = val.application_date;
    //   this.nicheParam.id = val.id;
    //   this.nicheParam.reference_no = val.niche;
    //   this.nicheParam.interment_date = val.interment_date;
    //   this.nicheParam.location = val.niche.full_location;
    //   this.nicheParam.amount = val.niche.price;
    //   this.nicheParam.start_date = val.start_date;
    //   this.nicheParam.expiry_date = val.expiry_date;
    //   this.nicheParam.discount = val.booking_discount;
    //   this.nicheParam.information = val.information;
    //   this.nicheParam.co_license = val.co_license;
    //   this.nicheParam.co_license_name = val.co_license_name;
    //   this.nicheParam.co_license_email = val.co_license_email;
    //   this.nicheParam.co_license_phone = val.co_license_phone;
    //   this.nicheParam.relationship_with_license = val.relationship_with_license
    // }
    // "nicheParam.co_license_postal_code": {
    //   deep: true,
    //   handler: _.debounce(function() {
    //     if (this.nicheParam.co_license_postal_code.toString().length == 6) {
    //       if (
    //         !!this.nicheParam.co_license_postal_code &&
    //         this.nicheParam.co_license_postal_code !== this.old_postal
    //       ) {
    //         this.$emit("loading", true);
    //         let prms = { postal_code: this.nicheParam.co_license_postal_code };
    //         this.findAdress(prms)
    //           .then((res) => {
    //             this.$emit("loading", false);
    //             this.nicheParam.co_license_street_name = res.data.data?.address;
    //           })
    //           .catch((error) => {
    //             this.$emit("loading", false);
    //             this.nicheParam.co_license_street_name = "";
    //           });
    //       }
    //     }
    //   }, 200),
    // },
    "nicheParam.book_funeral_director": function (val) {
      if (val != "" || val != null) {
        if (val.name == "No") {
          this.isFuneral = false;
        } else {
          this.isFuneral = true;
        }
      } else {
        this.nicheParam.book_funeral_director = this.yesNoOptions[0];
      }
    },
  },
};
</script>
