<template>
  <b-container fluid="lg" class="booking-new">
    <b-row v-bind:class="{ 'run-loading': isLoadingMain }">
      <b-col cols="12">
        <div class="title-input">
          Search Client and NRIC No.
        </div>
      </b-col>
      <b-col cols="3">
        <multiselect
          :disabled="newCustomer"
          :class="{ 'bg-white': !newCustomer, 'bg-gray': newCustomer }"
          :show-labels="false"
          deselect-label=""
          v-model="valueCustomer"
          :options="listCustomer.data"
          placeholder="Select user"
          label="display_name"
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
          :internal-search="false"
        ></multiselect>
      </b-col>
      <b-col cols="8">
        <b-form-checkbox
          :disabled="disableForm"
          name="check-button"
          v-model="newCustomer"
        >
          New Client
        </b-form-checkbox>
      </b-col>
    </b-row>
    <div class="line-horizontal"></div>
    <b-row>
      <b-col cols="6">
        <b-form-group>
          <label class="_label_input"
            >Full Name <span class="_require">*</span></label
          >
          <b-form-input
            :class="{
              'form-group--error': $v.customerParams.display_name.$error,
            }"
            v-model.trim="$v.customerParams.display_name.$model"
            :disabled="!newCustomer || disableForm"
            class="input-form"
          ></b-form-input>
          <div
            class="error"
            v-if="
              !$v.customerParams.display_name.required &&
                $v.customerParams.display_name.$error
            "
          >
            Field is required
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input"
            >NRIC / Passport No. <span class="_require">*</span></label
          >
          <b-form-input
            :class="{ 'form-group--error': $v.customerParams.passport.$error }"
            v-model.trim="$v.customerParams.passport.$model"
            :disabled="!newCustomer || disableForm"
            class="input-form"
          ></b-form-input>
          <div
            class="error"
            v-if="
              !$v.customerParams.passport.required &&
                $v.customerParams.passport.$error
            "
          >
            Field is required
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input"
            >Nationality <span class="_require">*</span></label
          >
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :disabled="!newCustomer || disableForm"
            :class="{
              'form-group--error': $v.customerParams.nationality.$error,
              'bg-white': newCustomer,
              'bg-gray': !newCustomer,
            }"
            v-model.trim="$v.customerParams.nationality.$model"
            :options="nationalityOption"
            placeholder="Select one"
          ></multiselect>
          <!-- <b-form-input :class="{'form-group--error': $v.customerParams.nationality.$error }" v-model.trim="$v.customerParams.nationality.$model" :disabled="!newCustomer" class="input-form" ></b-form-input> -->
          <div
            class="error"
            v-if="
              !$v.customerParams.nationality.required &&
                $v.customerParams.nationality.$error
            "
          >
            Field is required
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input"
            >Mobile</label
          >
          <b-form-input
            :class="{ 'form-group--error': $v.customerParams.phone.$error }"
            v-model.trim="$v.customerParams.phone.$model"
            :disabled="!newCustomer || disableForm"
            class="input-form"
          ></b-form-input>
          <div
            class="error"
            v-if="
              !$v.customerParams.phone.decimal && $v.customerParams.phone.$error
            "
          >
            Please enter number phone
          </div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input"
            >Email</label
          >
          <b-form-input
            :class="{ 'form-group--error': $v.customerParams.email.$error }"
            v-model.trim="$v.customerParams.email.$model"
            :disabled="!newCustomer || disableForm"
            class="input-form"
          ></b-form-input>
          <div
            class="error"
            v-if="
              !$v.customerParams.email.email && $v.customerParams.email.$error
            "
          >
            Please enter email
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Alternative Contact No.">
          <b-form-input
            v-model="customerParams.alternative_contact_no"
            :disabled="!newCustomer || disableForm"
            class="input-form"
          ></b-form-input>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Church Attended">
          <b-form-input
            v-model="customerParams.church_attended"
            :disabled="!newCustomer || disableForm"
            class="input-form"
          ></b-form-input>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input"
            >Religion <span class="_require">*</span></label
          >
          <multiselect
            :class="{
              'form-group--error': $v.customerParams.religion_id.$error,
              'bg-gray': !newCustomer || disableForm,
            }"
            v-model.trim="$v.customerParams.religion_id.$model"
            :disabled="!newCustomer || disableForm"
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :options="listTypeReligion"
            placeholder="Select one"
            track-by="id"
            label="reference_value_text"
          ></multiselect>
          <div
            class="error"
            v-if="
              !$v.customerParams.religion_id.required &&
                $v.customerParams.religion_id.$error
            "
          >
            Field is required
          </div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="Postal code">
          <b-form-input
            v-model="customerParams.postal_code"
            :disabled="!newCustomer || disableForm"
            class="input-form"
            maxlength="6"
          ></b-form-input>
        </b-form-group>
      </b-col>
      <b-col cols="6">
        <b-form-group>
          <label class="_label_input"
            >Address <span class="_require">*</span></label
          >
          <b-form-input
            :class="{
              'form-group--error': $v.customerParams.display_address.$error,
            }"
            v-model.trim="$v.customerParams.display_address.$model"
            :disabled="!newCustomer || disableForm"
            class="input-form"
          ></b-form-input>
          <div
            class="error"
            v-if="
              !$v.customerParams.display_address.required &&
                $v.customerParams.display_address.$error
            "
          >
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
            :disabled="!newCustomer || disableForm"
          ></multiselect>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="How did you hear about us?">
          <multiselect
            class="newCustomer"
            v-model="customerParams.is_tgor"
            :show-labels="false"
            deselect-label=""
            :options="listTypeTGOR"
            placeholder="Select one"
            track-by="id"
            label="reference_value_text"
            :disabled="!newCustomer || disableForm"
          ></multiselect>
        </b-form-group>
      </b-col>
      <b-col cols="6">
        <b-form-group label="Remarks">
          <b-form-input
            v-model="customerParams.remarks"
            :disabled="!newCustomer || disableForm"
            class="input-form"
          ></b-form-input>
        </b-form-group>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import Multiselect from "vue-multiselect";
import { mapActions, mapState } from "vuex";
import {
  required,
  minLength,
  between,
  decimal,
  email,
} from "vuelidate/lib/validators";
import { EventBus } from "../../event-bus";
import nationality from "../../nationality";
import _ from "lodash";

export default {
  components: {
    Multiselect,
  },
  props: {
    disableForm: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      customerParams: {
        id: "",
        email: "",
        salutation: "",
        display_name: "",
        // last_name: '',
        phone: "",
        passport: "",
        nationality: "",
        display_address: "",
        postal_code: null,
        alternative_contact_no: "",
        church_attended: "",
        religion_id: "",
        is_tgor: "",
        remarks: "",
        preferred_contact_by_id: null
      },
      nationalityOption: nationality,
      valueNiche: null,
      valueSalution: null,
      valueCustomer: null,
      valueNationality: null,
      valueReligion: null,
      valueContact: null,
      valueTgor: null,
      newCustomer: false,
      isLoading: false,
      old_postal: "",
      isLoadingMain: false,
    };
  },
  validations() {
    if (this.newCustomer) {
      return {
        customerParams: {
          email: {
            email,
          },
          display_name: {
            required,
          },
          phone: {
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
          religion_id: {
            required,
          },
        },
      };
    } else {
      return {
        customerParams: {
          email: "",
          salutation: "",
          display_name: "",
          phone: "",
          passport: "",
          nationality: "",
          alternative_contact_no: "",
          postal_code: "",
          display_address: "",
          church_attended: "",
          religion_id: "",
          is_tgor: "",
          remarks: "",
        },
      };
    }
  },
  created() {
    this.getListCustomer({ page: 1 });
    this.getListTypeSalutation();
    this.getListTypeReligion();
    this.getListTypeContact();
    this.getListTypeTGOR();

    this.customerParams.nationality = this.nationalityOption[0];

    EventBus.$on("save-customer-booking", this.saveCustomer);
  },
  computed: mapState({
    listCustomer: (state) => state.customer.listCustomer,
    listTypesSalutation: (state) => state.customer.listTypeSalutation,
    listTypeReligion: (state) => state.customer.listTypeReligion,
    listTypeTGOR: (state) => state.customer.listTypeTGOR,
    listTypeContact (state) {
      let array = state.customer.listTypeContact;
      if(array.length == 4){
        let temp = array[0];
        array[0] = array[3];
        array[3] = temp;
      }
      return array;
    }
  }),
  methods: {
    ...mapActions({
      getListCustomer: "customer/getListCustomer",
      getListTypeSalutation: "customer/getListTypeSalutation",
      getListTypeReligion: "customer/getListTypeReligion",
      getListTypeContact: "customer/getListTypeContact",
      getListTypeTGOR: "customer/getListTypeTGOR",
      createCustomer: "customer/createCustomer",
      findAdress: "address/findAdress",
    }),
    saveCustomer() {
      let prms = { ...this.customerParams };
      if (prms.id == "") {
        prms.religion_id = prms.religion_id.id;
        prms.is_tgor = prms.is_tgor.id;
      }
      if(prms.preferred_contact_by_id){
        prms.preferred_contact_by_id = prms.preferred_contact_by_id.id;
      }
      this.$emit("userInfo", prms, this.$v.customerParams.$anyError);
      this.$v.customerParams.$touch();
      if (this.$v.customerParams.$anyError) {
        return;
      }
    },
    limitText(count) {
      return `and ${count} other countries`;
    },
    asyncFind(query) {
      this.isLoading = true;
      let prms = {
        filter: {
          search_customer: query,
        },
      };
      this.getListCustomer(prms).then((response) => {
        this.isLoading = false;
      });
    },
  },
  watch: {
    valueCustomer: function(val) {
      if (Object.keys(val).length) {
        var replaceArray = [
          "Mr",
          "Mrs",
          "Ms",
          "Miss",
          "Master",
          "Mdm",
          "Dr",
          "Major",
          "Rev",
        ];
        let name = val.display_name;
        for (var i = replaceArray.length - 1; i >= 0; i--) {
          name = name.replace(
            RegExp(
              "\\b" +
                replaceArray[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&") +
                "\\b",
              "g"
            ),
            ""
          );
        }
        this.customerParams = val;
        this.customerParams.religion_id = val.religion;
        this.customerParams.display_name = val.salutation
          ? val.salutation.reference_value_text + " " + name.trim()
          : name.trim();
        this.old_postal = val.postal_code;
      }
    },
    newCustomer: function(val) {
      // console.log(val)
      this.$v.customerParams.$reset();

      this.customerParams = {
        id: "",
        email: "",
        salutation: "",
        display_name: "",
        phone: "",
        passport: "",
        nationality: this.nationalityOption[0],
        display_address: "",
        postal_code: "",
        alternative_contact_no: "",
        church_attended: "",
        religion_id: this.listTypeReligion[0],
        is_tgor: "",
        remarks: "",
        preferred_contact_by_id: null
      };
      this.valueCustomer = "";
      this.$emit("checkNewCustomer", val);
    },
    nationalityOption: function(val) {
      this.customerParams.nationality = val[0];
    },
    // listTypesSalutation: function(val) {
    //   this.customerParams.salutation = val[0];
    // },
    listTypeReligion: function(val) {
      this.customerParams.religion_id = val[0];
    },
    // "customerParams.postal_code": function(val) {
    //   if(val !== '')
    //   {
    //     let prms = {'postal_code': val};
    //     this.findAdress(prms).then(res => {
    //       this.customerParams.display_address = res.data.data?.address
    //     })
    //   }
    // }
    "customerParams.postal_code": {
      deep: true,
      handler: _.debounce(function() {
        if (this.customerParams.postal_code) {
          if (this.customerParams.postal_code.toString().length == 6) {
            if (
              !!this.customerParams.postal_code &&
              this.customerParams.postal_code !== this.old_postal
            ) {
              this.isLoadingMain = true;
              let prms = { postal_code: this.customerParams.postal_code };
              this.findAdress(prms)
                .then((res) => {
                  this.isLoadingMain = false;
                  this.customerParams.display_address = res.data.data?.address;
                })
                .catch((error) => {
                  this.isLoadingMain = false;
                  this.customerParams.display_address = "";
                });
            }
          }
        }
      }, 200),
    },
  },
};
</script>
