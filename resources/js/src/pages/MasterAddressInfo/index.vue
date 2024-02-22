<template>
  <div class="admin-customer-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            {{titleAddress}}
          </span>
        </div>
        <div class="wrapper-btn">
          <!-- <b-button class="btn-delete" v-if="$route.fullPath != '/new-niches-reserved'" @click="onDelete">Delete Reserved</b-button> -->
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <b-container fluid="lg" class="customer-info-admin">
        <b-form>
          <b-row>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input"
                  >Postal Code
                  <span class="_require">*</span></label
                >
                <div class="position-relative input-date">
                 <b-form-input
                  class="input-form"
                  :class="{ 'form-group--error': $v.addressParams.postal_code.$error }"
                  v-model.trim="$v.addressParams.postal_code.$model"
                  maxlength="6"
                ></b-form-input>
                </div>
                <div class="error"
                  v-if="!$v.addressParams.postal_code.required && $v.addressParams.postal_code.$error"
                >
                  Field is required
                </div>
                <div
                  class="error"
                  v-if="
                    !$v.addressParams.postal_code.decimal &&
                      $v.addressParams.postal_code.$error
                  "
                >
                  Please enter number
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group>
                <label class="_label_input"
                  >Address
                  <span class="_require">*</span></label
                >
                <div class="position-relative input-date">
                <b-form-input
                  class="input-form"
                  :class="{ 'form-group--error': $v.addressParams.address.$error }"
                  v-model.trim="$v.addressParams.address.$model"
                ></b-form-input>
                </div>
                <div class="error"
                  v-if="!$v.addressParams.address.required && $v.addressParams.address.$error"
                >
                  Field is required
                </div>
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
import Datepicker from "vuejs-datepicker";
import Calendar from "@/components/Icons/Calendar";
import moment from "moment";
import Multiselect from "vue-multiselect";
import {
  required,
  minLength,
  between,
  decimal,
  email
} from 'vuelidate/lib/validators';
import { mapActions, mapState } from "vuex";
export default {
  components: {
    ChevronLeft,
    Datepicker,
    Calendar,
    Multiselect,
  },
  data() {
    return {
      isLoading: false,
      titleAddress: 'Add Address',
      addressParams: {
        id:"",
        postal_code:"",
        address: "",
      },
    };
  },
  validations: {
      addressParams:{
        postal_code:{
            required,
            decimal
        },
        address: {
            required,
        },
    },
  },
  mouted() {
    this.$nextTick(() => {
      this.titleAddress = this.$router.history.current.params.id !== undefined ? 'Address Info' : 'Add Address';
    });
  },
  created() {
    let idAddress = this.$router.history.current.params.id
    if (idAddress && _.isInteger(+idAddress)) { 
        //update niches
        this.getAddressDetail({
            id: idAddress
        }).then((response) => {
            //do something  clg
            this.addressParams = response.data.data;
            this.titleAddress = 'Address Info';
        }).catch((error) => {
            this.$router.replace('/master-address')
            this.titleAddress = 'Add Address';
            this.$swal({
                icon: 'error',
                title: 'Oops...',
                text: error.response.data.errors,
            });
        })
    } else { //is create niche
        // this.form.status = this.statusNiches[0] 
         this.$nextTick(() => {
          this.titleAddress = 'Add Address';
         });
    }
        
  },
  computed: mapState({
    // listNicheForBooking: (state) => state.niche.listNicheForBooking,
  }),
  methods: {
    ...mapActions({
      createAddress: "address/createAddress",
      updateAddress: "address/updateAddress",
      getAddressDetail: "address/getAddressDetail",
    }),
    goBack() {
      this.$router.push({ name: "MasterAddess" });
    },
    onSave() {
      this.$v.addressParams.$touch();
      if (this.$v.addressParams.$anyError) {
        return;
      }
      let action = "createAddress";
      if (this.addressParams.id) {
        action = "updateAddress";
      }
      this.isLoading = true;

      let prms = { ...this.addressParams };

      this[action](prms)
        .then((response) => {
          this.isLoading = false;
          this.$swal({
            icon: "success",
            title: "Notifcation",
            text: response.data.status,
          });
          if (action === "createAddress") {
            this.addressParams.id = response.data.data.id;
            this.$router.replace(`master-address/${response.data.data.id}`);
          }
        })
        .catch((error) => {
          this.isLoading = false;
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
  },
};
</script>
