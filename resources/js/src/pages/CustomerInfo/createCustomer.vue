<template>
  <div class="admin-customer-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Client Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <!-- <div>
        <CustomerInfo/>
      </div> -->
      <b-container fluid="lg" class="customer-info-admin">
        <b-form>
          <b-row>
            <b-col cols="6">
              <b-form-group>
                <label class="_label_input">Full Name <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.customerParams.display_name.$error,
                  }"
                  v-model.trim="$v.customerParams.display_name.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.customerParams.display_name.required && $v.customerParams.display_name.$error">Field is required</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">NRIC / Passport No. <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.customerParams.passport.$error,
                  }"
                  v-model.trim="$v.customerParams.passport.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.customerParams.passport.required && $v.customerParams.passport.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Nationality <span class="_require">*</span></label>
                <multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :class="{
                    'form-group--error': $v.customerParams.nationality.$error,
                  }"
                  v-model.trim="$v.customerParams.nationality.$model"
                  :options="nationalityOption"
                  placeholder="Select one"
                ></multiselect>
                <!-- <b-form-input :class="{'form-group--error': $v.customerParams.nationality.$error }" v-model.trim="$v.customerParams.nationality.$model" class="input-form "></b-form-input> -->
                <div class="error" v-if="!$v.customerParams.nationality.required && $v.customerParams.nationality.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Mobile</label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.customerParams.phone.$error,
                  }"
                  v-model.trim="$v.customerParams.phone.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.customerParams.phone.maxLength && $v.customerParams.phone.$error">
                  The number phone must be
                  {{ $v.customerParams.phone.$params.maxLength.max }} number
                </div>
                <div class="error" v-else-if="!$v.customerParams.phone.decimal && $v.customerParams.phone.$error">Please enter the phone number</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Email</label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.customerParams.email.$error,
                  }"
                  v-model.trim="$v.customerParams.email.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.customerParams.email.email && $v.customerParams.email.$error">Please enter email</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="Alternative Contact No.">
                <b-form-input v-model="customerParams.alternative_contact_no" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Church Attended">
                <b-form-input v-model="customerParams.church_attended" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Religion <span class="_require">*</span></label>
                <multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :class="{
                    'form-group--error': $v.customerParams.religion_id.$error,
                  }"
                  v-model.trim="$v.customerParams.religion_id.$model"
                  :options="listTypeReligion"
                  placeholder="Select one"
                  track-by="id"
                  label="reference_value_text"
                ></multiselect>
                <div class="error" v-if="!$v.customerParams.religion_id.required && $v.customerParams.religion_id.$error">Field is required</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="Postal code">
                <b-form-input v-model="customerParams.postal_code" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group>
                <label class="_label_input">Address <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.customerParams.display_address.$error,
                  }"
                  v-model.trim="$v.customerParams.display_address.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.customerParams.display_address.required && $v.customerParams.display_address.$error">
                  Field is required
                </div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="How did you hear about us?">
                <multiselect
                  :show-labels="false"
                  deselect-label=""
                  v-model="customerParams.is_tgor"
                  :options="listTypeTGOR"
                  placeholder="Select one"
                  track-by="id"
                  label="reference_value_text"
                ></multiselect>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group label="Remarks">
                <b-form-input v-model="customerParams.remarks" class="input-form"> </b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
        </b-form>
      </b-container>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import CustomerInfo from "@/components/CustomerInfo";
import Calendar from "@/components/Icons/Calendar";
import Multiselect from "vue-multiselect";
import nationality from "../../nationality";
import _ from "lodash";

import { required, minLength, maxLength, between, decimal, email } from "vuelidate/lib/validators";
import { mapActions, mapState } from "vuex";

export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    CustomerInfo,
    Calendar,
    Multiselect,
  },
  metaInfo: {
    title: "Customer Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Customer Info Description",
      },
    ],
  },
  data() {
    return {
      tabIndex: 0,
      customerParams: {
        email: "",
        salutation: "",
        display_name: "",
        phone: "",
        passport: "",
        nationality: "",
        display_address: "",
        postal_code: "",
        alternative_contact_no: "",
        church_attended: "",
        religion_id: "",
        is_tgor: "",
        remarks: "",
      },
      nationalityOption: nationality,
      old_postal: "",
    };
  },
  validations: {
    customerParams: {
      email: {
        email,
      },
      salutation: {
        required,
      },
      display_name: {
        required,
      },
      phone: {
        decimal,
        maxLength: maxLength(12),
      },
      passport: {
        required,
      },
      nationality: {
        required,
      },
      religion_id: {
        required,
      },
      is_tgor: {
        required,
      },
      display_address: {
        required,
      },
    },
  },
  computed: mapState({
    listTypesSalutation: (state) => state.customer.listTypeSalutation,
    listTypeReligion: (state) => state.customer.listTypeReligion,
    listTypeTGOR: (state) => state.customer.listTypeTGOR,
  }),
  created() {
    this.getListTypeSalutation();
    this.getListTypeReligion();
    this.getListTypeTGOR();

    this.customerParams.nationality = this.nationalityOption[0];
  },
  methods: {
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return "";
      }
    },
    goBack() {
      // @click="goBack"
      this.$router.push("/admin-customer");
    },
    ...mapActions({
      getListTypeSalutation: "customer/getListTypeSalutation",
      getListTypeReligion: "customer/getListTypeReligion",
      getListTypeTGOR: "customer/getListTypeTGOR",
      createCustomer: "customer/createCustomer",
      findAdress: "address/findAdress",
    }),
    onSave() {
      this.$v.customerParams.$touch();
      if (this.$v.customerParams.$anyError) {
        return;
      } else {
        let prms = { ...this.customerParams };

        prms.salutation = prms.salutation.id;
        prms.religion_id = prms.religion_id.id;
        prms.is_tgor = prms.is_tgor.id;
        this.createCustomer(prms)
          .then((res) => {
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });
            this.$router.replace(`/customer-info/${res.data.data.id}`);
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
    listTypesSalutation: function (val) {
      this.customerParams.salutation = val[0];
    },
    listTypeReligion: function (val) {
      this.customerParams.religion_id = val[0];
    },
    listTypeTGOR: function (val) {
      this.customerParams.is_tgor = val[0];
    },
    "customerParams.postal_code": {
      deep: true,
      handler: _.debounce(function () {
        if (!!this.customerParams.postal_code) {
          let prms = { postal_code: this.customerParams.postal_code };
          this.findAdress(prms)
            .then((res) => {
              this.customerParams.display_address = res.data.data?.address;
            })
            .catch((error) => {
              this.customerParams.display_address = "";
            });
        }
      }, 200),
    },
  },
};
</script>
