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
          <label class="_label_input">Room Name <span class="_require">*</span></label>
          <multiselect
            :show-labels="false"
            :allow-empty="false"
            deselect-label=""
            :class="{
              'form-group--error': $v.discountParams.room_name.$error,
            }"
            v-model.trim="$v.discountParams.room_name.$model"
            :options="listRoomForBooking"
            placeholder="Select one"
            label="room_no"
            track-by="id"
          ></multiselect>
          <div class="error" v-if="!$v.discountParams.room_name.required && $v.discountParams.room_name.$error">Field is required</div>
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

import { required } from "vuelidate/lib/validators";
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
        amount: "",
        remarks: "",
        room_name: null,
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
      amount: {
        required,
      },
      remarks: {
        // required,
      },
      room_name: {
        required,
      },
      amount_type: {
        required,
      },
    },
  },
  created() {
    this.handleDiscountParams();
    this.getListDiscountType();
    this.getListRoomForBooking();
    this.getListAmountType();
  },
  computed: {
    ...mapState({
      listDiscountType: (state) => state.discount.listDiscountType,
      listRoomForBooking: (state) => state.room.listRoomForBooking,
      listAmountType: (state) => state.discount.listAmountType,
    }),
  },
  methods: {
    ...mapActions({
      getListDiscountType: "discount/getListDiscountType",
      getListRoomForBooking: "room/getListRoomForBooking",
      getListAmountType: "discount/getListAmountType",
      getDiscountDetail: "discount/getDiscountDetail",
      createDicountNiches: "discount/createDicountNiches",
      updateDicountNiches: "discount/updateDicountNiches",
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
      prms.room_id = prms.room_name.id;
      prms.amount_type = prms.amount_type.id;
      prms.discount_service = "Rooms";
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
    handleDiscountParams() {
      if (!isEmpty(this.discountDetail)) {
        this.discountParams.id = this.discountDetail.id;
        this.discountParams.discount_code = this.discountDetail.discount_code;
        this.discountParams.amount_type = this.discountDetail.type_amount;
        this.discountParams.discount_type = this.discountDetail.type_discount;
        this.discountParams.amount = this.discountDetail.amount;
        this.discountParams.remarks = this.discountDetail.remarks;
        this.discountParams.room_name = this.discountDetail.room;
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
