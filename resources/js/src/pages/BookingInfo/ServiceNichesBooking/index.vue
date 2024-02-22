<template>
  <div class="booking-info-niches">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Columbarium Niches Booking Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-extension" @click="showModal" v-if="tabIndex == 0">Extension</b-button>
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div>
        <b-tabs class="tabs-index" v-model="tabIndex" nav-class="nav-cus">
          <b-tab nav-class="booking-service" class="booking-service" title="Booking Details" v-bind:class="linkClass(0)">
            <b-container fluid="lg" class="booking-service-niches-field">
              <b-row>
                <b-col cols="3">
                  <b-form-group label="Item #">
                    <div class="position-relative input-date">
                      <b-form-input v-model.trim="nicheParam.booking_no" class="input-form" :disabled="true"></b-form-input>
                    </div>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-row class="mt">
                <b-col cols="3">
                  <b-form-group>
                    <label class="_label_input">Application Date <span class="_require">*</span></label>
                    <div class="position-relative input-date">
                      <Datepicker
                        :class="{
                          'form-group--error': $v.nicheParam.application_date.$error,
                        }"
                        v-model.trim="$v.nicheParam.application_date.$model"
                        :format="customFormatter"
                        class="choose-date"
                        placeholder="dd/mm/yyyy"
                      >
                      </Datepicker>
                      <div class="error-date" v-if="!$v.nicheParam.application_date.required && $v.nicheParam.application_date.$error">
                        Field is required
                      </div>
                      <Calendar />
                    </div>
                  </b-form-group>
                </b-col>
                <b-col cols="3">
                  <b-form-group label="Interment Date">
                    <div class="position-relative input-date">
                      <Datepicker v-model="nicheParam.interment_date" :format="customFormatter" class="choose-date" placeholder="dd/mm/yyyy">
                      </Datepicker>
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
                      class="bg-gray"
                      v-model.trim="nicheParam.status"
                      :disabled="true"
                      :options="listStatusNiches"
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
                    <label class="_label_input">Niche Reference No. <span class="_require">*</span></label>
                    <multiselect
                      :show-labels="false"
                      :allow-empty="false"
                      deselect-label=""
                      :class="{
                        'form-group--error': $v.nicheParam.reference_no.$error,
                      }"
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
                        class="choose-date"
                        placeholder="DD/MM/YYYY H:mm:ss"
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
                        }"
                        v-model.trim="$v.nicheParam.lease_expiry_date.$model"
                        :format="customFormatter"
                        class="choose-date"
                        placeholder="DD/MM/YYYY H:mm:ss"
                      ></Datepicker>
                      <div class="error-date" v-if="!$v.nicheParam.lease_expiry_date.required && $v.nicheParam.lease_expiry_date.$error">
                        Field is required
                      </div>
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
                      v-model="nicheParam.amount"
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
                  <b-col cols="6">
                    <b-form-group :label="`Occupant 0${+index + 1} Full Name`">
                      <b-form-input v-model.trim="v.full_name.$model" class="input-form" placeholder="Name"></b-form-input>
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
                      ></multiselect>
                    </b-form-group>
                  </b-col>
                  <b-col cols="3">
                    <b-form-group label="Date of Death">
                      <div class="position-relative input-date">
                        <Datepicker
                          :format="customFormatter"
                          class="choose-date"
                          placeholder="DD/MM/YYYY"
                          v-model.trim="v.death_anniversary.$model"
                        ></Datepicker>
                        <Calendar />
                      </div>
                    </b-form-group>
                  </b-col>
                </b-row>
              </template>
              <template v-if="!isReferral">
                <b-row class="mt">
                  <b-col cols="3">
                    <b-form-group>
                      <label class="_label_input">Do you have a referral? <span class="_require">*</span></label>
                      <multiselect
                        :show-labels="false"
                        :allow-empty="false"
                        deselect-label=""
                        :class="{
                          'form-group--error': $v.nicheParam.is_referral.$error,
                        }"
                        v-model.trim="$v.nicheParam.is_referral.$model"
                        :options="listReferral"
                        placeholder="Select one"
                        label="reference_value_text"
                        track-by="id"
                      ></multiselect>
                      <div class="error" v-if="!$v.nicheParam.is_referral.required && $v.nicheParam.is_referral.$error">Field is required</div>
                    </b-form-group>
                  </b-col>
                  <b-col cols="3">
                    <b-form-group>
                      <label class="_label_input">Is there a Co-Lessee? <span class="_require">*</span></label>
                      <multiselect
                        :show-labels="false"
                        :allow-empty="false"
                        deselect-label=""
                        :class="{
                          'form-group--error': $v.nicheParam.co_license.$error,
                        }"
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
              </template>
              <template v-else>
                <b-row class="mt">
                  <b-col cols="3">
                    <b-form-group>
                      <label class="_label_input">Do you have a referral? <span class="_require">*</span></label>
                      <multiselect
                        :show-labels="false"
                        :allow-empty="false"
                        deselect-label=""
                        :class="{
                          'form-group--error': $v.nicheParam.is_referral.$error,
                        }"
                        v-model.trim="$v.nicheParam.is_referral.$model"
                        :options="listReferral"
                        placeholder="Select one"
                        label="reference_value_text"
                        track-by="id"
                      ></multiselect>
                      <div class="error" v-if="!$v.nicheParam.is_referral.required && $v.nicheParam.is_referral.$error">Field is required</div>
                    </b-form-group>
                  </b-col>
                  <b-col cols="6">
                    <b-form-group>
                      <label class="_label_input">Name <span class="_require">*</span></label>
                      <b-form-input
                        :class="{
                          'form-group--error': $v.nicheParam.referral_name.$error,
                        }"
                        v-model.trim="$v.nicheParam.referral_name.$model"
                        class="input-form"
                      ></b-form-input>
                      <div class="error" v-if="!$v.nicheParam.referral_name.required && $v.nicheParam.referral_name.$error">Field is required</div>
                    </b-form-group>
                  </b-col>
                  <b-col cols="3">
                    <b-form-group>
                      <label class="_label_input">Is there a Co-Lessee? <span class="_require">*</span></label>
                      <multiselect
                        :show-labels="false"
                        :allow-empty="false"
                        deselect-label=""
                        :class="{
                          'form-group--error': $v.nicheParam.co_license.$error,
                        }"
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
                      ></b-form-input>
                      <div class="error" v-if="!$v.nicheParam.co_license_name.required && $v.nicheParam.co_license_name.$error">
                        Field is required
                      </div>
                    </b-form-group>
                  </b-col>
                  <b-col cols="3">
                    <b-form-group>
                      <label class="_label_input">NRIC / Passport No. <span class="_require">*</span></label>
                      <b-form-input v-model.trim="$v.nicheParam.co_license_passport.$model" class="input-form"></b-form-input>
                      <div class="error" v-if="!$v.nicheParam.co_license_passport.required && $v.nicheParam.co_license_passport.$error">
                        Field is required
                      </div>
                      <div class="error" v-else-if="!$v.nicheParam.co_license_passport.decimal && $v.nicheParam.co_license_passport.$error">
                        Please enter number phone
                      </div>
                    </b-form-group>
                  </b-col>
                  <b-col cols="3">
                    <b-form-group>
                      <label class="_label_input">Mobile <span class="_require">*</span></label>
                      <b-form-input
                        :class="{
                          'form-group--error': $v.nicheParam.co_license_phone.$error,
                        }"
                        v-model.trim="$v.nicheParam.co_license_phone.$model"
                        class="input-form"
                      ></b-form-input>
                      <div class="error" v-if="!$v.nicheParam.co_license_phone.required && $v.nicheParam.co_license_phone.$error">
                        Field is required
                      </div>
                      <div class="error" v-if="!$v.nicheParam.co_license_phone.decimal && $v.nicheParam.co_license_phone.$error">
                        Please enter number phone
                      </div>
                    </b-form-group>
                  </b-col>
                  <b-col cols="3">
                    <b-form-group>
                      <label class="_label_input">Email <span class="_require">*</span></label>
                      <b-form-input
                        :class="{
                          'form-group--error': $v.nicheParam.co_license_email.$error,
                        }"
                        v-model.trim="$v.nicheParam.co_license_email.$model"
                        class="input-form"
                      ></b-form-input>
                      <div class="error" v-if="!$v.nicheParam.co_license_email.required && $v.nicheParam.co_license_email.$error">
                        Field is required
                      </div>
                      <div class="error" v-else-if="!$v.nicheParam.co_license_email.email && $v.nicheParam.co_license_email.$error">
                        Please enter email
                      </div>
                    </b-form-group>
                  </b-col>
                </b-row>
              </template>
              <template v-if="isCoLisense">
                <b-row class="mt">
                  <b-col cols="3">
                    <b-form-group label="Postal code">
                      <b-form-input v-model="nicheParam.co_license_postal_code" class="input-form"></b-form-input>
                    </b-form-group>
                  </b-col>
                  <b-col cols="6">
                    <b-form-group>
                      <label class="_label_input">Street Name <span class="_require">*</span></label>
                      <b-form-input
                        :class="{
                          'form-group--error': $v.nicheParam.co_license_street_name.$error,
                        }"
                        v-model.trim="$v.nicheParam.co_license_street_name.$model"
                        class="input-form"
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
                        v-model="nicheParam.relationship_with_license"
                        :options="listRelationshipColisense"
                        placeholder="Select"
                        label="reference_value_text"
                        track-by="id"
                      ></multiselect>
                    </b-form-group>
                  </b-col>
                </b-row>
              </template>
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
    <b-modal centered hide-footer ref="extensionNiche" size="sm" title="Extension">
      <b-container fluid="lg">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Duration">
              <!-- :allow-empty="false" needs to be checked -->
              <multiselect
                id="singleLabel"
                :show-labels="false"
                deselect-label=""
                v-model.trim="extensionParam.duration"
                :options="durationOfNiche"
                placeholder="Select one"
                track-by="id"
                label="exten_year"
                :searchable="false"
                :allow-empty="false"
              >
                <template slot="singleLabel" slot-scope="{ option }">{{ option.exten_year }} years</template>
              </multiselect>
            </b-form-group>
          </b-col>
          <!-- <b-col cols="12" >
              <b-form-group label="Discount">
                <multiselect :show-labels="false" deselect-label=""  v-model.trim="extensionParam.discount" :options="listRate" placeholder="Select one" label="name" track-by="rate"></multiselect>
              </b-form-group>
            </b-col> -->
          <b-col cols="12">
            <b-form-group label="Price">
              <!-- <b-form-input v-model.trim="extensionParam.price" :disabled="true" class="input-form " placeholder="$00.00"></b-form-input> -->
              <masked-input
                type="text"
                v-model.trim="extensionParam.price"
                class="form-control"
                :mask="numberAmountMask()"
                :guide="true"
                :disabled="true"
                placeholder="$0,00"
              >
              </masked-input>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row class="btn-submit">
          <b-col cols="12">
            <div class="submit" @click="onSubmit">Submit</div>
          </b-col>
        </b-row>
      </b-container>
    </b-modal>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import Remarks from "@/components/Remarks";
import CustomerInfo from "@/components/CustomerInfo";
import Multiselect from "vue-multiselect";
import { mapActions, mapState } from "vuex";
//  import { EventBus } from '../../../event-bus';
import Datepicker from "vuejs-datepicker";
import Calendar from "@/components/Icons/Calendar";
import moment from "moment";
import { required, minLength, between, decimal } from "vuelidate/lib/validators";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
var accounting = require("accounting");
export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    Remarks,
    CustomerInfo,
    Multiselect,
    Datepicker,
    Calendar,
    MaskedInput,
  },
  metaInfo: {
    title: "Columbarium Niches Booking Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Columbarium Niches Booking Info Description",
      },
    ],
  },
  data() {
    return {
      tabIndex: 0,
      valueEx: null,
      data: {},
      id: this.$router.history.current.params.id,
      isLoading: false,
      valueNiche: null,
      optionsNiche: [
        {
          name: "30 years",
          value: 30,
        },
        {
          name: "50 years",
          value: 50,
        },
      ],
      is_sale: false,
      nicheParam: {
        id: "",
        booking_type_id: "",
        interment_date: "",
        user_id: "",
        reference_no: "",
        booking_no: "",
        booking_id: "",
        type_id: "",
        occupancy: "",
        amount: "",
        location: "",
        service_id: "",
        discount: "",
        lease_start_date: "",
        lease_expiry_date: "",
        application_date: "",
        co_license: "",
        co_license_name: "",
        co_license_email: "",
        co_license_phone: "",
        co_license_passport: "",
        co_license_postal_code: "",
        co_license_street_name: "",
        relationship_with_license: "",
        price_thirty_years: "",
        price_fifty_years: "",
        information: [
          {
            full_name: "",
            relationship_to_applicant: null,
            death_anniversary: "",
          },
        ],
        status: "",
        is_referral: null,
        referral_name: "",
      },
      extensionParam: {
        duration: "",
        discount: "",
        price: "",
      },
      isCoLisense: false,
      isReferral: false,
    };
  },
  validations() {
    if (this.isCoLisense == false && this.isReferral == false) {
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
          is_referral: {},
          referral_name: {},
        },
      };
    } else {
      if (this.isCoLisense == true && this.isReferral == false) {
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
            co_license_email: {
              required,
            },
            co_license_phone: {
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
            is_referral: {},
            referral_name: {},
          },
        };
      } else {
        if (this.isCoLisense == false && this.isReferral == true) {
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
              is_referral: {
                required,
              },
              referral_name: {
                required,
              },
            },
          };
        } else {
          // if(this.isCoLisense == true && this.isReferral == true)
          // {
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
              co_license_email: {
                required,
              },
              co_license_phone: {
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
              is_referral: {
                required,
              },
              referral_name: {
                required,
              },
            },
          };
          // }
        }
      }
    }
  },
  created() {
    this.getItem();
    this.getListNichForBooking();
    this.getListRate();
    this.getListRelationship();
    this.getListCoLisense();
    this.getListRelationshipCoLisense();
    this.getListStatusNiches();
    this.getListReferral();
    // this.extensionParam.duration = this.optionsNiche[0];
  },
  computed: mapState({
    listNicheForBooking: (state) => state.niche.listNicheForBooking,
    listRate: (state) => state.booking.listRate,
    listRelationship: (state) => state.booking.listRelationship,
    listCoLisesen: (state) => state.booking.listCoLisesen,
    listRelationshipColisense: (state) => state.booking.listRelationshipColisense,
    durationOfNiche: (state) => state.niche.durationOfNiche,
    listStatusNiches: (state) => state.booking.listStatusNiches,
    listReferral: (state) => state.booking.listReferral,
  }),
  methods: {
    ...mapActions({
      getItemServiceBooking: "service/getItemServiceBooking",
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
      this.$router.push("/service-niches");
    },
    ...mapActions({
      getListNichForBooking: "niche/getListNichForBooking",
      getListRate: "booking/getListRate",
      getListRelationship: "booking/getListRelationship",
      getListCoLisense: "booking/getListCoLisense",
      getListRelationshipCoLisense: "booking/getListRelationshipCoLisense",
      updateService: "booking/updateService",
      extensionNiche: "booking/extensionNiche",
      getDurationOfNiche: "niche/getDurationOfNiche",
      getListStatusNiches: "booking/getListStatusNiches",
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
    onSave() {
      this.$v.nicheParam.$touch();
      if (this.$v.nicheParam.$anyError) {
        return;
      } else {
        let prms = JSON.parse(JSON.stringify(this.nicheParam));

        prms.amount = this.formatMoney(prms.amount);
        prms.application_date = this.customFormatForSave(prms.application_date);
        prms.lease_expiry_date = this.customFormatForSave(prms.lease_expiry_date);
        prms.lease_start_date = this.customFormatForSave(prms.lease_start_date);

        if (prms.interment_date) {
          prms.interment_date = this.customFormatForSave(prms.interment_date);
        }
        // prms.discount = prms.discount.id;
        prms.service_id = prms.reference_no.id;
        prms.is_referral = prms.is_referral.id;
        if (prms.status) {
          prms.status = prms.status.id;
        }
        prms.co_license = prms.co_license.id;

        if (prms.relationship_with_license) {
          prms.relationship_with_license = prms.relationship_with_license.id;
        }
        prms.reference_no = prms.reference_no.reference_no;

        prms.information.map((item, key) => {
          prms.information[key].relationship_to_applicant = item.relationship_to_applicant ? item.relationship_to_applicant.id : null;

          prms.information[key].death_anniversary = item.death_anniversary ? this.customFormatForSave(item.death_anniversary) : null;
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
                  full_name: "",
                  relationship_to_applicant: null,
                  death_anniversary: "",
                },
                {
                  full_name: "",
                  relationship_to_applicant: null,
                  death_anniversary: "",
                }
              )
            : this.nicheParam.information.push({
                full_name: "",
                relationship_to_applicant: null,
                death_anniversary: "",
              });

          return;
        } else {
          if (this.nicheParam.information.length == 0) {
            this.nicheParam.information.push({
              full_name: "",
              relationship_to_applicant: null,
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
    showModal() {
      this.getDurationOfNiche({ niche_id: this.nicheParam.service_id }).then((res) => {
        this.extensionParam.duration = res.data.data[0];
      });

      this.$refs.extensionNiche.show();
    },
    onSubmit() {
      var arr = [];
      arr.push(this.nicheParam.booking_id);

      let prms = {
        arr_id: arr,
        user_id: this.nicheParam.user_id,
        // discount: this.extensionParam.discount ? this.extensionParam.discount.id : null,
        duration: this.extensionParam.duration.id,
      };
      this.extensionNiche(prms)
        .then((res) => {
          this.$refs.extensionNiche.hide();
          this.$store.commit("booking/updateNRS");
          this.$router.push({
            name: "BookingGeneralInfo",
            params: { id: res.data.data.id },
          });
        })
        .catch((error) => {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
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
      if (val != null) {
        if (val.reference_value_text == "Yes") {
          this.isCoLisense = true;
        } else {
          this.isCoLisense = false;
          this.nicheParam.co_license_name = "";
          this.nicheParam.co_license_email = "";
          this.nicheParam.co_license_phone = "";
          this.nicheParam.relationship_with_license = "";
        }
      } else {
        this.isCoLisense = false;
      }
    },
    listCoLisesen: function (val) {},
    // listRelationship: function(val) {
    //   this.nicheParam.information.map((item, key) => {
    //     item.relationship_to_applicant = val[0];
    //   });
    // },
    data: function (val) {
      this.nicheParam.id = val.id;
      this.nicheParam.booking_no = val.booking_no;
      this.nicheParam.booking_id = val.booking.id;
      this.nicheParam.service_id = val.service_id;
      this.nicheParam.booking_type_id = val.booking_type_id;
      this.nicheParam.user_id = val.client.id;
      this.nicheParam.application_date = val.application_date;
      this.nicheParam.reference_no = val.niche;
      this.nicheParam.interment_date = val.interment_date;
      this.nicheParam.location = val.niche.full_location;
      this.nicheParam.amount = val.niche.price;
      this.nicheParam.lease_start_date = val.lease_start_date;
      this.nicheParam.lease_expiry_date = val.lease_expiry_date;
      this.nicheParam.status = val.status;
      this.nicheParam.information = val.information;
      this.nicheParam.co_license = val.co_license;
      this.nicheParam.co_license_name = val.co_license_name;
      this.nicheParam.co_license_email = val.co_license_email;
      this.nicheParam.co_license_phone = val.co_license_phone;
      this.nicheParam.co_license_passport = val.co_license_passport;
      this.nicheParam.co_license_postal_code = val.co_license_postal_code;
      this.nicheParam.co_license_street_name = val.co_license_street_name;
      this.nicheParam.relationship_with_license = val.relationship_with_license;
      this.nicheParam.is_referral = val.referral;
      this.nicheParam.referral_name = val.referral_name;
      if (val.is_sale == 0) {
        this.is_sale = false;
      } else {
        this.is_sale = true;
      }
    },
    "extensionParam.duration": function (val) {
      if (val != null) {
        this.extensionParam.price = val.exten_price;
      } else {
        this.extensionParam.price = "";
      }
    },
    "nicheParam.is_referral": function (val) {
      val != null ? (val.reference_value_text == "Yes" ? (this.isReferral = true) : (this.isReferral = false)) : (this.isReferral = false);
    },
  },
};
</script>
