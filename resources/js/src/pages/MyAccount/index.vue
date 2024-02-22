<template>
  <div class="admin-customer-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name">
            <!-- <ChevronLeft /> -->
            My Account Info
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
              <b-form-group label="Name">
                <b-form-input
                  :class="{ 'form-group--error': $v.adminParams.name.$error }"
                  v-model.trim="$v.adminParams.name.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.adminParams.name.required && $v.adminParams.name.$error">Name is required</div>
                <div class="error" v-else-if="!$v.adminParams.name.maxLength && $v.adminParams.name.$error">
                  Name must have at most {{ $v.adminParams.name.$params.maxLength.max }} letters.
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Status">
                <multiselect
                  :class="{ 'form-group--error': $v.adminParams.status.$error }"
                  v-model.trim="$v.adminParams.status.$model"
                  :show-labels="false"
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
              <b-form-group label="Email">
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
              <b-form-group label="Password">
                <b-form-input
                  :class="{ 'form-group--error': $v.adminParams.password.$error }"
                  v-model.trim="$v.adminParams.password.$model"
                  type="password"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.adminParams.password.minLength && $v.adminParams.password.$error">
                  Password must have at least {{ $v.adminParams.password.$params.minLength.min }} letters.
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Confirm Password">
                <b-form-input
                  :class="{ 'form-group--error': $v.adminParams.confirm_password.$error }"
                  v-model.trim="$v.adminParams.confirm_password.$model"
                  type="password"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.adminParams.confirm_password.sameAsPassword && $v.adminParams.confirm_password.$error">
                  Passwords must be identical.
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
import { EventBus } from "../../event-bus";
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
      admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
      adminParams: {
        id: "",
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
        maxLength: maxLength(15),
      },
      email: {
        required,
        email,
      },
      password: {
        minLength: minLength(6),
      },
      confirm_password: {
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
    let idUser = this.admin_profile.id;

    this.getUserDetail(idUser);

    this.getListStatusAdmin();
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
      this.$router.push("/user-management");
    },
    ...mapActions({
      getListStatusAdmin: "user/getListStatusAdmin",
      userDetail: "user/userDetail",
      updateUser: "user/updateUser",
      getMe: "user/getMe",
    }),
    onSave() {
      this.$v.adminParams.$touch();
      if (this.$v.adminParams.$anyError) {
        return;
      } else {
        let prms = { ...this.adminParams };

        prms.status = prms.status.id;

        this.updateUser(prms)
          .then((res) => {
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });
            // this.getMe();
            EventBus.$emit("getMe");
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
    getUserDetail(idUser) {
      this.userDetail(idUser).then((res) => {
        this.adminParams.status = res.admin_status;
        this.adminParams.name = res.display_name;
        this.adminParams.email = res.email;
        this.adminParams.id = res.id;
      });
    },
  },
};
</script>
