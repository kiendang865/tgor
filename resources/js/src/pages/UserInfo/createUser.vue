<template>
  <div class="admin-customer-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            User Info
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
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Name <span class="_require">*</span></label>
                <b-form-input
                  autofocus
                  :class="{ 'form-group--error': $v.adminParams.name.$error }"
                  v-model.trim="$v.adminParams.name.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.adminParams.name.required && $v.adminParams.name.$error">Name is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Status <span class="_require">*</span></label>
                <multiselect
                  :class="{ 'form-group--error': $v.adminParams.status.$error }"
                  v-model.trim="$v.adminParams.status.$model"
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :options="listStatusAdmin"
                  placeholder="Select one"
                  track-by="id"
                  label="reference_value_text"
                ></multiselect>
                <div class="error" v-if="!$v.adminParams.status.required && $v.adminParams.status.$error">Status is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Email <span class="_require">*</span></label>
                <b-form-input
                  :class="{ 'form-group--error': $v.adminParams.email.$error }"
                  v-model.trim="$v.adminParams.email.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.adminParams.email.required && $v.adminParams.email.$error">Email is required</div>
                <div class="error" v-else-if="!$v.adminParams.email.email && $v.adminParams.email.$error">Please enter email</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Password <span class="_require">*</span></label>
                <b-form-input
                  :class="{ 'form-group--error': $v.adminParams.password.$error }"
                  v-model.trim="$v.adminParams.password.$model"
                  type="password"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.adminParams.password.required && $v.adminParams.password.$error">Password is required</div>
                <div class="error" v-if="!$v.adminParams.password.minLength && $v.adminParams.password.$error">
                  Password must have at least {{ $v.adminParams.password.$params.minLength.min }} letters.
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Confirm Password <span class="_require">*</span></label>
                <b-form-input
                  :class="{ 'form-group--error': $v.adminParams.confirm_password.$error }"
                  v-model.trim="$v.adminParams.confirm_password.$model"
                  type="password"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.adminParams.password.required && $v.adminParams.password.$error">Password is required</div>
                <div class="error" v-else-if="!$v.adminParams.confirm_password.sameAsPassword && $v.adminParams.confirm_password.$error">
                  Password must be identical.
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
import CustomerInfo from "@/components/CustomerInfo";
import Calendar from "@/components/Icons/Calendar";
import Multiselect from "vue-multiselect";
import nationality from "../../nationality";

import { required, minLength, maxLength, between, decimal, email, sameAs } from "vuelidate/lib/validators";
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
    title: "My Account Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "My Account Info Description",
      },
    ],
  },
  data() {
    return {
      adminParams: {
        name: "",
        email: "",
        password: "",
        confirm_password: "",
        status: "",
      },
    };
  },
  validations: {
    adminParams: {
      name: {
        required,
      },
      email: {
        required,
        email,
      },
      password: {
        required,
        minLength: minLength(6),
      },
      confirm_password: {
        required,
        sameAsPassword: sameAs("password"),
      },
      status: {
        required,
      },
    },
  },
  computed: mapState({
    listStatusAdmin: (state) => state.user.listStatusAdmin,
  }),
  created() {
    this.getListStatusAdmin();
  },
  methods: {
    goBack() {
      this.$router.push("/user-management");
    },
    ...mapActions({
      getListStatusAdmin: "user/getListStatusAdmin",
      createUser: "user/createUser",
    }),
    onSave() {
      this.$v.adminParams.$touch();
      if (this.$v.adminParams.$anyError) {
        return;
      } else {
        let prms = { ...this.adminParams };
        prms.status = prms.status.id;

        this.createUser(prms)
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
  },
  watch: {
    listStatusAdmin: function (val) {
      this.adminParams.status = val[0];
    },
  },
};
</script>
