<template>
  <div class="admin-contractor-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Add Contractor
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div v-bind:class="{ 'run-loading': isLoading }">
        <b-container fluid="lg" class="contractor-info-admin">
          <b-row>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Company Name <span class="_require">*</span></label>
                <b-form-input
                  autofocus
                  :class="{
                    'form-group--error': $v.contractorParams.company_name.$error,
                  }"
                  v-model.trim="$v.contractorParams.company_name.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contractorParams.company_name.required && $v.contractorParams.company_name.$error">
                  Field is required
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Postal Code">
                <b-form-input
                  v-model.trim="$v.contractorParams.postal_code.$model"
                  :class="{
                    'form-group--error': $v.contractorParams.postal_code.$error,
                  }"
                  class="input-form"
                  maxlength="6"
                ></b-form-input>
                <div class="error" v-if="!$v.contractorParams.postal_code.decimal && $v.contractorParams.postal_code.$error">Please enter number</div>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group label="Address">
                <b-form-input v-model="contractorParams.address" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="Company Main Tel">
                <b-form-input
                  v-model.trim="$v.contractorParams.company_main_tel.$model"
                  :class="{
                    'form-group--error': $v.contractorParams.company_main_tel.$error,
                  }"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contractorParams.company_main_tel.decimal && $v.contractorParams.company_main_tel.$error">
                  Please enter number
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Website">
                <b-form-input v-model="contractorParams.website" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="UEN No.">
                <b-form-input
                  v-model.trim="$v.contractorParams.uen_no.$model"
                  :class="{
                    'form-group--error': $v.contractorParams.uen_no.$error,
                  }"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contractorParams.uen_no.decimal && $v.contractorParams.uen_no.$error">Please enter number</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="Bank Name">
                <b-form-input v-model="contractorParams.bank_name" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Account Number">
                <b-form-input
                  v-model.trim="$v.contractorParams.account_number.$model"
                  :class="{
                    'form-group--error': $v.contractorParams.account_number.$error,
                  }"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contractorParams.account_number.decimal && $v.contractorParams.account_number.$error">
                  Please enter number
                </div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="6">
              <b-form-group label="Services">
                <multiselect
                  v-model="contractorParams.service_id"
                  :show-labels="false"
                  deselect-label=""
                  :options="otherByContractor"
                  placeholder="Select one"
                  :multiple="true"
                  :taggable="true"
                  @tag="addTag"
                  track-by="id"
                  label="service_name"
                ></multiselect>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group label="Remarks">
                <b-form-input v-model="contractorParams.remarks" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
        </b-container>
      </div>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import Calendar from "@/components/Icons/Calendar";
import Multiselect from "vue-multiselect";
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";

import { required, minLength, maxLength, email, decimal, between } from "vuelidate/lib/validators";
import { validationMixin } from "vuelidate";
import { mapActions, mapState } from "vuex";
import _ from "lodash";

export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    Calendar,
    Multiselect,
    ControllTable,
    TableCustom,
  },
  metaInfo: {
    title: "Contractor Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Contractor Info Description",
      },
    ],
  },
  data() {
    return {
      tabIndex: 0,
      isLoading: false,
      contractorParams: {
        company_name: "",
        bank_name: "",
        account_number: "",
        address: "",
        postal_code: "",
        website: "",
        service_id: "",
        company_main_tel: "",
        uen_no: "",
        remarks: "",
      },
      value: null,
    };
  },
  validations: {
    contractorParams: {
      company_name: {
        required,
      },
      account_number: {
        decimal,
      },
      postal_code: {
        decimal,
      },
      company_main_tel: {
        decimal,
      },
      uen_no: {
        decimal,
      },
    },
  },
  created() {
    this.getListOtherByContractRequired();
  },
  computed: mapState({
    listTypeBooking: (state) => state.contractor.listTypeBooking,
    otherByContractor: (state) => state.other.otherByContractor,
  }),

  methods: {
    ...mapActions({
      createContractor: "contractor/createContractor",
      getListTypeBooking: "contractor/getListTypeBooking",
      getListOtherByContractRequired: "other/getListOtherByContractRequired",
      findAdress: "address/findAdress",
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return "";
      }
    },
    goBack() {
      // @click="goBack"
      this.$router.push("admin-partners/#contractor");
    },
    onSave() {
      this.$v.contractorParams.$touch();
      if (this.$v.contractorParams.$anyError) {
        return;
      } else {
        let prms = { ...this.contractorParams };

        prms.service_id = [];
        if (this.contractorParams.service_id != "") {
          this.contractorParams.service_id.map((item, key) => {
            prms.service_id.push(item.id);
          });
        }

        this.createContractor(prms)
          .then((res) => {
            // this.$router.replace(`contractors-info/${res.data.data.id}`)
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
    onCreateContactPerson() {
      this.$refs.other_modal.show();
    },
    showModal(item) {
      // this.edit = true;
      this.$refs.other_modal.show();
    },
    addTag(newTag) {
      const tag = {
        name: newTag,
        code: newTag.substring(0, 2) + Math.floor(Math.random() * 10000000),
      };
      this.options.push(tag);
      this.contractorParams.service_id.push(tag);
    },
  },
  watch: {
    listTypeBooking: function (val) {
      this.contractorParams.service_id = val[0];
    },
    "contractorParams.postal_code": {
      deep: true,
      handler: _.debounce(function () {
        if (this.contractorParams.postal_code.toString().length == 6) {
          if (!!this.contractorParams.postal_code) {
            this.isLoading = true;
            let prms = { postal_code: this.contractorParams.postal_code };
            this.findAdress(prms)
              .then((res) => {
                this.isLoading = false;
                this.contractorParams.address = res.data.data?.address;
              })
              .catch((error) => {
                this.isLoading = false;
                this.contractorParams.address = "";
              });
          }
        }
      }, 200),
    },
  },
};
</script>
