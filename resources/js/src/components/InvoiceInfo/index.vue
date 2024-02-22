<template>
  <b-container fluid="lg" class="invoice-info">
    <div class="formInput">
      <b-row>
        <b-col cols="3">
          <b-form-group label="Invoice #">
            <b-form-input :disabled="true" v-model="invoiceParams.invoice_no" class="input-form"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Date">
            <div class="position-relative input-date">
              <!-- <Datepicker :disabled="true" :format="customFormatter" class="choose-date" placeholder="dd/mm/yyyy" v-model="invoiceParams.invoice_date"></Datepicker> -->
              <b-form-input :disabled="true" v-model="invoiceParams.invoice_date" class="input-form"></b-form-input>
              <Calendar />
            </div>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="TGOR Officer">
            <b-form-input :disabled="true" v-model="invoiceParams.officer" class="input-form"></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row class="mt">
        <b-col cols="3">
          <b-form-group label="Client Name">
            <b-form-input :disabled="true" class="input-form" v-model="invoiceParams.client_name"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Mobile">
            <b-form-input :disabled="true" class="input-form" v-model="invoiceParams.phone"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Email">
            <b-form-input :disabled="true" class="input-form" v-model="invoiceParams.email"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Preferred Contact by">
            <b-form-input :disabled="true" class="input-form" v-model="invoiceParams.contact"></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row class="mt">
        <!-- <b-col cols="6">
          <b-form-group label="Niches Discount">
            <multiselect
              v-model="invoiceParams.discount_id"
              :show-labels="false"
              deselect-label=""
              :options="invoiceParams.discount_list"
              placeholder="Select one"
              track-by="id"
              label="discount_code"
              :class="{ 'bg-gray': invoiceParams.is_payment }"
              :disabled="invoiceParams.is_payment"
            ></multiselect>
          </b-form-group>
        </b-col> -->
        <b-col cols="6">
          <b-form-group label="Remarks">
            <b-form-input class="input-form" v-model="invoiceParams.remarks"></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
    </div>
    <div class="line-horizontal"></div>
    <!-- =================================================== Table SaleAgreement ================================================= -->
    <label class="text-label" for="sale-agreement-table">Services</label>
    <b-row class="pd-row">
      <div class="sale-agreement-table">
        <div class="outside-sale-table">
          <ControllTable
            :optionSearch="optionsFilter"
            :onChangeSearch="onChangeSearch"
            :onCreate="onCreateLineItem"
            @deleteItems="deleteItem"
            :isShowIconSearch="false"
          />
          <template v-if="invoiceDetail.sale_agreement.sale_agreement_type !== null">
            <div v-if="invoiceDetail.sale_agreement.sale_agreement_type.reference_value_text === 'Niches'">
              <TableCustom
                @Items="getItems"
                :tableFields="columnActive.fields"
                :tableItems="invoiceParams.invoice_line_item || []"
                :showFooter="true"
                :rowClass="tbodyTrClass"
                :colspan="8"
              >
                <template slot="tgor_table:agreement_id" slot-scope="data">
                  {{ data.item.sale_agreement.sale_agreement_no }}
                </template>
                <template slot="tgor_table:service_type" slot-scope="data">
                  {{ data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text }}
                </template>
                <template slot="tgor_table:location" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == "Niches"
                      ? data.item.sale_agreement_line_item.booking_line_item.niche.location
                      : "--"
                  }}
                </template>
                <template slot="tgor_table:detail" slot-scope="data">
                  <div v-if="data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == 'Niches'">
                    {{
                      data.item.sale_agreement_line_item.booking_line_item.niche.type !== null
                        ? `${data.item.sale_agreement_line_item.booking_line_item.niche.type.reference_value_text} - `
                        : ""
                    }}
                    {{
                      data.item.sale_agreement_line_item.booking_line_item.niche.category !== null
                        ? `${data.item.sale_agreement_line_item.booking_line_item.niche.category.reference_value_text}`
                        : ""
                    }}
                  </div>
                  <div v-else>--</div>
                </template>
                <template slot="tgor_table:lease" slot-scope="data">
                  <div v-if="data.item.sale_agreement_line_item.booking_line_item.lease_start_date !== null">
                    {{ customFormatter(data.item.sale_agreement_line_item.booking_line_item.lease_start_date) }}
                  </div>
                  <div v-if="data.item.sale_agreement_line_item.booking_line_item.lease_expiry_date !== null">
                    {{ customFormatter(data.item.sale_agreement_line_item.booking_line_item.lease_expiry_date) }}
                  </div>
                  <div
                    v-if="
                      data.item.sale_agreement_line_item.booking_line_item.lease_expiry_date == null &&
                      data.item.sale_agreement_line_item.booking_line_item.lease_start_date == null
                    "
                  >
                    --
                  </div>
                </template>
                <template slot="tgor_table:niches" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == "Niches"
                      ? data.item.sale_agreement_line_item.booking_line_item.niche.reference_no
                      : "--"
                  }}
                </template>
                <template slot="tgor_table:remarks" slot-scope="data">
                  {{ data.item.sale_agreement_line_item.booking_line_item.remarks || "--" }}
                </template>
                <template slot="tgor_table:booking_line_item.amount" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.amount && data.item.sale_agreement_line_item.booking_line_item.amount != ""
                      ? +unFormatter(data.item.sale_agreement_line_item.booking_line_item.amount)
                      : 0 | formatMoney
                  }}
                </template>
                <template slot="tgor_table:booking_line_item.tax_amount" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.tax_amount &&
                    data.item.sale_agreement_line_item.booking_line_item.tax_amount != ""
                      ? +unFormatter(data.item.sale_agreement_line_item.booking_line_item.tax_amount)
                      : 0 | formatMoney
                  }}
                  {{ gst ? `(${gst}%)` : "0%" }}
                </template>
                <template slot="tgor_table:booking_line_item.total_amount" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.total_amount
                      ? +unFormatter(data.item.sale_agreement_line_item.booking_line_item.total_amount)
                      : 0 | formatMoney
                  }}
                </template>
                <template slot="tgor_table:booking_line_item.discount" slot-scope="data">
                  <!-- <template  v-if=" data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == 'Niches'"> -->
                  <template
                    v-if="
                      data.item.sale_agreement_line_item.booking_line_item.discount_amount &&
                      data.item.sale_agreement_line_item.booking_line_item.discount_amount != ''
                    "
                  >
                    {{ +unFormatter(data.item.sale_agreement_line_item.booking_line_item.discount_amount) | formatMoney }}
                  </template>
                  <!-- </template> -->
                  <template v-else> -- </template>
                </template>
              </TableCustom>
            </div>
            <div v-if="invoiceDetail.sale_agreement.sale_agreement_type.reference_value_text === 'Memorial Rooms'">
              <TableCustom
                @Items="getItems"
                :tableFields="columnActiveRoom.fields"
                :tableItems="invoiceParams.invoice_line_item || []"
                :showFooter="true"
                :rowClass="tbodyTrClass"
                :colspan="10"
              >
                <template slot="tgor_table:agreement_id" slot-scope="data">
                  {{ data.item.sale_agreement.sale_agreement_no }}
                </template>
                <template slot="tgor_table:service_type" slot-scope="data">
                  {{ data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text }}
                </template>
                <template slot="tgor_table:facility" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == "Memorial Rooms"
                      ? data.item.sale_agreement_line_item.booking_line_item.room.room_no
                      : "--"
                  }}
                </template>
                <template slot="tgor_table:event" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == "Memorial Rooms"
                      ? data.item.sale_agreement_line_item.booking_line_item.event.reference_value_text
                      : "--"
                  }}
                </template>

                <template slot="tgor_table:rate" slot-scope="data">
                  <div v-if="data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == 'Memorial Rooms'">
                    <div
                      v-if="
                        data.item.sale_agreement_line_item.booking_line_item.room_type !== null &&
                        data.item.sale_agreement_line_item.booking_line_item.room_type.reference_value_text == 'Daily'
                      "
                    >
                      {{ +unFormatter(data.item.sale_agreement_line_item.booking_line_item.room.price_daily) | formatMoney }}
                    </div>
                    <div
                      v-else-if="
                        data.item.sale_agreement_line_item.booking_line_item.room_type !== null &&
                        data.item.sale_agreement_line_item.booking_line_item.room_type.reference_value_text == 'Hourly'
                      "
                    >
                      {{ +unFormatter(data.item.sale_agreement_line_item.booking_line_item.room.price_hourly) | formatMoney }}
                    </div>
                    <div v-else>$ 0</div>
                  </div>
                  <div v-else>--</div>
                </template>
                <template slot="tgor_table:period" slot-scope="data">
                  <div>
                    {{
                      data.item.sale_agreement_line_item.booking_line_item.check_in_date
                        ? customFormatter(data.item.sale_agreement_line_item.booking_line_item.check_in_date)
                        : ""
                    }}
                    {{
                      data.item.sale_agreement_line_item.booking_line_item.check_in_time
                        ? formatTime(data.item.sale_agreement_line_item.booking_line_item.check_in_time)
                        : ""
                    }}
                  </div>
                  <div>
                    {{
                      data.item.sale_agreement_line_item.booking_line_item.check_out_time
                        ? customFormatter(data.item.sale_agreement_line_item.booking_line_item.check_out_date)
                        : ""
                    }}
                    {{
                      data.item.sale_agreement_line_item.booking_line_item.check_out_time
                        ? formatTime(data.item.sale_agreement_line_item.booking_line_item.check_out_time)
                        : ""
                    }}
                  </div>
                </template>
                <template slot="tgor_table:day" slot-scope="data">
                  {{
                    calculateNumberOfDays(
                      data.item.sale_agreement_line_item.booking_line_item.check_in_date,
                      data.item.sale_agreement_line_item.booking_line_item.check_out_date
                    )
                  }}
                </template>
                <template slot="tgor_table:remarks" slot-scope="data">
                  {{ data.item.sale_agreement_line_item.booking_line_item.remarks || "--" }}
                </template>
                <template slot="tgor_table:booking_line_item.amount" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.amount && data.item.sale_agreement_line_item.booking_line_item.amount != ""
                      ? +unFormatter(data.item.sale_agreement_line_item.booking_line_item.amount)
                      : 0 | formatMoney
                  }}
                </template>
                <template slot="tgor_table:booking_line_item.tax_amount" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.tax_amount &&
                    data.item.sale_agreement_line_item.booking_line_item.tax_amount != ""
                      ? +unFormatter(data.item.sale_agreement_line_item.booking_line_item.tax_amount)
                      : 0 | formatMoney
                  }}
                  {{ gst ? `(${gst}%)` : "0%" }}
                </template>
                <template slot="tgor_table:booking_line_item.total_amount" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.total_amount
                      ? +unFormatter(data.item.sale_agreement_line_item.booking_line_item.total_amount)
                      : 0 | formatMoney
                  }}
                </template>
                <template slot="tgor_table:booking_line_item.discount" slot-scope="data">
                  <!-- <template  v-if=" data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == 'Niches'"> -->
                  <template
                    v-if="
                      data.item.sale_agreement_line_item.booking_line_item.discount_amount &&
                      data.item.sale_agreement_line_item.booking_line_item.discount_amount != ''
                    "
                  >
                    {{ +unFormatter(data.item.sale_agreement_line_item.booking_line_item.discount_amount) | formatMoney }}
                  </template>
                  <!-- </template> -->
                  <template v-else> -- </template>
                </template>
              </TableCustom>
            </div>
            <div v-if="invoiceDetail.sale_agreement.sale_agreement_type.reference_value_text === 'Additional Services'">
              <TableCustom
                @Items="getItems"
                :tableFields="columnActiveOther.fields"
                :tableItems="invoiceParams.invoice_line_item || []"
                :showFooter="true"
                :rowClass="tbodyTrClass"
                :colspan="4"
              >
                <template slot="tgor_table:agreement_id" slot-scope="data">
                  {{ data.item.sale_agreement.sale_agreement_no }}
                </template>
                <template slot="tgor_table:service_type" slot-scope="data">
                  {{ data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text }}
                </template>
                <template slot="tgor_table:remarks" slot-scope="data">
                  {{ data.item.sale_agreement_line_item.booking_line_item.remarks || "--" }}
                </template>
                <template slot="tgor_table:booking_line_item.amount" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.amount && data.item.sale_agreement_line_item.booking_line_item.amount != ""
                      ? +unFormatter(data.item.sale_agreement_line_item.booking_line_item.amount)
                      : 0 | formatMoney
                  }}
                </template>
                <template slot="tgor_table:booking_line_item.tax_amount" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.tax_amount &&
                    data.item.sale_agreement_line_item.booking_line_item.tax_amount != ""
                      ? +unFormatter(data.item.sale_agreement_line_item.booking_line_item.tax_amount)
                      : 0 | formatMoney
                  }}
                  {{ gst ? `(${gst}%)` : "0%" }}
                </template>
                <template slot="tgor_table:booking_line_item.total_amount" slot-scope="data">
                  {{
                    data.item.sale_agreement_line_item.booking_line_item.total_amount
                      ? +unFormatter(data.item.sale_agreement_line_item.booking_line_item.total_amount)
                      : 0 | formatMoney
                  }}
                </template>
                <template slot="tgor_table:booking_line_item.discount" slot-scope="data">
                  <!-- <template  v-if=" data.item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == 'Niches'"> -->
                  <template
                    v-if="
                      data.item.sale_agreement_line_item.booking_line_item.discount_amount &&
                      data.item.sale_agreement_line_item.booking_line_item.discount_amount != ''
                    "
                  >
                    {{ +unFormatter(data.item.sale_agreement_line_item.booking_line_item.discount_amount) | formatMoney }}
                  </template>
                  <!-- </template> -->
                  <template v-else> -- </template>
                </template>
              </TableCustom>
            </div>
          </template>
        </div>
      </div>
    </b-row>

    <!-- =================================================== End Table SaleAgreement ================================================= -->
    <div class="line-horizontal"></div>
    <!-- =================================================== Signture ================================================= -->
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
            <div class="no_line" v-if="invoiceParams.signature_tgor_officer">
              <b-img @click="officerClear" class="image" :src="invoiceParams.signature_tgor_officer" fluid alt="Responsive image"></b-img>
            </div>
            <div class="signture" v-else>
              <VueSignaturePad width="392px" height="117px" ref="signaturePadOfficer" />
              <b-img
                class="image"
                :class="{ 'd-none': invoiceParams.signature_tgor_officer }"
                @click="officerClear"
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
            <div class="no_line" v-if="invoiceParams.signature_client">
              <b-img @click="clientClear" class="image" :src="invoiceParams.signature_client" fluid alt="Responsive image"></b-img>
            </div>
            <div class="signture" v-else>
              <VueSignaturePad width="392px" height="117px" ref="signaturePadClient" />
              <b-img
                class="image"
                :class="{ 'd-none': invoiceParams.signature_client }"
                @click="clientClear"
                src="/images/edit.jpg"
                fluid
                alt="Responsive image"
              ></b-img>
            </div>
          </b-col>
        </b-row>
      </b-col>
    </b-row>
    <!-- =================================================== End Signture ================================================= -->
    <div class="line-horizontal"></div>
    <b-row class="title-sign"> Attachments </b-row>
    <b-row class="controll-attachment">
      <div>
        <b-img @click="showModal" class="image" src="/images/Create.png" fluid alt="Responsive image"></b-img>
      </div>
      <div>
        <b-img @click="deleteAtt" class="image" src="/images/trash.png" fluid alt="Responsive image"></b-img>
      </div>
      <div>
        <b-img @click="downloadAttach" class="image" src="/images/download.jpg" fluid alt="Responsive image"></b-img>
      </div>
    </b-row>
    <b-row class="file-attachment">
      <div v-for="(item, key) in listAttachment" :key="key">
        <b-form-checkbox @change="checkItem(item)" name="check-button">
          {{ item.attachable_file_name }}
        </b-form-checkbox>
      </div>
    </b-row>
    <b-modal centered ref="other_modal" hide-footer id="extension" size="sm" title="Add Attachments">
      <b-container fluid="lg">
        <b-row>
          <b-col cols="12">
            <b-form-group>
              <label class="_label_input">Upload File <span class="_require">*</span></label
              ><br />
              <b-form-file v-model="attachmentParams.file" class="mt-3" id="upload-photo" plain></b-form-file>
              <label for="upload-photo" class="btn-file-report">Select File</label>
              <div class="name-file">
                {{ attachmentParams.file && attachmentParams.file.name ? attachmentParams.file.name : "" }}
              </div>

              <div class="error" v-if="!$v.attachmentParams.file.required && $v.attachmentParams.file.$error">Field is required</div>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row class="btn-submit">
          <b-col cols="12">
            <div class="submit" @click="onSubmit">Submit</div>
          </b-col>
        </b-row>
      </b-container>
    </b-modal>
    <b-modal centered ref="add_discount" hide-footer id="extension" size="sm" title="Add Discount">
      <b-container fluid="lg">
        <b-row>
          <b-col cols="12">
            <div class="subtitle">Line Item Detail</div>
            <div class="text-normal">{{ titleDetail }}</div>
          </b-col>
        </b-row>
        <b-row class="mt">
          <b-col cols="12">
            <b-form-group label="Amount">
              <masked-input
                type="text"
                v-model="prmsDiscount.amount"
                class="form-control"
                :mask="numberAmountMask()"
                :guide="true"
                placeholder="$0.00"
                disabled="true"
              >
              </masked-input>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row class="mt">
          <b-col cols="12">
            <b-form-group label="Discount">
              <masked-input
                type="text"
                v-model="prmsDiscount.discount"
                class="form-control"
                :mask="numberMinusAmountMask()"
                :guide="true"
                placeholder="-$0.00"
              >
              </masked-input>
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
    <b-modal centered ref="AddLineItem" hide-footer id="extension" size="sm" title="Add Line item">
      <b-container fluid="lg">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Item">
              <multiselect
                v-model="lineItemParams.sale_agreement_id"
                :show-labels="false"
                deselect-label=""
                :options="listAgreement"
                placeholder="Select one"
                track-by="id"
                label="discount_code"
                :custom-label="ServiceWithCode"
              ></multiselect>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row class="btn-submit">
          <b-col cols="12">
            <div class="submit" @click="SubmitLineItem">Submit</div>
          </b-col>
        </b-row>
      </b-container>
    </b-modal>
  </b-container>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import Calendar from "@/components/Icons/Calendar";
import moment from "moment";
import TableCustom from "../../components/Table/paymentTable";
import Multiselect from "vue-multiselect";
import { mapActions, mapState } from "vuex";
import { required, minLength, between } from "vuelidate/lib/validators";
const accounting = require("accounting");
import createNumberMask from "text-mask-addons/dist/createNumberMask";
import MaskedInput from "vue-text-mask";
import ControllTable from "../customViews/controllTable.vue";
import _ from "lodash";
import { InvoiceNiche, InvoiceRoom, InvoiceOther } from "@/enums/invoiceTableHeader";
export default {
  name: "",
  components: {
    Datepicker,
    Calendar,
    TableCustom,
    Multiselect,
    MaskedInput,
    ControllTable,
    InvoiceNiche,
    InvoiceRoom,
    InvoiceOther,
  },
  data() {
    return {
      attachmentParams: {
        file: "",
      },
      invoiceParams: {
        booking_id: "",
        invoice_no: "",
        invoice_date: "",
        sale_agreement_id: "",
        user_id: "",
        client_name: "",
        phone: "",
        email: "",
        contact: "",
        remarks: "",
        invoice_line_item: "",
        signature_client: "",
        signature_tgor_officer: "",
        discount_id: "",
        discount_list: [],
        discount_custom: [],
        is_payment: false,
        officer: "",
        gst_id: "",
      },
      fakeDiscount: "",
      tabIndex: 0,
      activeFilter: false,
      activeStatus: "Active",
      activeClass: false,
      allSelected: false,
      selected: [],
      ids: [],
      service_id: [],
      isSave: false,
      prmsDiscount: {
        id: "",
        amount: "",
        discount: "",
        type: "",
        sale_id: "",
      },
      typeModel: "",
      titleDetail: "",
      lineItemParams: {
        id: this.$router.history.current.params.id,
        sale_agreement_id: "",
      },
      listService: [
        { id: 1, type: "Niche" },
        { id: 2, type: "Room" },
        { id: 3, type: "Additional Service" },
      ],
      listSale: [],
      columnActive: InvoiceNiche,
      columnActiveRoom: InvoiceRoom,
      columnActiveOther: InvoiceOther,
      optionsFilter: [],
      gst: null,
      gstNumber: 0,
    };
  },
  validations: {
    attachmentParams: {
      file: {
        required,
      },
    },
  },
  computed: mapState({
    totalDetail: (state) => state.saleareement.totalDetail,
    invoiceDetail: (state) => state.invoice.invoiceDetail,
    listAttachment: (state) => state.attachment.listAttachment,
    listAgreement: (state) => state.saleareement.listAgreement,
  }),
  created() {
    this.$store.commit("saleareement/emptySumTotal");
    this.handleListAttachment();
    this.$nextTick(function () {
      this.$refs.signaturePadOfficer.resizeCanvas();
      this.$refs.signaturePadClient.resizeCanvas();
    });
    this.gstdetail()
      .then((res) => {
        this.gst = Math.floor(res.data.data.rate * 100);
        this.gstNumber = this.gstNumber = res.data.data.rate;
      })
      .catch((error) => {});
  },
  methods: {
    ...mapActions({
      getSumTotal: "saleareement/getSumTotal",
      addAttachment: "attachment/addAttachment",
      getListAttachment: "attachment/getListAttachment",
      deleteAttachment: "attachment/deleteAttachment",
      downloadAttachment: "attachment/downloadAttachment",
      saveSignInvoices: "invoice/saveSignInvoices",
      generatePayment: "payment/generatePayment",
      emptySumTotal: "saleareement/emptySumTotal",
      listAgreementForInvoice: "saleareement/listAgreementForInvoice",
      addAgreementForInvoice: "saleareement/addAgreementForInvoice",
      getDetailInvoices: "invoice/getDetailInvoices",
      deleteAgreementForInvoice: "saleareement/deleteAgreementForInvoice",
      getTotalInvoice: "invoice/getTotalInvoice",
      gstdetail: "gst/gstdetail",
    }),
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    formatTime(time) {
      return moment(time, "HH:mm:ss").format("HH:mm");
    },
    unFormatter(val) {
      return accounting.unformat(val);
    },
    getItems(item) {
      this.ids = item;
      this.service_id = item;
      this.sumTotalAmount();
      // let prms = {
      //   type: 2,
      //   arr_id: this.service_id,
      // };
      // this.getSumTotal(prms).then((res) => {});
    },
    showModal(item) {
      this.$v.attachmentParams.$reset();
      this.attachmentParams.file = {};
      this.$refs.other_modal.show();
    },
    onSubmit() {
      this.$v.attachmentParams.$touch();
      if (this.$v.attachmentParams.$anyError) {
        return;
      }

      var formData = new FormData();

      formData.append("id", this.$router.history.current.params.id);
      formData.append("type", 1);
      formData.append("file", this.attachmentParams.file);
      this.addAttachment(formData).then((res) => {
        this.$refs.other_modal.hide();
        this.$swal({
          icon: "success",
          title: "Success!",
          text: res.data.status,
        });
        this.handleListAttachment();
      });
    },
    handleListAttachment() {
      let prms = {
        id: this.$router.history.current.params.id,
        type: 1,
      };
      this.getListAttachment(prms).then((res) => {});
    },
    checkItem(item) {
      if (this.ids.includes(item.id)) {
        var index = this.ids.indexOf(item.id);
        if (index >= 0) {
          this.ids.splice(index, 1);
        }
      } else {
        this.ids.push(item.id);
      }
    },
    deleteAtt() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find Attachment.",
        });
      } else {
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
            let prms = { ids: this.ids };
            this.deleteAttachment(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handleListAttachment(this.reportParams);
              })
              .catch((error) => {
                this.$swal({
                  icon: "error",
                  title: "Oops...",
                  text: error.response.data.errors,
                });
              });
          }
        });
      }
    },
    downloadAttach() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find Attachment.",
        });
        return;
      }
      let prms = {
        id: this.$router.history.current.params.id,
        arr_id: this.ids,
        type: 1,
      };
      this.downloadAttachment(prms).then((res) => {
        res.map((item, key) => {
          return new Promise((resolve, reject) => {
            return axios
              .request({
                baseURL: `${window.location.origin}/`,
                method: "GET",
                url: item.full_url,
                responseType: "blob",
              })
              .then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", item.attachable_file_name);
                document.body.appendChild(link);
                link.click();
              });
          });
        });
      });
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
    getSignOfficer() {
      if (this.invoiceParams.signature_tgor_officer == null) {
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
      if (this.invoiceParams.signature_client == null) {
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
        if (this.invoiceParams.signature_tgor_officer == null) {
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

      if (this.invoiceParams.remarks != "") {
        formData.append("remarks", this.invoiceParams.remarks);
      }
      formData.append("discount_custom", JSON.stringify(this.invoiceParams.discount_custom));

      if (this.invoiceParams.discount_id) {
        formData.append("discount_id", this.invoiceParams.discount_id.id);
      }

      let prms = {
        id: this.$router.history.current.params.id,
        data: formData,
      };
      this.saveSignInvoices(prms)
        .then((res) => {
          this.getDetailInvoices({
            id: this.$router.history.current.params.id,
          });
          let invoiceDetail = { ...this.invoiceDetail };
          let arr_ids = [];
          invoiceDetail.invoice_line_item.map((item) => {
            if (item.sale_agreement_line_item.booking_line_item.id) {
              arr_ids.push(item.sale_agreement_line_item.booking_line_item.id);
            }
          });
          if (arr_ids.length) {
            let prms = {
              invoice_id: this.$router.history.current.params.id,
              arr_id: arr_ids,
            };
            this.getTotalInvoice(prms);
          }
          this.$swal({
            icon: "success",
            title: "Success!",
            text: res.data.status,
          });
          (this.invoiceParams.signature_client = res.data.data.signature_client),
            (this.invoiceParams.signature_tgor_officer = res.data.data.signature_tgor_officer);
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
    onGeneratePay() {
      if (!this.isSave) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Please save the information before generate payment.",
        });
        return;
      }
      if (this.service_id.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Please select services",
        });
        return;
      }
      let prms = {
        id: this.$router.history.current.params.id,
        booking_id: this.invoiceParams.booking_id,
        service_ids: this.service_id,
        total_amount: this.unformatter(this.totalDetail.amount),
        total_tax_amount: this.unformatter(this.totalDetail.gst_amount),
        total: this.unformatter(this.totalDetail.total_amount),
        total_discount: this.unFormatter(this.totalDetail.discount),
        user_id: this.invoiceParams.user_id,
      };
      this.generatePayment(prms)
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
      if (item != null && item.is_payment == 1) {
        return "hidden-row";
      }
      return "";
    },
    addDiscountToLine(items) {
      if (
        (typeof this.invoiceParams?.discount_id?.id !== "undefined" &&
          this.invoiceParams?.discount_id?.id !== "" &&
          items.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text === "Niches") ||
        !!this.invoiceParams.is_payment
      ) {
        return;
      }
      switch (items.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text) {
        case "Niches":
          this.titleDetail = `Booking Niche - ${items.sale_agreement_line_item.booking_line_item.niche.reference_no}`;
          this.typeModel = "niches";
          break;
        case "Additional Services":
          this.titleDetail = `${items.sale_agreement_line_item.booking_line_item.other.service_name} - ${items.sale_agreement_line_item.booking_line_item.service_type?.service_name}`;
          this.typeModel = "additional_services";
          break;
        case "Memorial Rooms":
          this.titleDetail = `Booking Memorial Room - ${items.sale_agreement_line_item.booking_line_item.room.room_no}`;
          this.typeModel = "room";
          break;
        default:
          this.titleDetail = "";
          this.typeModel = "";
          break;
      }
      this.prmsDiscount.id = items.id;
      this.prmsDiscount.sale_id = items.sale_agreement_id;
      this.prmsDiscount.amount = items.sale_agreement_line_item.booking_line_item.amount_format;
      this.prmsDiscount.discount = "";
      this.prmsDiscount.type = this.typeModel;
      this.$refs.add_discount.show();
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
    onChangeSearch() {},
    onCreateLineItem() {
      this.lineItemParams.sale_agreement_id = "";
      if (!this.invoiceParams.is_payment) {
        let prms = { id: this.invoiceParams.user_id };
        this.listAgreementForInvoice(prms);
        this.$refs.AddLineItem.show();
      }
    },
    deleteItem() {
      // deleteAgreementForInvoice
      if (!this.invoiceParams.is_payment)
        if (this.ids.length == 0) {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: "Can not find service.",
          });
        } else {
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
              let prms = { type: "Niche", ids: this.ids };
              this.deleteAgreementForInvoice(prms)
                .then((res) => {
                  let prms = { id: this.$router.history.current.params.id };
                  this.getDetailInvoices(prms);
                  this.$swal({
                    icon: "success",
                    title: "Success!",
                    text: res.data.status,
                  });
                  this.ids = [];
                  // this.handlePanigate(this.serviceNicheParams)
                  this.$store.commit("saleareement/emptySumTotal");
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
        }
    },
    ServiceWithCode(item) {
      let Code = "";
      switch (item.sale_agreement_item.booking_line_item.booking_type.reference_value_text) {
        case "Memorial Rooms":
          Code = item.sale_agreement_item.booking_line_item.room.room_no;
          break;
        case "Niches":
          Code = item.sale_agreement_item.booking_line_item.niche.reference_no;
          break;
        case "Additional Services":
          Code = item.sale_agreement_item.booking_line_item.other.service_name;
          break;
        default:
          break;
      }
      if (item.sale_agreement_item.booking_line_item.booking_type.reference_value_text === "Memorial Rooms") {
        Code = item.sale_agreement_item.booking_line_item.room.room_no;
      }
      return `${item.sale_agreement_no} — ${Code}`;
    },
    SubmitLineItem() {
      let prmsAdd = { ...this.lineItemParams };
      prmsAdd.sale_agreement_id = this.lineItemParams.sale_agreement_id.sale_agreement_item.sale_agreement_id;
      this.addAgreementForInvoice(prmsAdd)
        .then((res) => {
          let prms = { id: this.$router.history.current.params.id };
          this.getDetailInvoices(prms).then((res) => {
            this.$refs.AddLineItem.hide();
            this.$swal({
              icon: "success",
              title: "Success!",
              text: "Successfully Added Sale Agreement.",
            });
          });
        })
        .catch((error) => {});
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
      }
      this.invoiceParams.invoice_line_item.map((item, i) => {
        if (item.id == prms.id) {
          let total =
            parseFloat(item.sale_agreement_line_item.booking_line_item.amount) +
            parseFloat(item.sale_agreement_line_item.booking_line_item.tax_amount);
          this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.discount_amount = discount;
          this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.get_discount = discount;
          this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.total_amount = total - discount;
        }
      });
      this.$refs.add_discount.hide();

      let discountObject = {
        sale_id: prms.sale_id,
        type: prms.type,
        discount_amount: discount,
      };
      if (this.invoiceParams.discount_custom?.length > 0) {
        this.invoiceParams.discount_custom.map((data, key) => {
          if (data.sale_id === discountObject.sale_id) {
            this.invoiceParams.discount_custom.splice(key, 1);
          }
        });
      }
      this.invoiceParams.discount_custom.push(discountObject);
      this.sumTotalAmount();
    },
    removeDiscountNiche() {
      if (this.invoiceParams.discount_custom?.length > 0) {
        _.remove(this.invoiceParams.discount_custom, function (data) {
          return data.type === "niches";
        });
      }
    },
    sumTotalAmount() {
      var arr_invoice_line_item = [];
      this.invoiceParams.invoice_line_item.map((item, i) => {
        let isCheck = this.ids.includes(item.id);
        if (isCheck) {
          arr_invoice_line_item.push(item);
        }
      });
      var totalDetail = {
        amount: 0,
        gst_amount: 0,
        discount: 0,
        total_amount: 0,
      };

      arr_invoice_line_item.map((item, i) => {
        totalDetail.amount += parseFloat(item.sale_agreement_line_item.booking_line_item.amount);
        totalDetail.gst_amount += parseFloat(item.sale_agreement_line_item.booking_line_item.tax_amount);
        totalDetail.total_amount += parseFloat(this.unformatter(item.sale_agreement_line_item.booking_line_item.total_amount));
        let discountAmount =
          item.sale_agreement_line_item.booking_line_item.discount_amount !== null
            ? Number.isInteger(item.sale_agreement_line_item.booking_line_item.discount_amount)
              ? item.sale_agreement_line_item.booking_line_item.discount_amount
              : parseFloat(item.sale_agreement_line_item.booking_line_item.discount_amount)
            : 0;
        totalDetail.discount += discountAmount;
      });
      this.$store.commit("saleareement/updateTotal", totalDetail);
    },
    handleDefaultGst() {
      this.invoiceParams?.invoice_line_item.map((item, i) => {
        let discount = this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.discount_amount;
        let total = parseFloat(item.sale_agreement_line_item.booking_line_item.amount) - discount;
        let newDiscount = this.gstNumber * total;

        this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.total_amount = total + newDiscount;
      });
      this.sumTotalAmount();
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
    invoiceDetail: function (val) {
      this.ids = val.invoice_line_item.map((item) => item.id);
      this.service_id = val.invoice_line_item.map((item) => item.id);
      this.invoiceParams.invoice_no = val.invoice_no;
      this.invoiceParams.invoice_date = this.customFormatter(val.invoice_date);
      this.invoiceParams.client_name = val.client.display_name;
      this.invoiceParams.phone = val.client.phone;
      this.invoiceParams.email = val.client.email;
      this.invoiceParams.contact = val.client.preferred_contact_by ? val.client.preferred_contact_by.reference_value_text : null;
      this.invoiceParams.remarks = val.remarks;
      this.invoiceParams.invoice_line_item = val.invoice_line_item;
      // (this.invoiceParams.sale_agreement_id =
      //   val.sale_agreement.sale_agreement_no),
      // (this.invoiceParams.booking_id = val.sale_agreement.booking_id),
      this.invoiceParams.user_id = val.client.id;
      this.invoiceParams.discount_list = val.discount_list;
      this.invoiceParams.discount_id = val.discount;
      this.invoiceParams.signature_client = val.signature_client;
      this.invoiceParams.signature_tgor_officer = val.signature_tgor_officer;
      this.invoiceParams.is_payment = val.is_payment;
      this.invoiceParams.gst_id = val.sale_agreement?.gst_id || null;
      if (this.invoiceParams.gst_id) {
        let prms = {
          gst_id: this.invoiceParams.gst_id,
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
      if (val.admin) {
        this.invoiceParams.officer = val.admin.display_name;
      }
      // if(val.is_payment){
      //   this.showIconAdd = false;
      //   this.showIconTrash = false;
      // }
      if (val.remarks && val.signature_tgor_officer != null) {
        this.$emit("isSave", true);
        this.isSave = true;
      }
      this.sumTotalAmount();
    },
    "invoiceParams.discount_id": function (val) {
      if (val != null && val != "" && typeof val !== "undefined") {
        this.invoiceParams.invoice_line_item.map((item, i) => {
          // this.invoiceParams.invoice_line_item.sale_agreement_item[i].booking_line_item.discount_amount = val.amount;
          if (item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == "Niches") {
            if (val.type_amount.reference_value_text == "Value") {
              let total =
                parseFloat(item.sale_agreement_line_item.booking_line_item.amount) +
                parseFloat(item.sale_agreement_line_item.booking_line_item.tax_amount);
              this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.discount_amount = val.amount;
              this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.get_discount = val.amount;
              this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.total_amount = total - val.amount;
            } else {
              let total =
                parseFloat(item.sale_agreement_line_item.booking_line_item.amount) +
                parseFloat(item.sale_agreement_line_item.booking_line_item.tax_amount);
              let money = parseFloat(item.sale_agreement_line_item.booking_line_item.amount) * val.percent;
              this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.discount_amount = money;
              this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.get_discount = money;
              this.invoiceParams.invoice_line_item[i].sale_agreement_line_item.booking_line_item.total_amount = total - money;
            }
          }
        });
        this.removeDiscountNiche();
        this.sumTotalAmount();
      } else {
        this.handleDefaultGst();
      }
    },
    gstNumber: function (val) {
      this.handleDefaultGst();
    },
  },
};
</script>
<style lang="css" scoped>
label {
  cursor: pointer;
}

#upload-photo {
  opacity: 0;
  position: absolute;
  z-index: -1;
}
.btn-file-report {
  padding: 0 17px;
  border: 1px solid #000000;
  box-sizing: border-box;
  border-radius: 5px;
  line-height: 2.5 !important;
}
.name-file {
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
