<template>
  <b-container fluid="lg">
    <div class="sale-agreement-info">
      <div class="formInput">
        <b-row>
          <b-col cols="3">
            <b-form-group label="Sale Agreement #">
              <b-form-input :disabled="true" v-model="saleParams.sale_agreement_no" class="input-form"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="3">
            <b-form-group label="Date">
              <div class="position-relative input-date">
                <!-- <Datepicker :disabled="true" :format="customFormatter" class="choose-date" placeholder="dd/mm/yyyy" v-model="saleParams.sale_agreement_date"></Datepicker> -->
                <b-form-input v-model="saleParams.sale_agreement_date" class="input-form"></b-form-input>
                <Calendar />
              </div>
            </b-form-group>
          </b-col>
          <b-col cols="3">
            <b-form-group label="Booking #">
              <b-form-input :disabled="true" v-model="saleParams.booking" class="input-form"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="3">
            <b-form-group label="TGOR Officer">
              <b-form-input :disabled="true" v-model="saleParams.officer" class="input-form"></b-form-input>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row class="mt">
          <b-col cols="3">
            <b-form-group label="Client Name">
              <b-form-input :disabled="true" v-model="saleParams.client_name" class="input-form"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="3">
            <b-form-group label="Mobile">
              <b-form-input :disabled="true" v-model="saleParams.phone" class="input-form"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="3">
            <b-form-group label="Email">
              <b-form-input :disabled="true" v-model="saleParams.email" class="input-form"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="3">
            <b-form-group label="Preferred Contact by">
              <multiselect
                :disabled="true"
                v-model="saleParams.contact"
                :class="{ 'bg-gray': true }"
                :show-labels="false"
                deselect-label=""
                :options="listTypeContact"
                placeholder="Select one"
                track-by="id"
                label="reference_value_text"
              ></multiselect>
            </b-form-group>
          </b-col>
        </b-row>
        <!-- <b-row class="mt">
          <b-col cols="6">
            <b-form-group label="Niches Discount">
              <multiselect
                v-model="saleParams.discount_id"
                :disabled="saleParams.is_invoice"
                :class="{ 'bg-gray': saleParams.is_invoice }"
                :show-labels="false"
                deselect-label=""
                :options="saleParams.discount_list"
                placeholder="Select one"
                track-by="id"
                label="discount_code"
              ></multiselect>
            </b-form-group>
          </b-col>
        </b-row> -->
      </div>
      <div class="line-horizontal"></div>
      <!-- =================================================== Table SaleAgreement ================================================= -->
      <label class="text-label" for="sale-agreement-table">Services</label>
      <b-row class="pd-row">
        <div class="sale-agreement-table">
          <div class="outside-sale-table">
            <template v-if="saleItem.sale_agreement_type !== null">
              <div v-if="saleItem.sale_agreement_type.reference_value_text === 'Niches'">
                <TableCustom
                  @Items="getItems"
                  :tableFields="columnActive.fields"
                  :tableItems="saleParams.sale_agreement_item"
                  :showFooter="true"
                  :rowClass="tbodyTrClass"
                  @rowClicked="addDiscountToLine"
                  :colspan="7"
                >
                  <template slot="tgor_table:service_type" slot-scope="data">
                    {{ data.item.booking_line_item.booking_type.reference_value_text }}
                  </template>
                  <template slot="tgor_table:niches" slot-scope="data">
                    {{
                      data.item.booking_line_item.booking_type.reference_value_text == "Niches"
                        ? data.item.booking_line_item.niche.reference_no
                        : "--"
                    }}
                  </template>
                  <template slot="tgor_table:location" slot-scope="data">
                    {{
                      data.item.booking_line_item.booking_type.reference_value_text == "Niches" ? data.item.booking_line_item.niche.location : "--"
                    }}
                  </template>
                  <template slot="tgor_table:detail" slot-scope="data">
                    <div v-if="data.item.booking_line_item.booking_type.reference_value_text == 'Niches'">
                      {{ data.item.booking_line_item.niche.type !== null ? `${data.item.booking_line_item.niche.type.reference_value_text} - ` : "" }}
                      {{
                        data.item.booking_line_item.niche.category !== null
                          ? `${data.item.booking_line_item.niche.category.reference_value_text}`
                          : ""
                      }}
                    </div>
                    <div v-else>--</div>
                  </template>
                  <template slot="tgor_table:lease" slot-scope="data">
                    <div v-if="data.item.booking_line_item.lease_start_date !== null">
                      {{ customFormatter(data.item.booking_line_item.lease_start_date) }}
                    </div>
                    <div v-if="data.item.booking_line_item.lease_expiry_date !== null">
                      {{ customFormatter(data.item.booking_line_item.lease_expiry_date) }}
                    </div>
                    <div v-if="data.item.booking_line_item.lease_expiry_date == null && data.item.booking_line_item.lease_start_date == null">--</div>
                  </template>
                  <template slot="tgor_table:remarks" slot-scope="data">
                    {{ data.item.booking_line_item.remarks || "--" }}
                  </template>
                  <template slot="tgor_table:booking_line_item.amount" slot-scope="data">
                    {{
                      data.item.booking_line_item.amount && data.item.booking_line_item.amount != ""
                        ? +unFormatter(data.item.booking_line_item.amount)
                        : 0 | formatMoney
                    }}
                  </template>
                  <template slot="tgor_table:booking_line_item.tax_amount" slot-scope="data">
                    {{
                      data.item.booking_line_item.tax_amount && data.item.booking_line_item.tax_amount != ""
                        ? +unFormatter(data.item.booking_line_item.tax_amount)
                        : 0 | formatMoney
                    }}
                    {{ gst ? `(${gst}%)` : "0%" }}
                  </template>
                  <template slot="tgor_table:booking_line_item.discount" slot-scope="data">
                    <!-- data.item.booking_line_item.booking_type.reference_value_text == "Niches" -->
                    <template>
                      <template v-if="data.item.booking_line_item.discount_amount && data.item.booking_line_item.discount_amount != ''">
                        {{ +unFormatter(data.item.booking_line_item.discount_amount) | formatMoney }}
                      </template>
                      <template v-else>
                        <span class="add-discount-custom">Add</span>
                      </template>
                    </template>

                    <!-- {{data.item.booking_line_item.discount && data.item.booking_line_item.discount != '' ? +unFormatter(data.item.booking_line_item.discount) : 0 | formatMoney}} -->
                  </template>
                  <template slot="tgor_table:booking_line_item.total_amount" slot-scope="data">
                    {{ data.item.booking_line_item.total_amount ? +unFormatter(data.item.booking_line_item.total_amount) : 0 | formatMoney }}
                  </template>
                </TableCustom>
              </div>
              <div v-if="saleItem.sale_agreement_type.reference_value_text === 'Memorial Rooms'">
                <TableCustom
                  @Items="getItems"
                  :tableFields="columnActiveRoom.fields"
                  :tableItems="saleParams.sale_agreement_item"
                  :showFooter="true"
                  :rowClass="tbodyTrClass"
                  @rowClicked="addDiscountToLine"
                  :colspan="9"
                >
                  <template slot="tgor_table:service_type" slot-scope="data">
                    {{ data.item.booking_line_item.booking_type.reference_value_text }}
                  </template>
                  <template slot="tgor_table:facility" slot-scope="data">
                    {{
                      data.item.booking_line_item.booking_type.reference_value_text == "Memorial Rooms"
                        ? data.item.booking_line_item.room.room_no
                        : "--"
                    }}
                  </template>
                  <template slot="tgor_table:event" slot-scope="data">
                    {{
                      data.item.booking_line_item.booking_type.reference_value_text == "Memorial Rooms"
                        ? data.item.booking_line_item.event.reference_value_text
                        : "--"
                    }}
                  </template>
                  <template slot="tgor_table:rate_type" slot-scope="data">
                    {{
                      data.item.booking_line_item.booking_type.reference_value_text == "Memorial Rooms"
                        ? data.item.booking_line_item.room_type.reference_value_text
                        : "--"
                    }}
                  </template>
                  <template slot="tgor_table:rate" slot-scope="data">
                    <div v-if="data.item.booking_line_item.booking_type.reference_value_text == 'Memorial Rooms'">
                      <div
                        v-if="data.item.booking_line_item.room_type !== null && data.item.booking_line_item.room_type.reference_value_text == 'Daily'"
                      >
                        {{ +unFormatter(data.item.booking_line_item.room.price_daily) | formatMoney }}
                      </div>
                      <div
                        v-else-if="
                          data.item.booking_line_item.room_type !== null && data.item.booking_line_item.room_type.reference_value_text == 'Hourly'
                        "
                      >
                        {{ +unFormatter(data.item.booking_line_item.room.price_hourly) | formatMoney }}
                      </div>
                      <div v-else>$ 0</div>
                    </div>
                    <div v-else>--</div>
                  </template>
                  <template slot="tgor_table:period" slot-scope="data">
                    <div>
                      {{ data.item.booking_line_item.check_in_date ? customFormatter(data.item.booking_line_item.check_in_date) : "" }}
                      {{ data.item.booking_line_item.check_in_time ? formatTime(data.item.booking_line_item.check_in_time) : "" }}
                    </div>
                    <div>
                      {{ data.item.booking_line_item.check_out_time ? customFormatter(data.item.booking_line_item.check_out_date) : "" }}
                      {{ data.item.booking_line_item.check_out_time ? formatTime(data.item.booking_line_item.check_out_time) : "" }}
                    </div>
                  </template>
                  <template slot="tgor_table:day" slot-scope="data">
                    {{ calculateNumberOfDays(data.item.booking_line_item.check_in_date, data.item.booking_line_item.check_out_date) }}
                  </template>
                  <template slot="tgor_table:remarks" slot-scope="data">
                    {{ data.item.booking_line_item.remarks || "--" }}
                  </template>
                  <template slot="tgor_table:booking_line_item.amount" slot-scope="data">
                    {{
                      data.item.booking_line_item.amount && data.item.booking_line_item.amount != ""
                        ? +unFormatter(data.item.booking_line_item.amount)
                        : 0 | formatMoney
                    }}
                  </template>
                  <template slot="tgor_table:booking_line_item.tax_amount" slot-scope="data">
                    {{
                      data.item.booking_line_item.tax_amount && data.item.booking_line_item.tax_amount != ""
                        ? +unFormatter(data.item.booking_line_item.tax_amount)
                        : 0 | formatMoney
                    }}
                    {{ gst ? `(${gst}%)` : "0%" }}
                  </template>
                  <template slot="tgor_table:booking_line_item.discount" slot-scope="data">
                    <!-- data.item.booking_line_item.booking_type.reference_value_text == "Niches" -->
                    <template>
                      <template v-if="data.item.booking_line_item.discount_amount && data.item.booking_line_item.discount_amount != ''">
                        {{ +unFormatter(data.item.booking_line_item.discount_amount) | formatMoney }}
                      </template>
                      <template v-else>
                        <span class="add-discount-custom">Add</span>
                      </template>
                    </template>

                    <!-- {{data.item.booking_line_item.discount && data.item.booking_line_item.discount != '' ? +unFormatter(data.item.booking_line_item.discount) : 0 | formatMoney}} -->
                  </template>
                  <template slot="tgor_table:booking_line_item.total_amount" slot-scope="data">
                    {{ data.item.booking_line_item.total_amount ? +unFormatter(data.item.booking_line_item.total_amount) : 0 | formatMoney }}
                  </template>
                </TableCustom>
              </div>
            </template>
          </div>
        </div>
      </b-row>

      <!-- =================================================== End Table SaleAgreement ================================================= -->
      <div class="line-horizontal"></div>
      <b-row class="title-sign"> Signature </b-row>
      <b-row class="sign-officer">
        <b-col cols="6">
          <b-row>
            <b-col cols="2" class="offier">
              <span class="st-icon-pandora"> TGOR </span>
              <br />
              <span> Officer </span>
            </b-col>
            <b-col cols="8">
              <div class="no_line" v-if="saleParams.signature_tgor_officer">
                <b-img @click="officerClear" class="image" :src="saleParams.signature_tgor_officer" fluid alt="Responsive image"></b-img>
              </div>
              <div class="signture" v-else>
                <VueSignaturePad
                  :options="{
                    onBegin: () => {
                      $refs.signaturePadOfficer.resizeCanvas();
                    },
                  }"
                  width="392px"
                  height="117px"
                  ref="signaturePadOfficer"
                />
                <b-img
                  @click="officerClear"
                  :class="{ 'd-none': saleParams.signature_tgor_officer }"
                  class="image"
                  src="/images/edit.jpg"
                  fluid
                  alt="Responsive image"
                ></b-img>
              </div>
            </b-col>
          </b-row>
        </b-col>
        <b-col cols="6">
          <b-row>
            <b-col cols="2">
              <span class="st-icon-pandora"> Client </span>
            </b-col>
            <b-col cols="8">
              <div class="no_line" v-if="saleParams.signature_client">
                <b-img @click="clientClear" class="image" :src="saleParams.signature_client" fluid alt="Responsive image"></b-img>
              </div>
              <div class="signture" v-else>
                <VueSignaturePad
                  :options="{
                    onBegin: () => {
                      $refs.signaturePadClient.resizeCanvas();
                    },
                  }"
                  width="392px"
                  height="117px"
                  ref="signaturePadClient"
                />
                <b-img
                  @click="clientClear"
                  :class="{ 'd-none': saleParams.signature_client }"
                  class="image"
                  src="/images/edit.jpg"
                  fluid
                  alt="Responsive image"
                ></b-img>
              </div>
            </b-col>
          </b-row>
        </b-col>
      </b-row>
      <b-modal centered ref="add_discount" hide-footer id="extension" size="sm" title="Add Discount">
        <b-container fluid="lg">
          <b-row>
            <b-col cols="12">
              <div class="subtitle">Line Item Detail</div>
              <div class="text-normal">{{ titleDetail }}</div>
            </b-col>
          </b-row>
          <b-row>
            <b-col cols="12">
              <b-form-group label="Amount">
                <masked-input
                  type="text"
                  v-model="prmsDiscount.amount"
                  class="form-control"
                  :mask="numberAmountMask()"
                  :guide="true"
                  placeholder="$0.00"
                  :disabled="true"
                >
                </masked-input>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="12">
              <b-form-group label="Discount Type">
                <multiselect
                  v-model="prmsDiscount.discount_type"
                  :show-labels="false"
                  deselect-label=""
                  :options="listTypeDiscount"
                  placeholder="Select one"
                ></multiselect>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="12">
              <b-form-group label="Discount">
                <masked-input
                  v-if="prmsDiscount.discount_type == 'Fixed Price'"
                  type="text"
                  v-model="prmsDiscount.discount"
                  class="form-control"
                  :mask="numberMinusAmountMask()"
                  :guide="true"
                  placeholder="-$0.00"
                />
                <masked-input
                  v-if="prmsDiscount.discount_type == 'Percentage'"
                  type="text"
                  v-model="prmsDiscount.discount"
                  class="form-control"
                  :mask="numberMinusAmountPercentMask()"
                  :guide="true"
                  placeholder="%"
                />
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="btn-submit">
            <b-col cols="12">
              <div class="submit" @click="submitAddDiscount">Submit</div>
            </b-col>
          </b-row>
        </b-container>
      </b-modal>
    </div>
  </b-container>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import Calendar from "@/components/Icons/Calendar";
import moment from "moment";
import TableCustom from "../../components/Table/saleAgreementTable.vue";
import Multiselect from "vue-multiselect";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
const accounting = require("accounting");
import { mapActions, mapState } from "vuex";
import { SaleAgreementNiche, SaleAgreementRoom } from "@/enums/saleAgreementTableHeader";
import { _, map } from "lodash";
export default {
  name: "",
  components: {
    Datepicker,
    Calendar,
    TableCustom,
    Multiselect,
    MaskedInput,
    SaleAgreementNiche,
    SaleAgreementRoom,
  },
  props: {
    saleItem: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      total: {},
      saleAgreementType: null,
      saleParams: {
        booking: "",
        booking_id: "",
        client_name: "",
        phone: "",
        email: "",
        contact: "",
        id: "",
        sale_agreement_date: "",
        sale_agreement_item: [],
        sale_agreement_no: "",
        status: "",
        total_amount: "",
        total_tax_amount: "",
        user_id: "",
        signature_client: "",
        signature_tgor_officer: "",
        discount_list: [],
        discount_custom: [],
        discount_id: "",
        is_invoice: false,
        officer: "",
        gst_id: "",
        gst_detail: {},
      },
      prmsDiscount: {
        id: "",
        amount: "",
        discount: "",
        type: "",
        sale_id: "",
        line_item_id: "",
        discount_type: "Fixed Price",
      },
      columnActive: SaleAgreementNiche,
      columnActiveRoom: SaleAgreementRoom,
      typeModel: "",
      titleDetail: "",
      tabIndex: 0,
      activeFilter: false,
      activeStatus: "Active",
      activeClass: false,
      allSelected: false,
      selected: [],
      listTypeContact: [],
      listTypeDiscount: ["Fixed Price", "Percentage"],
      ids: [],
      isSave: false,
      gst: null,
      gstNumber: 0,
    };
  },
  mounted() {
    this.gstdetail(prms)
      .then((res) => {
        this.gst = Math.floor(res.data.data.rate * 100);
        this.gstNumber = res.data.data.rate;
      })
      .catch((error) => {});
  },
  created() {
    this.$store.commit("saleareement/emptySumTotal");
    this.$nextTick(function () {
      this.$refs.signaturePadOfficer.resizeCanvas();
      this.$refs.signaturePadClient.resizeCanvas();
    });
  },
  computed: mapState({
    totalDetail: (state) => state.saleareement.totalDetail,
  }),
  methods: {
    ...mapActions({
      getSumTotal: "saleareement/getSumTotal",
      saveSaleArgeement: "saleareement/saveSaleArgeement",
      generateInvoices: "invoice/generateInvoices",
      emptySumTotal: "saleareement/emptySumTotal",
      getDetailSaleAreement: "saleareement/getDetailSaleAreement",
      handleTotalSaleAgreement: "saleareement/handleTotalSaleAgreement",
      gstdetail: "gst/gstdetail",
    }),
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    formatTime(time) {
      return moment(time, "HH:mm:ss").format("HH:mm");
    },
    getItems(item) {
      this.ids = item;
      this.sumTotalAmount();
    },
    removeDiscountNiche() {
      if (this.saleParams.discount_custom?.length > 0) {
        _.remove(this.saleParams.discount_custom, function (data) {
          return data.type === "niches";
        });
      }
    },
    handleSetTotal() {
      let total = 0;
      var arr_sale_line_item = [];
      this.saleParams.sale_agreement_item.map((item, i) => {
        arr_sale_line_item.push(item);
      });
      arr_sale_line_item.map((item, i) => {
        total += parseFloat(this.unformatter(item.booking_line_item.total_amount));
      });
      this.saleParams.total_amount = total;
    },
    sumTotalAmount() {
      var arr_sale_line_item = [];
      this.saleParams.sale_agreement_item.map((item, i) => {
        let isCheck = this.ids.includes(item.id);
        if (isCheck) {
          arr_sale_line_item.push(item);
        }
      });
      var totalDetail = {
        amount: 0,
        gst_amount: 0,
        discount: 0,
        total_amount: 0,
      };

      arr_sale_line_item.map((item, i) => {
        totalDetail.amount += parseFloat(item.booking_line_item.amount);
        totalDetail.gst_amount += parseFloat(item.booking_line_item.tax_amount);
        totalDetail.total_amount += parseFloat(this.unformatter(item.booking_line_item.total_amount));
        let discountAmount =
          item.booking_line_item.discount_amount !== null
            ? Number.isInteger(item.booking_line_item.discount_amount)
              ? item.booking_line_item.discount_amount
              : parseFloat(item.booking_line_item.discount_amount)
            : 0;
        totalDetail.discount += discountAmount;
      });
      this.$store.commit("saleareement/updateTotal", totalDetail);
    },
    submitAddDiscount() {
      let prms = { ...this.prmsDiscount };
      let discount = Math.abs(this.unformatter(this.prmsDiscount.discount));
      let amount = this.unformatter(this.prmsDiscount.amount);
      if (discount > amount) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "The discount amount cannot be larger than the amount.",
        });
        return;
      }
      this.saleParams.sale_agreement_item.map((item, i) => {
        if (item.id == prms.id) {
          if (prms.discount_type == "Fixed Price") {
            let total = parseFloat(item.booking_line_item.amount) - discount;
            let newDiscount = this.gstNumber * total;
            this.saleParams.sale_agreement_item[i].booking_line_item.discount_amount = discount;
            this.saleParams.sale_agreement_item[i].booking_line_item.tax_amount = newDiscount;
            this.saleParams.sale_agreement_item[i].booking_line_item.get_discount = discount;
            this.saleParams.sale_agreement_item[i].booking_line_item.total_amount = total + newDiscount;
          } else {
            let discountAmount = parseFloat(item.booking_line_item.amount) * (discount / 100);
            let total = parseFloat(item.booking_line_item.amount) - discountAmount;
            let newDiscount = this.gstNumber * total;
            this.saleParams.sale_agreement_item[i].booking_line_item.discount_amount = discountAmount;
            this.saleParams.sale_agreement_item[i].booking_line_item.tax_amount = newDiscount;
            this.saleParams.sale_agreement_item[i].booking_line_item.get_discount = discount;
            this.saleParams.sale_agreement_item[i].booking_line_item.total_amount = total + newDiscount;
          }
        }
      });
      this.$refs.add_discount.hide();

      let discountObject = {
        sale_id: prms.sale_id,
        type: prms.type,
        discount_amount: discount,
        line_item_id: prms.line_item_id,
        discount_type: prms.discount_type,
      };

      this.saleParams.discount_custom.push(discountObject);
      this.sumTotalAmount();
      this.handleSetTotal();
    },
    officerClear() {
      this.$refs.signaturePadOfficer.clearSignature();
    },
    clientClear() {
      this.$refs.signaturePadClient.clearSignature();
    },
    unFormatter(val) {
      return accounting.unformat(val);
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
    numberMinusAmountMask() {
      return createNumberMask({
        prefix: "-$",
        suffix: "",
        allowDecimal: true,
        includeThousandsSeparator: true,
        allowLeadingZeroes: true,
        allowNegative: false,
        integerLimit: 8,
        decimalLimit: 2,
      });
    },
    numberMinusAmountPercentMask() {
      return createNumberMask({
        prefix: "",
        suffix: "%",
        allowDecimal: true,
        includeThousandsSeparator: true,
        allowLeadingZeroes: true,
        allowNegative: false,
        integerLimit: 3,
      });
    },
    getSignOfficer() {
      if (this.saleParams.signature_tgor_officer == null) {
        const { isEmpty, data } = this.$refs.signaturePadOfficer.saveSignature();

        if (!isEmpty) {
          var signOfficerContent = data.replace(/^data:image\/(png|jpg);base64,/, "");
          var officer = this.base64ToBlob(signOfficerContent, "image/png");
          return officer;
        }
      }
      return null;
    },
    getSignClient() {
      if (this.saleParams.signature_client == null) {
        const { isEmpty, data } = this.$refs.signaturePadClient.saveSignature();
        if (!isEmpty) {
          var signClientContent = data.replace(/^data:image\/(png|jpg);base64,/, "");
          var client = this.base64ToBlob(signClientContent, "image/png");
          return client;
        }
      }
      return null;
    },
    onSaveSignture() {
      let officer = this.getSignOfficer();
      let client = this.getSignClient();
      var formData = new FormData();
      if (officer != null) {
        formData.append("signature_offier", officer);
      } else {
        if (this.saleParams.signature_tgor_officer == null) {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: "Don't Forget Officer Signature",
          });
          return;
        }
      }
      if (client != null) {
        formData.append("signature_client", client);
      }
      formData.append("discount_custom", JSON.stringify(this.saleParams.discount_custom));
      if (this.saleParams.discount_id) {
        formData.append("discount_id", this.saleParams.discount_id.id);
      }
      formData.append("total", this.unformatter(this.saleParams.total_amount));
      let prms = {
        id: this.$router.history.current.params.id,
        data: formData,
      };
      this.saveSaleArgeement(prms)
        .then((res) => {
          this.getDetailSaleAreement({
            id: this.$router.history.current.params.id,
          }).then((val) => {
            if (val.admin) {
              this.saleParams.officer = val.admin.display_name;
            }
          });
          let saleAgreementDetail = { ...this.saleItem };
          if (saleAgreementDetail) {
            let prms = {
              sale_agreement_id: this.$router.history.current.params.id,
              id: saleAgreementDetail.sale_agreement_item.id,
            };
            this.handleTotalSaleAgreement(prms);
          }
          this.$swal({
            icon: "success",
            title: "Success!",
            text: res.data.status,
          });
          (this.saleParams.signature_client = res.data.data.signature_client),
            (this.saleParams.signature_tgor_officer = res.data.data.signature_tgor_officer);
          this.isSave = true;
          this.$emit("isSave", this.isSave);
        })
        .catch((error) => {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
    base64ToBlob(base64, mime) {
      mime = mime || "";
      var sliceSize = 1024;
      var byteChars = window.atob(base64);
      var byteArrays = [];

      for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
        var slice = byteChars.slice(offset, offset + sliceSize);

        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
          byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
      }

      return new Blob(byteArrays, { type: mime });
    },
    onGenerateInv() {
      if (!this.isSave) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Please save the information before generate invoice.",
        });
        return;
      }
      let prms = {
        id: this.$router.history.current.params.id,
        booking_id: this.saleParams.booking_id,
        service_ids: this.ids,
        total_amount: this.unformatter(this.totalDetail.amount),
        total_tax_amount: this.unformatter(this.totalDetail.gst_amount),
        total: this.unformatter(this.totalDetail.total_amount),
      };
      this.generateInvoices(prms)
        .then((res) => {
          this.$store.commit("saleareement/emptySumTotal");
          this.$router.push({ name: "BookingGeneral" });
        })
        .catch((error) => {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
    unformatter(val) {
      return accounting.unformat(val);
    },
    tbodyTrClass(item) {
      if (item != null && item.isInvoice == 1) {
        return "hidden-row";
      }
      return "";
    },
    addDiscountToLine(items) {
      if (
        typeof this.saleParams?.discount_id?.id !== "undefined" &&
        this.saleParams?.discount_id?.id !== "" &&
        items.booking_line_item.booking_type.reference_value_text === "Niches"
      ) {
        return;
      }
      switch (items.booking_line_item.booking_type.reference_value_text) {
        case "Niches":
          this.titleDetail = `Booking Niche - ${items.booking_line_item.niche.reference_no}`;
          this.typeModel = "niches";
          break;
        case "Additional Services":
          this.titleDetail = `${items.booking_line_item.other.service_name} - ${items.booking_line_item.service_type?.service_name}`;
          this.typeModel = "additional_services";
          break;
        case "Memorial Rooms":
          this.titleDetail = `Booking Memorial Room - ${items.booking_line_item.room.room_no}`;
          this.typeModel = "room";
          break;
        default:
          this.titleDetail = "";
          this.typeModel = "";
          break;
      }
      this.prmsDiscount.id = items.id;
      this.prmsDiscount.sale_id = items.sale_agreement_id;
      this.prmsDiscount.amount = items.booking_line_item.amount_format;
      this.prmsDiscount.discount = "";
      this.prmsDiscount.line_item_id = items.line_item_id;
      this.prmsDiscount.type = this.typeModel;
      this.$refs.add_discount.show();
    },
    handleDiscount(val, item, index) {
      if (val.type_amount.reference_value_text == "Value") {
        let total = parseFloat(item.booking_line_item.amount) - discount;
        let newDiscount = this.gstNumber * total;

        this.saleParams.sale_agreement_item[index].booking_line_item.discount_amount = val.amount;
        this.saleParams.sale_agreement_item[index].booking_line_item.get_discount = val.amount;
        this.saleParams.sale_agreement_item[index].booking_line_item.tax_amount = newDiscount;
        this.saleParams.sale_agreement_item[index].booking_line_item.total_amount = total + newDiscount;
      } else {
        let money = parseFloat(item.booking_line_item.amount) * val.percent;
        let total = parseFloat(item.booking_line_item.amount) - money;
        let newDiscount = this.gstNumber * total;
        this.saleParams.sale_agreement_item[index].booking_line_item.discount_amount = money;
        this.saleParams.sale_agreement_item[index].booking_line_item.get_discount = money;
        this.saleParams.sale_agreement_item[index].booking_line_item.tax_amount = newDiscount;
        this.saleParams.sale_agreement_item[index].booking_line_item.total_amount = total + newDiscount;
      }
    },
    handleDefaultGst() {
      this.saleParams?.sale_agreement_item.map((item, i) => {
        let discount = 0;
        let total = 0;
        let totalAmount = 0;
        let taxAmount = 0;
        if (this.saleParams.sale_agreement_item[i].booking_line_item.discount_amount) {
          discount = this.saleParams.sale_agreement_item[i].booking_line_item.discount_amount;
          total = parseFloat(item.booking_line_item.amount) - discount;
          let newDiscount = this.gstNumber * total;
          taxAmount = newDiscount;
          totalAmount = total + newDiscount;
        } else {
          total = parseFloat(item.booking_line_item.amount) + parseFloat(item.sale_agreement_line_item.booking_line_item.tax_amount);
          totalAmount = total;
        }
        this.saleParams.sale_agreement_item[i].booking_line_item.discount_amount = discount;
        this.saleParams.sale_agreement_item[i].booking_line_item.get_discount = 0;
        this.saleParams.sale_agreement_item[i].booking_line_item.tax_amount = taxAmount;
        this.saleParams.sale_agreement_item[i].booking_line_item.total_amount = totalAmount;
      });
      this.sumTotalAmount();
      this.handleSetTotal();
    },
    calculateNumberOfDays(checkin, checkout) {
      var startDate = moment(checkin);
      var endDate = moment(checkout);
      var result = endDate.diff(startDate, "days") + 1;
      return result || "--";
    },
  },
  filters: {
    formatMoney(val) {
      return accounting.formatMoney(val, {
        format: { pos: "%s %v", neg: "%s (%v)", zero: "--" },
      });
    },
  },
  watch: {
    saleItem: function (val) {
      console.log(val, "999999999999999");
      this.ids = val.sale_agreement_item.map((item) => item.id);
      this.saleParams.booking = val.booking.booking_no;
      this.saleParams.booking_id = val.booking_id;
      this.saleParams.client_name = val.client.display_name;
      this.saleParams.phone = val.client.phone;
      this.saleParams.email = val.client.email;
      this.saleParams.contact = val.client.preferred_contact_by;
      this.saleParams.id = val.id;
      this.saleParams.sale_agreement_date = this.customFormatter(val.sale_agreement_date);
      this.saleParams.sale_agreement_item = val.sale_agreement_item;
      this.saleParams.sale_agreement_no = val.sale_agreement_no;
      this.saleParams.status = val.status;
      this.saleParams.total_amount = val.total_amount;
      this.saleParams.total_tax_amount = val.total_tax_amount;
      this.saleParams.user_id = val.user_id;
      this.saleParams.signature_client = val.signature_client;
      this.saleParams.signature_tgor_officer = val.signature_tgor_officer;
      this.saleParams.discount_list = val.discount_list;
      this.saleParams.discount_id = val.discount;
      this.saleParams.is_invoice = val.is_invoice;
      this.saleParams.gst_id = val.gst_id;
      if (val.admin) {
        this.saleParams.officer = val.admin.display_name;
      }
      if (val.signature_client != null || val.signature_tgor_officer != null) {
        this.$emit("isSave", true);
        this.isSave = true;
      }
      if (this.saleParams.gst_id) {
        let prms = {
          gst_id: this.saleParams.gst_id,
        };
        this.gstdetail(prms)
          .then((res) => {
            if (res.data.data) {
              this.gst = Math.floor(res.data.data.rate * 100);
              this.gstNumber = res.data.data.rate;
            }
          })
          .catch((error) => {});
      }
      this.sumTotalAmount();
    },
    "saleParams.discount_id": function (val) {
      if (val != null && val != "") {
        this.saleParams.sale_agreement_item.map((item, i) => {
          if (item.booking_line_item.booking_type.reference_value_text == "Niches" && val.service_type.reference_value_text == "Niches") {
            this.handleDiscount(val, item, i);
          }
          if (
            item.booking_line_item.booking_type.reference_value_text == "Memorial Rooms" &&
            val.service_type.reference_value_text == "Memorial Rooms" &&
            val.room_id == item.booking_line_item.room.id
          ) {
            this.handleDiscount(val, item, i);
          }
          if (
            item.booking_line_item.booking_type.reference_value_text == "Additional Services" &&
            val.service_type.reference_value_text == "Additional Services" &&
            val.other_id == item.booking_line_item.other.id &&
            val.type == item.booking_line_item.other.type
          ) {
            this.handleDiscount(val, item, i);
          }
        });
        this.removeDiscountNiche();
      } else {
        this.handleDefaultGst();
        this.sumTotalAmount();
        this.handleSetTotal();
      }
    },
    gstNumber: function (val) {
      this.handleDefaultGst();
    },
  },
};
</script>
