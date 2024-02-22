<template>
  <div class="admin-customer-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            {{ titleNicheReserved }}
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-delete" v-if="$route.fullPath != '/new-niches-reserved'" @click="onDelete">Delete Reserved</b-button>
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <b-container fluid="lg" class="customer-info-admin">
        <b-form>
          <b-row>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Reserved Date <span class="_require">*</span></label>
                <div class="position-relative input-date">
                  <Datepicker
                    v-model="$v.nicheReserved.reserved_date.$model"
                    :class="{ 'form-group--error': $v.nicheReserved.reserved_date.$error }"
                    :format="customFormatter"
                    class="choose-date"
                    placeholder="dd/mm/yyyy"
                  >
                  </Datepicker>
                  <Calendar />
                </div>
                <div class="error" v-if="!$v.nicheReserved.reserved_date.required && $v.nicheReserved.reserved_date.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group class="list-niches">
                <label class="_label_input">Niche Reference No. <span class="_require">*</span></label>
                <!-- <multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :options="nicheReference"
                  v-model="nicheReserved.reference_no"
                  placeholder="Select one"
                  track-by="id"
                ></multiselect> -->
                <multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  open-direction="bottom"
                  :class="{ 'form-group--error': $v.nicheReserved.niche_id.$error }"
                  v-model.trim="$v.nicheReserved.niche_id.$model"
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
                <div class="error" v-if="!$v.nicheReserved.niche_id.required && $v.nicheReserved.niche_id.$error">Field is required</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="6">
              <b-form-group>
                <label class="_label_input">Customer Name <span class="_require">*</span></label>
                <b-form-input
                  class="input-form"
                  :class="{ 'form-group--error': $v.nicheReserved.customer_name.$error }"
                  v-model.trim="$v.nicheReserved.customer_name.$model"
                ></b-form-input>
                <div class="error" v-if="!$v.nicheReserved.customer_name.required && $v.nicheReserved.customer_name.$error">Field is required</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Mobile <span class="_require">*</span></label>
                <b-form-input
                  class="input-form"
                  :class="{ 'form-group--error': $v.nicheReserved.mobile.$error }"
                  v-model.trim="$v.nicheReserved.mobile.$model"
                ></b-form-input>
                <div class="error" v-if="!$v.nicheReserved.mobile.required && $v.nicheReserved.mobile.$error">Field is required</div>
                <div class="error" v-else-if="!$v.nicheReserved.mobile.decimal && $v.nicheReserved.mobile.$error">Please enter mobile phone</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Email <span class="_require">*</span></label>
                <b-form-input
                  :class="{ 'form-group--error': $v.nicheReserved.email.$error }"
                  v-model.trim="$v.nicheReserved.email.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.nicheReserved.email.required && $v.nicheReserved.email.$error">Field is required</div>
                <div class="error" v-else-if="!$v.nicheReserved.email.email && $v.nicheReserved.email.$error">Please enter email</div>
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
import { required, minLength, between, decimal, email } from "vuelidate/lib/validators";
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
      titleNicheReserved: "Add Niches (Reserved)",
      nicheReserved: {
        id: "",
        niche_id: "",
        reserved_date: "",
        customer_name: "",
        mobile: "",
        email: "",
      },
      nicheReference: [
        {
          name: "A01-001",
        },
        {
          name: "A01-002",
        },
      ],
    };
  },
  validations: {
    nicheReserved: {
      niche_id: {
        required,
      },
      reserved_date: {
        required,
      },
      customer_name: {
        required,
      },
      mobile: {
        required,
        decimal,
      },
      email: {
        required,
        email,
      },
    },
  },
  mouted() {
    this.$nextTick(() => {
      this.titleNicheReserved = this.$router.history.current.params.id !== undefined ? "Niches (Reserved)" : "Add Niches (Reserved)";
    });
  },
  created() {
    this.getListNichForBooking();
    if (!this.nicheReserved.reserved_date) {
      this.nicheReserved.reserved_date = moment().format("MM/DD/YYYY");
    }
    let idNicheReserved = this.$router.history.current.params.id;
    if (idNicheReserved && _.isInteger(+idNicheReserved)) {
      //update niches
      this.getNicheReservedDetail({
        id: idNicheReserved,
      })
        .then((response) => {
          //do something
          this.nicheReserved = response.data.data;
          this.nicheReserved.niche_id = response.data.data.niche;
          this.titleNicheReserved = "Niches (Reserved)";
        })
        .catch((error) => {
          this.$router.replace("/reserved-niches");
          this.titleNicheReserved = "Add Niches (Reserved)";
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    } else {
      //is create niche
      // this.form.status = this.statusNiches[0]
      this.$nextTick(() => {
        this.titleNicheReserved = "Add Niches (Reserved)";
      });
    }
  },
  computed: mapState({
    listNicheForBooking: (state) => state.niche.listNicheForBooking,
  }),
  methods: {
    ...mapActions({
      getListNichForBooking: "niche/getListNichForBooking",
      createNicheReserved: "nichereserved/createNicheReserved",
      updateNicheReserved: "nichereserved/updateNicheReserved",
      getNicheReservedDetail: "nichereserved/getNicheReservedDetail",
      deleteNicheReserved: "nichereserved/deleteNicheReserved",
    }),
    goBack() {
      this.$router.push({ name: "NicheReserved" });
    },
    onSave() {
      this.$v.nicheReserved.$touch();
      if (this.$v.nicheReserved.$anyError) {
        return;
      }
      let action = "createNicheReserved";
      if (this.nicheReserved.id) {
        action = "updateNicheReserved";
      }
      this.isLoading = true;

      let prms = { ...this.nicheReserved };

      prms.niche_id = this.nicheReserved.niche_id.id;

      this[action](prms)
        .then((response) => {
          this.isLoading = false;
          this.$swal({
            icon: "success",
            title: "Notifcation",
            text: response.data.status,
          });
          if (action === "createNicheReserved") {
            this.nicheReserved.id = response.data.data.id;
            this.$router.replace(`reserved-niches/${response.data.data.id}`);
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
    onDelete() {
      this.$swal({
        title: "Permanently delete?",
        text: "This action is irreversible.",
        icon: "warning",
        customClass: {
          container: "swal-del-item",
        },
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
      }).then((result) => {
        if (result.value) {
          this.isLoading = true;
          let ids = [];
          let idNicheReserved = this.$router.history.current.params.id;
          ids.push(idNicheReserved);
          let prms = { ids: ids };
          this.deleteNicheReserved(prms)
            .then((res) => {
              this.$swal({
                icon: "success",
                title: "Success!",
                text: res.data.status,
              });
              this.$router.push(`/reserved-niches`);
              // this.$refs.nichesReserved.reloadData();
              this.isLoading = false;
            })
            .catch((error) => {
              this.isLoading = false;
              this.$swal({
                icon: "error",
                title: "Oops...",
                text: error.response.data.errors,
              });
            });
        }
      });
    },
  },
};
</script>
