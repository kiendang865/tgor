<template>
  <div class="admin-other-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title" @click="goBack">
          <span class="title-name">
            <ChevronLeft />
            Additional Services Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div>
        <b-container fluid="lg" class="other-info-admin">
          <b-row>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Service / Product <span class="_require">*</span></label>
                <b-form-input
                  autofocus
                  :class="{
                    'form-group--error': $v.otherParams.service_name.$error,
                  }"
                  v-model.trim="$v.otherParams.service_name.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.otherParams.service_name.required && $v.otherParams.service_name.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Sale / Rental <span class="_require">*</span></label>
                <multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :class="{ 'form-group--error': $v.otherParams.type.$error }"
                  v-model.trim="$v.otherParams.type.$model"
                  :options="listTypeOther"
                  placeholder="Select one"
                  track-by="id"
                  label="reference_value_text"
                ></multiselect>
                <div class="error" v-if="!$v.otherParams.type.required && $v.otherParams.type.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Contractor Required? <span class="_require">*</span></label>
                <multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :class="{
                    'form-group--error': $v.otherParams.is_contractor.$error,
                  }"
                  v-model.trim="$v.otherParams.is_contractor.$model"
                  :options="listContractorRequired"
                  placeholder="Select one"
                  track-by="id"
                  label="reference_value_text"
                ></multiselect>
                <div class="error" v-if="!$v.otherParams.is_contractor.required && $v.otherParams.is_contractor.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Status <span class="_require">*</span></label>
                <Multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  class="select-type-niche"
                  :class="{ 'form-group--error': $v.otherParams.status.$error }"
                  v-model.trim="$v.otherParams.status.$model"
                  :options="listOtherStatus"
                  track-by="id"
                  label="reference_value_text"
                  placeholder="Select a status"
                ></Multiselect>
                <div class="error" v-if="!$v.otherParams.status.required && $v.otherParams.status.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Category <span class="_require">*</span></label>
                <Multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  class="select-type-niche"
                  :class="{
                    'form-group--error': $v.otherParams.category.$error,
                  }"
                  v-model.trim="$v.otherParams.category.$model"
                  :options="listOtherCategory"
                  track-by="id"
                  label="reference_value_text"
                  placeholder="Select a category"
                ></Multiselect>
                <div class="error" v-if="!$v.otherParams.category.required && $v.otherParams.category.$error">Field is required</div>
              </b-form-group>
            </b-col>
          </b-row>
        </b-container>
      </div>
      <b-modal centered ref="other_modal" hide-footer id="extension" size="sm" title="Add Description">
        <b-container fluid="lg">
          <b-row>
            <b-col cols="12">
              <b-form-group label="Name">
                <b-form-input class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="12">
              <b-form-group label="Price (Excl. GST)">
                <masked-input
                  type="text"
                  :class="{ 'form-group--error': checkPrice }"
                  class="form-control"
                  :mask="numberAmountMask()"
                  :guide="true"
                  placeholder="$0.00"
                >
                </masked-input>
                <div class="error" v-if="checkPrice">Field is required</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="btn-submit">
            <b-col cols="12">
              <div class="submit">Submit</div>
            </b-col>
          </b-row>
        </b-container>
      </b-modal>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import OtherInfo from "@/components/OtherInfo";
import Calendar from "@/components/Icons/Calendar";
import Multiselect from "vue-multiselect";
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";

import { required, minLength, between } from "vuelidate/lib/validators";
import { mapActions, mapState } from "vuex";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
var accounting = require("accounting");

export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    OtherInfo,
    Multiselect,
    Calendar,
    MaskedInput,
    ControllTable,
    TableCustom,
  },
  metaInfo: {
    title: "Others Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Others Info Description",
      },
    ],
  },
  data() {
    return {
      tabIndex: 0,
      isLoading: false,
      otherParams: {
        service_name: "",
        type: "",
        is_contractor: "",
        status: "",
        category: null,
      },
      serviceType: {
        id: "",
        service_type_name: "",
        price: "",
      },
      contractorStatus: ["Yes", "No"],
      columnActive: {
        fields: [
          {
            key: "actions",
            label: "",
            thClass: "checkbox-column text-center",
            tdClass: "checkbox-column text-center",
            thStyle: "width: 50px",
            isActive: 1,
          },
          {
            key: "index",
            label: "#",
            isActive: 1,
            keySearch: "id",
            type: "text",
            isFilter: true,
          },
          {
            key: "name",
            label: "Service Type Name",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "phone",
            label: "Price",
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
      items: [
        {
          id: 2,
          index: "02",
          name: "AV Eqpt",
          phone: "$100.00",
        },
      ],
      checkPrice: false,
    };
  },
  validations: {
    otherParams: {
      service_name: {
        required,
      },
      type: {
        required,
      },
      is_contractor: {
        required,
      },
      status: {
        required,
      },
      category: {
        required,
      },
    },
  },
  created() {
    this.getListTypeOther();
    this.getListContractorRequired();
    this.getListOtherStatus();
    this.getListOtherCategory();
  },
  computed: {
    ...mapState({
      listTypeOther: (state) => state.other.listTypeOther,
      listContractorRequired: (state) => state.other.listContractorRequired,
      listOtherStatus: (state) => state.other.listOtherStatus,
      listOtherCategory: (state) => state.other.listOtherCategory,
    }),
  },
  methods: {
    ...mapActions({
      getListTypeOther: "other/getListTypeOther",
      createOther: "other/createOther",
      getListContractorRequired: "other/getListContractorRequired",
      getListOtherStatus: "other/getListOtherStatus",
      getListOtherCategory: "other/getListOtherCategory",
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return "";
      }
    },
    goBack() {
      this.$router.push("/admin-other");
    },
    onSave() {
      this.$v.otherParams.$touch();
      if (this.$v.otherParams.$anyError) {
        return;
      } else {
        let prms = { ...this.otherParams };

        prms.type = prms.type.id;
        prms.status = prms.status.id;
        prms.is_contractor = prms.is_contractor.id;
        prms.category_type = prms.category.id;

        this.createOther(prms)
          .then((res) => {
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });
            this.$router.replace(`/admin-other-info/${res.data.data.id}`);
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
    onCreateOther() {
      this.$refs.other_modal.show();
    },
    showModal(item) {
      this.$refs.other_modal.show();
    },
  },
  watch: {
    listTypeOther: function (val) {
      this.otherParams.type = val[0];
    },
    listContractorRequired: function (val) {
      this.otherParams.is_contractor = val[0];
    },
    listOtherStatus: function (val) {
      this.otherParams.status = val[0];
    },
  },
};
</script>
