<template>
  <div class="admin-customer-basic-info">
    <b-container fluid="lg">
      <b-container fluid="lg" class="customer-info-admin">
        <b-form>
          <b-row>
            <b-col cols="3">
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
            <!-- <b-col cols="3">
              <b-form-group label="Last Name">
                <b-form-input :class="{'form-group--error': $v.customerParams.last_name.$error }" v-model.trim="$v.customerParams.last_name.$model" class="input-form "></b-form-input>
                <div class="error" v-if="!$v.customerParams.last_name.required && $v.customerParams.last_name.$error">Field is required</div>
              </b-form-group>
            </b-col> -->
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
                <label class="_label_input">Mobile <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.customerParams.phone.$error,
                  }"
                  v-model.trim="$v.customerParams.phone.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.customerParams.phone.required && $v.customerParams.phone.$error">Field is required</div>
                <div class="error" v-else-if="!$v.customerParams.phone.decimal && $v.customerParams.phone.$error">Please enter the phone number</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Email <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.customerParams.email.$error,
                  }"
                  v-model.trim="$v.customerParams.email.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.customerParams.email.required && $v.customerParams.email.$error">Field is required</div>
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
                <b-form-input v-model="customerParams.postal_code" class="input-form" maxlength="6"></b-form-input>
                <div class="error" v-if="!$v.customerParams.postal_code.decimal && $v.customerParams.postal_code.$error">Please enter number</div>
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
              <b-form-group label="Preferred Mode of Contact">
                <multiselect
                  :show-labels="false"
                  deselect-label=""
                  v-model="customerParams.preferred_contact_by_id"
                  :options="listTypeContact"
                  placeholder="Select one"
                  track-by="id"
                  label="reference_value_text"
                ></multiselect>
              </b-form-group>
            </b-col>
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
          </b-row>
        </b-form>
      </b-container>
    </b-container>
    <b-modal centered hide-footer ref="donation" size="sm" title="Donation">
      <b-container fluid="lg">
        <b-row>
          <b-col cols="12">
            <!-- <b-form-group label="Amount"> -->
            <!-- <b-form-input class="input-form" :class="{'form-group--error': $v.donateParams.amount.$error }" v-model.trim="$v.donateParams.amount.$model" placeholder="$00.00"></b-form-input> -->
            <b-form-group label="Amount">
              <masked-input
                type="text"
                :class="{ 'form-group--error': checkDonateAmount }"
                v-model.trim="$v.donateParams.amount.$model"
                class="form-control"
                :mask="numberAmountMask()"
                :guide="true"
                placeholder="$0.00"
              >
              </masked-input>
              <!-- <div class="error" v-if="!$v.donateParams.amount.required && $v.donateParams.amount.$error">Field is required</div> -->
              <!-- <div class="error" v-else-if="!$v.donateParams.amount.decimal && $v.donateParams.amount.$error">Please enter number</div> -->
            </b-form-group>
            <div class="error" v-if="checkDonateAmount">Field is required</div>
            <!-- <div class="error" v-else-if="!$v.donateParams.amount.decimal && $v.donateParams.amount.$error">Please enter number</div> -->
            <!-- </b-form-group> -->
          </b-col>
          <b-col cols="12" class="mt">
            <b-form-group label="Remarks">
              <b-form-input
                class="input-form"
                :class="{ 'form-group--error': $v.donateParams.remarks.$error }"
                v-model.trim="$v.donateParams.remarks.$model"
              ></b-form-input>
              <div class="error" v-if="!$v.donateParams.remarks.required && $v.donateParams.remarks.$error">Field is required</div>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row class="btn-submit">
          <b-col cols="12">
            <div class="submit" @click="onDonate">Submit</div>
          </b-col>
        </b-row>
      </b-container>
    </b-modal>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import CustomerInfo from "@/components/CustomerInfo";
import Calendar from "@/components/Icons/Calendar";
import Multiselect from "vue-multiselect";
import nationality from "../../nationality";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
import moment from "moment";

import { required, minLength, maxLength, between, decimal, email } from "vuelidate/lib/validators";
import { mapActions, mapState } from "vuex";
import MaskedInput from "vue-text-mask";
var accounting = require("accounting");
import _ from "lodash";

export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    CustomerInfo,
    Calendar,
    Multiselect,
    MaskedInput,
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
      donateParams: {
        user_id: this.$router.history.current.params.id,
        amount: "",
        remarks: "",
      },
      customerParams: {
        email: "",
        salutation: "",
        display_name: "",
        phone: "",
        passport: "",
        nationality: "",
        street_no: "",
        display_address: "",
        building_name: "",
        unit_no: "",
        postal_code: "",
        alternative_contact_no: "",
        church_attended: "",
        religion_id: "",
        preferred_contact_by_id: "",
        is_tgor: "",
      },
      nationalityOption: nationality,
      checkDonateAmount: false,
      old_postal: "",
    };
  },
  validations: {
    donateParams: {
      amount: {
        // required,
        // decimal
      },
      remarks: {
        required,
      },
    },
    customerParams: {
      email: {
        required,
        email,
      },
      display_name: {
        required,
      },
      phone: {
        required,
        decimal,
      },
      passport: {
        required,
      },
      nationality: {
        required,
      },
      display_address: {
        required,
      },
      postal_code: {
        decimal,
      },
      religion_id: {
        required,
      },
    },
  },
  computed: mapState({
    listTypesSalutation: (state) => state.customer.listTypeSalutation,
    listTypeReligion: (state) => state.customer.listTypeReligion,
    listTypeTGOR: (state) => state.customer.listTypeTGOR,
    listTypeContact(state) {
      let array = state.customer.listTypeContact;
      if (array.length == 4) {
        let temp = array[0];
        array[0] = array[3];
        array[3] = temp;
      }
      return array;
    },
  }),
  created() {
    let idCustomer = this.$router.history.current.params.id;
    // get detail customer
    this.getCustomerDetail(idCustomer);
    // get list type
    this.getListTypeSalutation();
    this.getListTypeReligion();
    this.getListTypeContact();
    this.getListTypeTGOR();
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
      this.$router.push("/admin-customer");
    },
    ...mapActions({
      getListTypeSalutation: "customer/getListTypeSalutation",
      getListTypeReligion: "customer/getListTypeReligion",
      getListTypeContact: "customer/getListTypeContact",
      getListTypeTGOR: "customer/getListTypeTGOR",
      customerDetail: "customer/customerDetail",
      updateCustomer: "customer/updateCustomer",
      userDonate: "user/userDonate",
      findAdress: "address/findAdress",
    }),
    onSave() {
      this.$v.customerParams.$touch();
      if (this.$v.customerParams.$anyError) {
        this.$swal({
          title: "Warning!",
          text: "Some field are blank. Please fill them up",
          icon: "warning",
        });
        return;
      } else {
        let prms = { ...this.customerParams };
        if (prms.salutation) {
          prms.salutation = prms.salutation.id;
        }
        prms.religion_id = prms.religion_id.id;
        if (prms.is_tgor) {
          prms.is_tgor = prms.is_tgor.id;
        }
        if (prms.preferred_contact_by_id) {
          prms.preferred_contact_by_id = prms.preferred_contact_by_id.id;
        }

        this.updateCustomer(prms)
          .then((res) => {
            this.$swal({
              icon: "success",
              title: "Notifcation",
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
    getCustomerDetail(idCustomer) {
      this.customerDetail(idCustomer).then((res) => {
        this.customerParams = res;
        this.customerParams.religion_id = res.religion;
        this.customerParams.preferred_contact_by_id = res.preferred_contact_by;
        this.old_postal = res.postal_code;
      });
    },
    showModal() {
      this.$v.donateParams.$reset();
      this.donateParams.amount = "";
      this.donateParams.remarks = "";
      this.$refs.donation.show();
      this.checkDonateAmount = false;
    },
    onDonate() {
      this.$v.donateParams.$touch();
      if (this.$v.donateParams.$anyError) {
        if (this.donateParams.amount == "") {
          this.checkDonateAmount = true;
        }
        return;
      }
      let prms = { ...this.donateParams };
      prms.amount = this.formatMoney(prms.amount);

      this.userDonate(prms).then((res) => {
        this.$refs.donation.hide();
        this.$swal({
          icon: "success",
          title: "Notifcation",
          text: "Donation Successfully.",
        });
        this.$router.push({
          name: "DonateInfo",
          params: { id: res.data.data.id },
        });
      });
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
  },
  watch: {
    "donateParams.amount": function (val) {
      if (val != "") {
        this.checkDonateAmount = false;
      } else {
        this.checkDonateAmount = true;
      }
    },
    "customerParams.postal_code": {
      deep: true,
      handler: _.debounce(function () {
        if (!!this.customerParams.postal_code && this.customerParams.postal_code !== this.old_postal) {
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
