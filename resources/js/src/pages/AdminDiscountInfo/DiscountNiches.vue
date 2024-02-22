<template>
  <b-container fluid="lg">
    <b-row class="mt">
      <b-col cols="6">
        <b-form-group>
          <label class="_label_input">Name <span class="_require">*</span></label>
          <b-form-input
            autofocus
            :class="{
              'form-group--error': $v.discountParams.discount_code.$error,
            }"
            v-model.trim="$v.discountParams.discount_code.$model"
            class="input-form"
          ></b-form-input>
          <div class="error" v-if="!$v.discountParams.discount_code.required && $v.discountParams.discount_code.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Category <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{
              'form-group--error': $v.discountParams.discount_type.$error,
            }"
            v-model.trim="$v.discountParams.discount_type.$model"
            :options="listDiscountType"
            placeholder="Select one"
            track-by="id"
            label="reference_value_text"
          ></multiselect>
          <div class="error" v-if="!$v.discountParams.discount_type.required && $v.discountParams.discount_type.$error">Field is required</div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Niche Category <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{
              'form-group--error': $v.discountParams.category.$error,
            }"
            v-model.trim="$v.discountParams.category.$model"
            :options="listNicheCategory"
            placeholder="Select one"
            track-by="id"
            label="reference_value_text"
          ></multiselect>
          <div class="error" v-if="!$v.discountParams.category.required && $v.discountParams.category.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Niche Type <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            class="select-type-niche"
            :class="{
              'form-group--error': $v.discountParams.type.$error,
            }"
            v-model.trim="$v.discountParams.type.$model"
            :options="listTypes"
            track-by="id"
            label="reference_value_text"
            placeholder="Select a status"
          ></multiselect>
          <div class="error" v-if="!$v.discountParams.type.required && $v.discountParams.type.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Minimum Qty <span class="_require">*</span></label>
          <b-form-input
            :class="{
              'form-group--error': $v.discountParams.minimum_qty.$error,
            }"
            v-model.trim="$v.discountParams.minimum_qty.$model"
            class="input-form"
            placeholder="00"
          ></b-form-input>
          <div class="error" v-if="!$v.discountParams.minimum_qty.required && $v.discountParams.minimum_qty.$error">Field is required</div>
          <div class="error" v-if="!$v.discountParams.minimum_qty.decimal && $v.discountParams.minimum_qty.$error">Please enter number</div>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Discount Type <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{
              'form-group--error': $v.discountParams.amount_type.$error,
            }"
            v-model.trim="$v.discountParams.amount_type.$model"
            :options="listAmountType"
            placeholder="Select one"
            track-by="id"
            label="reference_value_text"
          ></multiselect>
          <div class="error" v-if="!$v.discountParams.amount_type.required && $v.discountParams.amount_type.$error">Field is required</div>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group>
          <label class="_label_input">Amount <span class="_require">*</span></label>
          <template v-if="discountParams.amount_type.reference_value_text == 'Value'">
            <masked-input
              type="text"
              class="form-control"
              v-model.trim="$v.discountParams.amount.$model"
              :mask="numberAmountMask()"
              :guide="true"
              placeholder="$0.00"
            >
            </masked-input>
            <div class="error" v-if="!$v.discountParams.amount.required && $v.discountParams.amount.$error">Field is required</div>
          </template>
          <template v-else>
            <b-form-input
              :class="{
                'form-group--error': $v.discountParams.amount.$error,
              }"
              v-model.trim="$v.discountParams.amount.$model"
              class="input-form"
            ></b-form-input>
            <div class="error" v-if="!$v.discountParams.amount.required && $v.discountParams.amount.$error">Field is required</div>
          </template>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="6">
        <b-form-group>
          <label class="_label_input">Remarks <span class="_require">*</span></label>
          <b-form-input
            :class="{
              'form-group--error': $v.discountParams.remarks.$error,
            }"
            v-model.trim="$v.discountParams.remarks.$model"
            class="input-form"
          ></b-form-input>
          <!-- <div class="error" v-if="!$v.discountParams.remarks.required && $v.discountParams.remarks.$error">Field is required</div> -->
        </b-form-group>
      </b-col>
    </b-row>
  </b-container>
</template>
<script>
import OtherInfo from "@/components/OtherInfo";
import Calendar from "@/components/Icons/Calendar";
import Multiselect from "vue-multiselect";
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";
const accounting = require("accounting");

import { required, minLength, between, decimal } from "vuelidate/lib/validators";
import { mapActions, mapState } from "vuex";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
import { isEmpty } from "lodash";
export default {
  props: {
    serviceId: {
      type: Number,
      default: null,
    },
    discountDetail: {
      type: Object,
      default: () => {},
    },
  },
  components: {
    OtherInfo,
    Multiselect,
    Calendar,
    MaskedInput,
    ControllTable,
    TableCustom,
  },
  data() {
    return {
      tabIndex: 0,
      isLoading: false,
      ids: [],
      discountParams: {
        id: "",
        discount_code: "",
        discount_type: "",
        type: "",
        minimum_qty: "",
        amount: "",
        remarks: "",
        category: "",
        amount_type: "",
        percent: "",
      },

      checkPrice: false,
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
            key: "ordinal_number",
            label: "#",
            isActive: 1,
            keySearch: "id",
            type: "text",
            isFilter: true,
          },
          {
            key: "service_name",
            label: "Service Type Name",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "price",
            label: "Price",
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
      typeService: "Niches",
    };
  },
  validations: {
    discountParams: {
      discount_code: {
        required,
      },
      discount_type: {
        required,
      },
      type: {
        required,
      },
      minimum_qty: {
        required,
        decimal,
      },
      amount: {
        required,
      },
      remarks: {
        // required,
      },
      category: {
        required,
      },
      amount_type: {
        required,
      },
    },
  },
  created() {
    this.getListDiscountType();
    this.getListTypeNiches();
    this.getListCategoryNiche();
    this.getListAmountType();
    this.getListTypeBooking();
    this.handleDiscountParams();
  },
  computed: {
    ...mapState({
      listDiscountType: (state) => state.discount.listDiscountType,
      listTypes: (state) => state.niche.listTypeNiches,
      listNicheCategory: (state) => state.niche.listNicheCategory,
      listAmountType: (state) => state.discount.listAmountType,
      listTypeBooking: (state) => state.contractor.listTypeBooking,
    }),
  },
  methods: {
    ...mapActions({
      getListDiscountType: "discount/getListDiscountType",
      getListTypeNiches: "niche/getListTypeNiches",
      getListCategoryNiche: "niche/getListCategoryNiche",
      getListAmountType: "discount/getListAmountType",
      createDicountNiches: "discount/createDicountNiches",
      updateDicountNiches: "discount/updateDicountNiches",
      getListTypeBooking: "contractor/getListTypeBooking",
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return "";
      }
    },
    goBack() {
      this.$router.push("/admin-discount");
    },
    onSave() {
      if (this.discountParams.amount_type.reference_value_text == "Value") {
        let check = this.discountParams.amount.search("$");
        if (check == -1) {
          this.discountParams.amount = "";
        }
      } else {
        let check = this.discountParams.amount.search("%");
        if (check == -1) {
          this.discountParams.amount = "";
        } else {
          this.discountParams.percent = this.discountParams.amount.substring(0, 2) / 100;
        }
      }
      this.$v.discountParams.$touch();
      if (this.$v.discountParams.$anyError) {
        if (this.discountParams.price == "") {
          this.checkPrice = true;
        }
        return;
      }
      let action = "createDicountNiches";
      if (this.discountParams.id) {
        action = "updateDicountNiches";
      }
      this.isLoading = true;

      let prms = { ...this.discountParams };
      if (prms.amount_type.reference_value_text == "Value") {
        prms.amount = this.unformatter(this.discountParams.amount);
      }
      prms.discount_type = prms.discount_type.id;
      prms.type = prms.type.id;
      prms.category = prms.category.id;
      prms.amount_type = prms.amount_type.id;
      prms.discount_service = "Niches";
      prms.service_id = this.serviceId;

      this[action](prms)
        .then((res) => {
          this.$swal({
            icon: "success",
            title: "Notifcation",
            text: res.data.status,
          });
          if (action === "createDicountNiches") {
            this.discountParams.id = res.data.data.id;
            this.$router.replace(`admin-discount-info/${res.data.data.id}`);
            this.titleNiches = "Niche Info";
          }
        })
        .catch((error) => {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
    getDetailOther(idOther) {
      this.getOtherDetail(idOther).then((res) => {
        this.discountParams = res.data.data;
        this.discountParams.is_contractor = this.discountParams.contractor;
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
    unformatter(val) {
      return accounting.unformat(val);
    },
    handleDiscountParams: function () {
      if (!isEmpty(this.discountDetail)) {
        this.discountParams.id = this.discountDetail.id;
        this.discountParams.discount_code = this.discountDetail.discount_code;
        this.discountParams.amount_type = this.discountDetail.type_amount;
        this.discountParams.discount_type = this.discountDetail.type_discount;
        this.discountParams.type = this.discountDetail.type;
        this.discountParams.minimum_qty = this.discountDetail.minimum_qty;
        this.discountParams.amount = this.discountDetail.amount;
        this.discountParams.remarks = this.discountDetail.remarks;
        this.discountParams.category = this.discountDetail.category;
      }
    },
  },
  watch: {
    "discountParams.amount_type": function (val) {
      if (val != null) {
        if (val.reference_value_text == "Value") {
          let check = this.discountParams.amount.search("$");
          if (check == -1) {
            this.discountParams.amount = "";
          }
        } else {
          let check = this.discountParams.amount.search("%");
          if (check == -1) {
            this.discountParams.amount = "";
          }
        }
      }
    },
  },
  filters: {
    formatMoney(val) {
      return accounting.formatMoney(val, {
        format: { pos: "%s %v", neg: "%s (%v)", zero: "--" },
      });
    },
  },
};
</script>
