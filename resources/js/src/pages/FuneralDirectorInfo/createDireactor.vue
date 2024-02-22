<template>
  <div class="admin-funeral-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Add Funeral Director
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div v-bind:class="{ 'run-loading': isLoading }">
        <b-container fluid="lg" class="funeral-info-admin">
          <b-row>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input"
                  >Company Name <span class="_require">*</span></label
                >
                <b-form-input
                  autofocus
                  :class="{
                    'form-group--error': $v.direactorParams.company_name.$error,
                  }"
                  v-model.trim="$v.direactorParams.company_name.$model"
                  class="input-form "
                ></b-form-input>
                <div
                  class="error"
                  v-if="
                    !$v.direactorParams.company_name.required &&
                      $v.direactorParams.company_name.$error
                  "
                >
                  Field is required
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Postal Code">
                <b-form-input
                  v-model.trim="$v.direactorParams.postal_code.$model"
                  :class="{
                    'form-group--error': $v.direactorParams.postal_code.$error,
                  }"
                  class="input-form "
                  maxlength="6"
                ></b-form-input>
                <div
                  class="error"
                  v-if="
                    !$v.direactorParams.postal_code.decimal &&
                      $v.direactorParams.postal_code.$error
                  "
                >
                  Please enter number
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group label="Address">
                <b-form-input
                  v-model="direactorParams.address"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="Company Main Tel">
                <b-form-input
                  v-model="direactorParams.company_main_tel"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Website">
                <b-form-input
                  v-model="direactorParams.website"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="UEN No.">
                <b-form-input
                  v-model="direactorParams.uen_no"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="Bank Name">
                <b-form-input
                  v-model="direactorParams.bank_name"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Account Number">
                <b-form-input
                  v-model="direactorParams.account_number"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="6">
              <b-form-group label="Remarks">
                <b-form-input
                  v-model="direactorParams.remarks"
                  class="input-form "
                ></b-form-input>
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
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";

import {
  required,
  minLength,
  maxLength,
  email,
  decimal,
  between,
} from "vuelidate/lib/validators";
import { validationMixin } from "vuelidate";
import { mapActions, mapState } from "vuex";
import _ from 'lodash';

export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    Calendar,
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
      direactorParams: {
        company_name: "",
        bank_name: "",
        account_number: "",
        address: "",
        website: "",
        postal_code: "",
        company_main_tel: "",
        uen_no: "",
        remarks: ""
      },
      isLoading: false
    };
  },
  validations: {
    direactorParams: {
      company_name: {
        required,
      },
      postal_code: {
        decimal
      }
    },
  },
  watch: {
    "direactorParams.postal_code": {
      deep: true,
        handler: _.debounce(function() {
        if(this.direactorParams.postal_code.toString().length == 6){
          if(!!this.direactorParams.postal_code && this.direactorParams.postal_code)
          {
            this.isLoading = true;
            let prms = {'postal_code': this.direactorParams.postal_code};
            this.findAdress(prms).then(res => { 
              this.isLoading = false;
              this.direactorParams.address = res.data.data?.address
            }).catch(error => {
              this.isLoading = false;
              this.direactorParams.address = '';
            })
          }
        }
        
      }, 200)
    }
  },
  methods: {
    ...mapActions({
      createDirector: "director/createDirector",
      findAdress: "address/findAdress"
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
      this.$router.push("admin-partners/#funeral");
    },
    onSave() {
      this.$v.direactorParams.$touch();
      if (this.$v.direactorParams.$anyError) {
        return;
      } else {
        let prms = { ...this.direactorParams };
        this.createDirector(prms)
          .then((res) => {
            this.$router.replace(`funeral-director-info/${res.data.data.id}`);
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
      this.$refs.other_modal.show();
    },
  },
};
</script>
