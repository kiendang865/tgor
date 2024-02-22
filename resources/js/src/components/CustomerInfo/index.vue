<template>
  <b-container fluid="lg" class="customer-info">
    <b-form @submit="onSubmit">
      <b-row>
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">Full Name <span class="_require">*</span></label>
            <b-form-input
              :disabled="true"
              :class="{
                'form-group--error': $v.customerParams.display_name.$error,
              }"
              v-model.trim="$v.customerParams.display_name.$model"
              class="input-form"
            ></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row class="mt">
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">NRIC / Passport No. <span class="_require">*</span></label>
            <b-form-input
              :disabled="true"
              :class="{
                'form-group--error': $v.customerParams.passport.$error,
              }"
              v-model.trim="$v.customerParams.passport.$model"
              class="input-form"
            ></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">Nationality <span class="_require">*</span></label>
            <b-form-input
              :disabled="true"
              :class="{
                'form-group--error': $v.customerParams.nationality.$error,
              }"
              v-model.trim="$v.customerParams.nationality.$model"
              class="input-form"
            ></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">Mobile <span class="_require">*</span></label>
            <b-form-input
              :disabled="true"
              :class="{ 'form-group--error': $v.customerParams.phone.$error }"
              v-model.trim="$v.customerParams.phone.$model"
              class="input-form"
            ></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">Email <span class="_require">*</span></label>
            <b-form-input
              :disabled="true"
              :class="{ 'form-group--error': $v.customerParams.email.$error }"
              v-model.trim="$v.customerParams.email.$model"
              class="input-form"
            ></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row class="mt">
        <b-col cols="3">
          <b-form-group label="Alternative Contact No.">
            <b-form-input :disabled="true" v-model="customerParams.alternative_contact_no" class="input-form"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Church Attended">
            <b-form-input :disabled="true" v-model="customerParams.church_attended" class="input-form"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group>
            <label class="_label_input">Religion <span class="_require">*</span></label>
            <multiselect
              :disabled="true"
              :show-labels="false"
              :allow-empty="false"
              deselect-label=""
              :class="{
                'form-group--error': $v.customerParams.religion_id.$error,
                'bg-gray': true,
              }"
              v-model.trim="$v.customerParams.religion_id.$model"
              :options="listTypeReligion"
              placeholder="Select one"
              track-by="id"
              label="reference_value_text"
            ></multiselect>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row class="mt">
        <b-col cols="3">
          <b-form-group label="Postal code">
            <b-form-input :disabled="true" v-model="customerParams.postal_code" class="input-form"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="6">
          <b-form-group>
            <label class="_label_input">Address <span class="_require">*</span></label>
            <b-form-input
              :disabled="true"
              :class="{
                'form-group--error': $v.customerParams.display_address.$error,
              }"
              v-model.trim="$v.customerParams.display_address.$model"
              class="input-form"
            ></b-form-input>
            <div class="error" v-if="!$v.customerParams.display_address.required && $v.customerParams.display_address.$error">Field is required</div>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row class="mt">
        <b-col cols="3">
          <b-form-group label="How did you hear about us?">
            <multiselect
              :disabled="true"
              :show-labels="false"
              deselect-label=""
              :class="{
                'form-group--error': $v.customerParams.is_tgor.$error,
                'bg-gray': true,
              }"
              v-model.trim="$v.customerParams.is_tgor.$model"
              :options="listTypeTGOR"
              placeholder="Select one"
              track-by="id"
              label="reference_value_text"
            ></multiselect>
          </b-form-group>
        </b-col>
        <b-col cols="6">
          <b-form-group label="Remarks">
            <b-form-input v-model="customerParams.remarks" class="input-form" :disabled="true"> </b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
    </b-form>
  </b-container>
</template>

<script>
import Multiselect from "vue-multiselect";
import { required, minLength, between } from "vuelidate/lib/validators";
import { mapActions, mapState } from "vuex";
import _ from "lodash";

export default {
  components: { Multiselect },
  props: {
    customerItem: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      state: {
        date1: null,
        date2: null,
      },
      isTrue: true,
      valueNiche: null,
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
        remarks: "",
        is_tgor: "",
      },
      old_postal: "",
    };
  },
  validations: {
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
      is_tgor: "",
    },
  },
  computed: mapState({
    listTypesSalutation: (state) => state.customer.listTypeSalutation,
    listTypeReligion: (state) => state.customer.listTypeReligion,
    listTypeContact: (state) => state.customer.listTypeContact,
    listTypeTGOR: (state) => state.customer.listTypeTGOR,
  }),
  created() {
    this.getListTypeSalutation();
    this.getListTypeReligion();
    this.getListTypeContact();
    this.getListTypeTGOR();
  },
  methods: {
    ...mapActions({
      getListTypeSalutation: "customer/getListTypeSalutation",
      getListTypeReligion: "customer/getListTypeReligion",
      getListTypeContact: "customer/getListTypeContact",
      getListTypeTGOR: "customer/getListTypeTGOR",
      findAdress: "address/findAdress",
    }),
    onSubmit() {
      this.$v.customerParams.$touch();
      if (this.$v.customerParams.$anyError) {
        return;
      } else {
        this.$emit("saveCustomer", this.customerParams);
      }
    },
  },
  watch: {
    customerItem: function (val) {
      this.customerParams = val;
      this.customerParams.religion_id = val.religion;
      this.old_postal = val.postal_code;
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
