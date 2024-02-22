<template>
  <b-container fluid="lg" class="payment-info-components">
    <div class="formInput">
      <b-row>
        <b-col cols="3">
          <b-form-group label="Receipt #">
            <b-form-input :disabled="true" v-model="paymentParams.payment_no" class="input-form"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Date">
            <div class="position-relative input-date">
              <b-form-input :disabled="true" v-model="paymentParams.payment_date" class="input-form"></b-form-input>
            </div>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Invoice #">
            <b-form-input :disabled="true" class="input-form" v-model="paymentParams.invoice_id"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="TGOR Officer">
            <b-form-input :disabled="true" class="input-form" v-model="paymentParams.officer"></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row class="mt">
        <b-col cols="3">
          <b-form-group label="Client Name">
            <b-form-input :disabled="true" class="input-form" v-model="paymentParams.client_name"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Mobile">
            <b-form-input :disabled="true" class="input-form" v-model="paymentParams.phone"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Email">
            <b-form-input :disabled="true" class="input-form" v-model="paymentParams.email"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="3">
          <b-form-group label="Preferred Contact by">
            <b-form-input :disabled="true" class="input-form" v-model="paymentParams.contact"></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row class="mt">
        <b-col cols="3">
          <b-form-group label="Payment Mode">
            <multiselect
              :show-labels="false"
              deselect-label=""
              :class="{
                'form-group--error': $v.paymentParams.payment_mode_id.$error,
              }"
              v-model="$v.paymentParams.payment_mode_id.$model"
              :options="listPaymentMode"
              placeholder="Select one"
              track-by="id"
              label="reference_value_text"
            ></multiselect>
            <div class="error" v-if="!$v.paymentParams.payment_mode_id.required && $v.paymentParams.payment_mode_id.$error">Field is required</div>
          </b-form-group>
        </b-col>
        <!-- ========= isCheque ========= -->
        <b-col cols="3" :class="{ 'd-none': isCheque }">
          <b-form-group label="Cheque #">
            <b-form-input
              class="input-form"
              :class="{ 'form-group--error': $v.paymentParams.cheque.$error }"
              v-model="$v.paymentParams.cheque.$model"
            ></b-form-input>
            <div class="error" v-if="!$v.paymentParams.cheque.required && $v.paymentParams.cheque.$error">Field is required</div>
          </b-form-group>
        </b-col>
        <b-col cols="3" :class="{ 'd-none': isCheque }">
          <b-form-group label="Bank">
            <b-form-input
              class="input-form"
              :class="{
                'form-group--error': $v.paymentParams.cheque_bank.$error,
              }"
              v-model="$v.paymentParams.cheque_bank.$model"
            ></b-form-input>
            <div class="error" v-if="!$v.paymentParams.cheque_bank.required && $v.paymentParams.cheque_bank.$error">Field is required</div>
          </b-form-group>
        </b-col>
        <!-- ========= isBank ========= -->
        <b-col cols="3" :class="{ 'd-none': isBank }">
          <b-form-group label="Transaction #">
            <b-form-input
              class="input-form"
              :class="{
                'form-group--error': $v.paymentParams.transaction.$error,
              }"
              v-model="$v.paymentParams.transaction.$model"
            ></b-form-input>
            <div class="error" v-if="!$v.paymentParams.transaction.required && $v.paymentParams.transaction.$error">Field is required</div>
          </b-form-group>
        </b-col>
        <b-col cols="6" :class="{ 'd-none': isBank }">
          <b-form-group label="Remarks">
            <b-form-input class="input-form" v-model="paymentParams.remarks"></b-form-input>
            <!-- <div
              class="error"
              v-if="
                !$v.paymentParams.remarks.required &&
                  $v.paymentParams.remarks.$error
              "
            >
              Field is required
            </div> -->
          </b-form-group>
        </b-col>
        <!-- ========= isCash ========= -->
        <b-col cols="6" :class="{ 'd-none': isCash && isPayNow }">
          <b-form-group label="Remarks">
            <b-form-input class="input-form" v-model="paymentParams.remarks"></b-form-input>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row class="mt">
        <b-col cols="6" :class="{ 'd-none': isCheque }">
          <b-form-group label="Remarks">
            <b-form-input class="input-form" v-model="paymentParams.remarks"></b-form-input>
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
          <template v-if="paymentDetail.invoice.sale_agreement.sale_agreement_type !== null">
            <div v-if="paymentDetail.invoice.sale_agreement.sale_agreement_type.reference_value_text === 'Niches'">
              <TableCustom
                :tableFields="columnActive.fields"
                :tableItems="paymentParams.payment_line_item || []"
                :showFooter="true"
                :gstNumber="gst"
                :colspan="7"
              >
                <template slot="tgor_table:invoice_no" slot-scope="data">
                  {{ data.item.invoice.invoice_no || "--" }}
                </template>
                <template slot="tgor_table:service_type" slot-scope="data">
                  {{ data.item.line_item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text }}
                </template>

                <template slot="tgor_table:location" slot-scope="data">
                  {{
                    data.item.line_item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == "Niches"
                      ? data.item.line_item.sale_agreement_line_item.booking_line_item.niche.location
                      : "--"
                  }}
                </template>
                <template slot="tgor_table:detail" slot-scope="data">
                  <div v-if="data.item.line_item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == 'Niches'">
                    {{
                      data.item.line_item.sale_agreement_line_item.booking_line_item.niche.type !== null
                        ? `${data.item.line_item.sale_agreement_line_item.booking_line_item.niche.type.reference_value_text} - `
                        : ""
                    }}
                    {{
                      data.item.line_item.sale_agreement_line_item.booking_line_item.niche.category !== null
                        ? `${data.item.line_item.sale_agreement_line_item.booking_line_item.niche.category.reference_value_text}`
                        : ""
                    }}
                  </div>
                  <div v-else>--</div>
                </template>
                <template slot="tgor_table:lease" slot-scope="data">
                  <div v-if="data.item.line_item.sale_agreement_line_item.booking_line_item.lease_start_date !== null">
                    {{ customFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.lease_start_date) }}
                  </div>
                  <div v-if="data.item.line_item.sale_agreement_line_item.booking_line_item.lease_expiry_date !== null">
                    {{ customFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.lease_expiry_date) }}
                  </div>
                  <div
                    v-if="
                      data.item.line_item.sale_agreement_line_item.booking_line_item.lease_expiry_date == null &&
                      data.item.line_item.sale_agreement_line_item.booking_line_item.lease_start_date == null
                    "
                  >
                    --
                  </div>
                </template>
                <template slot="tgor_table:niches" slot-scope="data">
                  {{
                    data.item.line_item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == "Niches"
                      ? data.item.line_item.sale_agreement_line_item.booking_line_item.niche.reference_no
                      : "--"
                  }}
                </template>
                <template slot="tgor_table:remarks" slot-scope="data">
                  {{ data.item.line_item.sale_agreement_line_item.booking_line_item.remarks || "--" }}
                </template>
                <template slot="tgor_table:booking_line_item.amount" slot-scope="data">
                  {{
                    data.item.line_item.sale_agreement_line_item.booking_line_item.amount &&
                    data.item.line_item.sale_agreement_line_item.booking_line_item.amount != ""
                      ? +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.amount)
                      : 0 | formatMoney
                  }}
                </template>
                <template slot="tgor_table:booking_line_item.tax_amount" slot-scope="data">
                  {{
                    data.item.line_item.sale_agreement_line_item.booking_line_item.tax_amount &&
                    data.item.line_item.sale_agreement_line_item.booking_line_item.tax_amount != ""
                      ? +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.tax_amount)
                      : 0 | formatMoney
                  }}
                  {{ gst ? `(${gst}%)` : "0%" }}
                </template>
                <template slot="tgor_table:booking_line_item.total_amount" slot-scope="data">
                  <template
                    v-if="
                      data.item.line_item.sale_agreement_line_item.booking_line_item.amount &&
                      data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount
                    "
                  >
                    {{ handleDefaultTotal(data) | formatMoney }}
                  </template>
                  <template v-else>
                    {{ handleDefaultTotal(data) | formatMoney }}
                  </template>
                </template>
                <template slot="tgor_table:booking_line_item.discount" slot-scope="data">
                  <template
                    v-if="
                      data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount &&
                      data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount != ''
                    "
                  >
                    {{ +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount) | formatMoney }}
                  </template>
                  <template v-else> -- </template>
                </template>
              </TableCustom>
            </div>
            <div v-if="paymentDetail.invoice.sale_agreement.sale_agreement_type.reference_value_text === 'Memorial Rooms'">
              <TableCustom
                :tableFields="columnActiveRoom.fields"
                :tableItems="paymentParams.payment_line_item || []"
                :showFooter="true"
                :gstNumber="gst"
                :colspan="10"
              >
                <template slot="tgor_table:invoice_no" slot-scope="data">
                  {{ data.item.invoice.invoice_no || "--" }}
                </template>
                <template slot="tgor_table:service_type" slot-scope="data">
                  {{ data.item.line_item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text }}
                </template>

                <template slot="tgor_table:facility" slot-scope="data">
                  {{
                    data.item.line_item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == "Memorial Rooms"
                      ? data.item.line_item.sale_agreement_line_item.booking_line_item.room.room_no
                      : "--"
                  }}
                </template>
                <template slot="tgor_table:event" slot-scope="data">
                  {{
                    data.item.line_item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == "Memorial Rooms"
                      ? data.item.line_item.sale_agreement_line_item.booking_line_item.event.reference_value_text
                      : "--"
                  }}
                </template>
                <template slot="tgor_table:rate" slot-scope="data">
                  <div v-if="data.item.line_item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text == 'Memorial Rooms'">
                    <div
                      v-if="
                        data.item.line_item.sale_agreement_line_item.booking_line_item.room_type !== null &&
                        data.item.line_item.sale_agreement_line_item.booking_line_item.room_type.reference_value_text == 'Daily'
                      "
                    >
                      {{ +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.room.price_daily) | formatMoney }}
                    </div>
                    <div
                      v-else-if="
                        data.item.line_item.sale_agreement_line_item.booking_line_item.room_type !== null &&
                        data.item.line_item.sale_agreement_line_item.booking_line_item.room_type.reference_value_text == 'Hourly'
                      "
                    >
                      {{ +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.room.price_hourly) | formatMoney }}
                    </div>
                    <div v-else>$ 0</div>
                  </div>
                  <div v-else>--</div>
                </template>
                <template slot="tgor_table:period" slot-scope="data">
                  <div>
                    {{
                      data.item.line_item.sale_agreement_line_item.booking_line_item.check_in_date
                        ? customFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.check_in_date)
                        : ""
                    }}
                    {{
                      data.item.line_item.sale_agreement_line_item.booking_line_item.check_in_time
                        ? formatTime(data.item.line_item.sale_agreement_line_item.booking_line_item.check_in_time)
                        : ""
                    }}
                  </div>
                  <div>
                    {{
                      data.item.line_item.sale_agreement_line_item.booking_line_item.check_out_time
                        ? customFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.check_out_date)
                        : ""
                    }}
                    {{
                      data.item.line_item.sale_agreement_line_item.booking_line_item.check_out_time
                        ? formatTime(data.item.line_item.sale_agreement_line_item.booking_line_item.check_out_time)
                        : ""
                    }}
                  </div>
                </template>
                <template slot="tgor_table:day" slot-scope="data">
                  {{
                    calculateNumberOfDays(
                      data.item.line_item.sale_agreement_line_item.booking_line_item.check_in_date,
                      data.item.line_item.sale_agreement_line_item.booking_line_item.check_out_date
                    )
                  }}
                </template>
                <template slot="tgor_table:remarks" slot-scope="data">
                  {{ data.item.line_item.sale_agreement_line_item.booking_line_item.remarks || "--" }}
                </template>
                <template slot="tgor_table:booking_line_item.amount" slot-scope="data">
                  {{
                    data.item.line_item.sale_agreement_line_item.booking_line_item.amount &&
                    data.item.line_item.sale_agreement_line_item.booking_line_item.amount != ""
                      ? +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.amount)
                      : 0 | formatMoney
                  }}
                </template>
                <template slot="tgor_table:booking_line_item.tax_amount" slot-scope="data">
                  {{
                    data.item.line_item.sale_agreement_line_item.booking_line_item.tax_amount &&
                    data.item.line_item.sale_agreement_line_item.booking_line_item.tax_amount != ""
                      ? +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.tax_amount)
                      : 0 | formatMoney
                  }}
                  {{ gst ? `(${gst}%)` : "0%" }}
                </template>
                <template slot="tgor_table:booking_line_item.total_amount" slot-scope="data">
                  <template
                    v-if="
                      data.item.line_item.sale_agreement_line_item.booking_line_item.amount &&
                      data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount
                    "
                  >
                    {{ handleDefaultTotal(data) | formatMoney }}
                  </template>
                  <template v-else>
                    {{ handleDefaultTotal(data) | formatMoney }}
                  </template>
                </template>
                <template slot="tgor_table:booking_line_item.discount" slot-scope="data">
                  <template
                    v-if="
                      data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount &&
                      data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount != ''
                    "
                  >
                    {{ +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount) | formatMoney }}
                  </template>
                  <template v-else> -- </template>
                </template>
              </TableCustom>
            </div>
            <div v-if="paymentDetail.invoice.sale_agreement.sale_agreement_type.reference_value_text === 'Additional Services'">
              <TableCustom
                :tableFields="columnActiveOther.fields"
                :tableItems="paymentParams.payment_line_item || []"
                :showFooter="true"
                :gstNumber="gst"
                :colspan="4"
              >
                <template slot="tgor_table:invoice_no" slot-scope="data">
                  {{ data.item.invoice.invoice_no || "--" }}
                </template>
                <template slot="tgor_table:service_type" slot-scope="data">
                  {{ data.item.line_item.sale_agreement_line_item.booking_line_item.booking_type.reference_value_text }}
                </template>
                <template slot="tgor_table:remarks" slot-scope="data">
                  {{ data.item.line_item.sale_agreement_line_item.booking_line_item.remarks || "--" }}
                </template>
                <template slot="tgor_table:booking_line_item.amount" slot-scope="data">
                  {{
                    data.item.line_item.sale_agreement_line_item.booking_line_item.amount &&
                    data.item.line_item.sale_agreement_line_item.booking_line_item.amount != ""
                      ? +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.amount)
                      : 0 | formatMoney
                  }}
                </template>
                <template slot="tgor_table:booking_line_item.tax_amount" slot-scope="data">
                  {{
                    data.item.line_item.sale_agreement_line_item.booking_line_item.tax_amount &&
                    data.item.line_item.sale_agreement_line_item.booking_line_item.tax_amount != ""
                      ? +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.tax_amount)
                      : 0 | formatMoney
                  }}
                  {{ gst ? `(${gst}%)` : "0%" }}
                </template>
                <template slot="tgor_table:booking_line_item.total_amount" slot-scope="data">
                  <template
                    v-if="
                      data.item.line_item.sale_agreement_line_item.booking_line_item.amount &&
                      data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount
                    "
                  >
                    {{ handleDefaultTotal(data) | formatMoney }}
                  </template>
                  <template v-else>
                    {{ handleDefaultTotal(data) | formatMoney }}
                  </template>
                </template>
                <template slot="tgor_table:booking_line_item.discount" slot-scope="data">
                  <template
                    v-if="
                      data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount &&
                      data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount != ''
                    "
                  >
                    {{ +unFormatter(data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount) | formatMoney }}
                  </template>
                  <template v-else> -- </template>
                </template>
              </TableCustom>
            </div>
          </template>
          <template v-if="paymentParams.partial_payment.length">
            <SubTablePayment
              :tableFields="columnActive2.fields"
              :tableItems="paymentParams.partial_payment || []"
              :showFooter="true"
              :gstNumber="gstNumber"
              @checkPayment2="checkPayment2"
            >
              <template slot="tgor_table:key1" slot-scope="data">
                <router-link
                  class="ml-3"
                  :to="{
                    name: 'PartialPaymentInfo',
                    params: {
                      id: data.item.payment_id,
                      id_partial: data.item.id,
                    },
                  }"
                >
                  Receipt {{ data.index + 1 }}
                </router-link>
              </template>
              <template slot="tgor_table:data" slot-scope="data">
                <span class="pl-3">{{ data.item.amount | formatMoney }}</span>
              </template>
            </SubTablePayment>
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
            <span> Officer </span>
          </b-col>
          <b-col cols="8">
            <div class="no_line" v-if="paymentParams.signature_tgor_officer">
              <b-img class="image" :src="paymentParams.signature_tgor_officer" fluid alt="Responsive image"></b-img>
            </div>
            <div class="signture" v-else>
              <VueSignaturePad width="392px" height="117px" ref="signaturePadOfficer" />
              <b-img
                @click="officerClear"
                :class="{ 'd-none': paymentParams.signature_tgor_officer }"
                class="image"
                src="/images/edit.jpg"
                fluid
                alt="Responsive image"
              ></b-img>
            </div>
          </b-col>
        </b-row>
      </b-col>
      <!-- <b-col cols="6">
        <b-row>
          <b-col cols="2">
            <span class="st-icon-pandora">
              Client
            </span>
          </b-col>
          <b-col cols="8">
            <div class="no_line" v-if="paymentParams.signature_client">
              <b-img
                @click="clientClear"
                class="image"
                :src="paymentParams.signature_client"
                fluid
                alt="Responsive image"
              ></b-img>
            </div>
            <div class="signture" v-else>
              <VueSignaturePad
                width="392px"
                height="117px"
                ref="signaturePadClient"
              />
              <b-img
                @click="clientClear"
                :class="{ 'd-none': paymentParams.signature_client }"
                class="image"
                src="/images/edit.jpg"
                fluid
                alt="Responsive image"
              ></b-img>
            </div>
          </b-col>
        </b-row>
      </b-col> -->
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
              {{ attachmentParams.file && attachmentParams.file.name ? attachmentParams.file.name : "" }}
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
  </b-container>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import Calendar from "@/components/Icons/Calendar";
import Multiselect from "vue-multiselect";
import moment from "moment";
import TableCustom from "../../components/Table/paymentInfoTable";
import SubTablePayment from "../../components/Table/subTablePayment";
const accounting = require("accounting");
import { required, minLength, between } from "vuelidate/lib/validators";
import { mapActions, mapState } from "vuex";
import { isNull } from "lodash";
import { ReceiptNiche, ReceiptRoom, ReceiptOther } from "@/enums/receiptTableHeader";
export default {
  name: "",
  components: {
    Datepicker,
    Calendar,
    TableCustom,
    Multiselect,
    SubTablePayment,
    ReceiptNiche,
    ReceiptRoom,
  },
  data() {
    return {
      attachmentParams: {
        file: "",
      },
      paymentParams: {
        booking_id: "",
        payment_no: "",
        payment_date: "",
        invoice_id: "",
        user_id: "",
        client_name: "",
        phone: "",
        email: "",
        contact: "",
        remarks: "",
        payment_mode_id: "",
        cheque: "",
        cheque_bank: "",
        payment_line_item: "",
        // signature_client: "",
        signature_tgor_officer: "",
        transaction: "",
        officer: "",
        partial_payment: [],
        gst_id: "",
      },
      fakeDiscount: "",
      isCheque: true,
      isCash: true,
      isBank: true,
      isPayNow: true,
      tabIndex: 0,
      activeFilter: false,
      activeStatus: "Active",
      activeClass: false,
      allSelected: false,
      selected: [],
      ids: [],
      service_id: [],
      isSave: false,
      columnActive: ReceiptNiche,
      columnActiveRoom: ReceiptRoom,
      columnActiveOther: ReceiptOther,
      columnActive2: {
        fields: [
          {
            key: "key1",
            thStyle: "width: 50px",
          },
          {
            key: "key2",
            thStyle: "width: 150px",
          },
          {
            key: "key3",
            thStyle: "width: 150px",
          },
          {
            key: "key4",
            thStyle: "width: 250px",
          },
          {
            key: "key5",
            thStyle: "width: 150px",
          },
          {
            key: "key6",
            thStyle: "width: 500px",
          },
          {
            key: "key7",
            thStyle: "width: 100px",
          },
          {
            key: "key8",
            thStyle: "width: 100px",
          },
          {
            key: "key9",
            thStyle: "width: 100px",
          },
          {
            key: "data",
            label: "Total",
            isActive: 1,
            thStyle: "width: 100px",
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
      state: {
        date1: null,
        date2: null,
      },
      dataPartialPayment: [null],
      gst: null,
      gstNumber: 0,
    };
  },
  validations() {
    if (!this.isCheque && this.isCash && this.isBank) {
      return {
        attachmentParams: {
          file: {
            required,
          },
        },
        paymentParams: {
          payment_mode_id: {
            required,
          },
          cheque: {
            required,
          },
          cheque_bank: {
            required,
          },
          transaction: {
            // required
          },
        },
      };
    } else {
      if (!this.isCash && this.isCheque && this.isBank) {
        return {
          attachmentParams: {
            file: {
              required,
            },
          },
          paymentParams: {
            payment_mode_id: {},
            cheque: {},
            cheque_bank: {
              // required
            },
            transaction: {
              // required
            },
          },
        };
      } else {
        return {
          attachmentParams: {
            file: {
              required,
            },
          },
          paymentParams: {
            payment_mode_id: {},
            cheque: {},
            cheque_bank: {},
            transaction: {
              required,
            },
          },
        };
      }
    }
  },
  filters: {
    formatMoney(val) {
      return accounting.formatMoney(val, {
        format: { pos: "%s %v", neg: "%s (%v)", zero: "--" },
      });
    },
  },
  computed: mapState({
    listAttachment: (state) => state.attachment.listAttachment,
    paymentDetail: (state) => state.payment.paymentDetail,
    listPaymentMode: (state) => state.payment.listPaymentMode,
  }),

  created() {
    this.gstdetail()
      .then((res) => {
        this.gst = Math.floor(res.data.data.rate * 100);
        this.gstNumber = res.data.data.rate;
      })
      .catch((error) => {});
    this.handleListAttachment();
    this.getPaymentdetail();
    this.getListPaymentMode()
      .then((response) => {
        if (response.data.data.length && !this.paymentParams.payment_mode_id) {
          this.paymentParams.payment_mode_id = response.data.data[0];
        }
      })
      .catch((err) => console.log(err));
    this.$nextTick(function () {
      this.$refs.signaturePadOfficer.resizeCanvas();
      // this.$refs.signaturePadClient.resizeCanvas();
    });
    this.gstdetail()
      .then((res) => {
        this.gst = Math.floor(res.data.data.rate * 100);
      })
      .catch((error) => {});
  },
  methods: {
    ...mapActions({
      addAttachment: "attachment/addAttachment",
      getListAttachment: "attachment/getListAttachment",
      deleteAttachment: "attachment/deleteAttachment",
      downloadAttachment: "attachment/downloadAttachment",
      getDetailPayment: "payment/getDetailPayment",
      saveSignPayment: "payment/saveSignPayment",
      getListPaymentMode: "payment/getListPaymentMode",
      getSumTotal: "saleareement/getSumTotal",
      gstdetail: "gst/gstdetail",
    }),
    checkPayment2(val) {
      this.$emit("checkPayment3", val);
    },
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    unFormatter(val) {
      return accounting.unformat(val);
    },
    onSubmit() {
      this.$v.attachmentParams.$touch();
      if (this.$v.attachmentParams.$anyError) {
        return;
      }

      var formData = new FormData();

      formData.append("id", this.$router.history.current.params.id);
      formData.append("type", 2);
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
        type: 2,
      };
      this.getListAttachment(prms).then((res) => {});
    },
    showModal(item) {
      this.$v.attachmentParams.$reset();
      this.attachmentParams.file = {};
      this.$refs.other_modal.show();
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
        type: 2,
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
    getPaymentdetail() {
      let prms = { id: this.$router.history.current.params.id };
      this.getDetailPayment(prms);
    },
    officerClear() {
      this.$refs.signaturePadOfficer.clearSignature();
    },
    // clientClear() {
    //   this.$refs.signaturePadClient.clearSignature();
    // },
    unFormatter(val) {
      return accounting.unformat(val);
    },
    getSignOfficer() {
      if (this.paymentParams.signature_tgor_officer == null) {
        const { isEmpty, data } = this.$refs.signaturePadOfficer.saveSignature();

        if (!isEmpty) {
          var signOfficerContent = data.replace(/^data:image\/(png|jpg);base64,/, "");
          var officer = this.base64ToBlob(signOfficerContent, "image/png");
          return officer;
        }
      }

      return null;
    },
    // getSignClient() {
    //   if (this.paymentParams.signature_client == null) {
    //     const { isEmpty, data } = this.$refs.signaturePadClient.saveSignature();

    //     if (!isEmpty) {
    //       var signClientContent = data.replace(
    //         /^data:image\/(png|jpg);base64,/,
    //         ""
    //       );
    //       var client = this.base64ToBlob(signClientContent, "image/png");
    //       return client;
    //     }
    //   }
    //   return null;
    // },
    onSaveSignture() {
      this.$v.paymentParams.$touch();
      if (this.$v.paymentParams.$anyError) {
        return;
      }
      let officer = this.getSignOfficer();
      // let client = this.getSignClient();
      var formData = new FormData();
      if (officer != null) {
        formData.append("signature_offier", officer);
      } else {
        if (this.paymentParams.signature_tgor_officer == null) {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: "Don't Forget Officer Signature",
          });
          return;
        }
      }
      // if (client != null) {
      //   formData.append("signature_client", client);
      // }
      formData.append("payment_mode_id", this.paymentParams.payment_mode_id.id);
      if (!this.isCheque) {
        formData.append("cheque", this.paymentParams.cheque);
        formData.append("cheque_bank", this.paymentParams.cheque_bank);
      }
      if (!this.isBank) {
        formData.append("transaction", this.paymentParams.transaction);
      }
      formData.append("remarks", this.paymentParams.remarks ? this.paymentParams.remarks : "");
      let prms = {
        id: this.$router.history.current.params.id,
        data: formData,
      };
      this.saveSignPayment(prms)
        .then((res) => {
          this.getDetailPayment({ id: this.$router.history.current.params.id });
          this.$swal({
            icon: "success",
            title: "Success!",
            text: res.data.status,
          });
          // (this.paymentParams.signature_client =
          //   res.data.data.signature_client),
          this.paymentParams.signature_tgor_officer = res.data.data.signature_tgor_officer;
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
    handleDefaultTotal(data) {
      let discount = parseFloat(data.item.line_item.sale_agreement_line_item.booking_line_item.discount_amount) || 0;
      let total = parseFloat(data.item.line_item.sale_agreement_line_item.booking_line_item.amount) - discount;
      let newDiscount = this.gstNumber * total;
      let total_amount = total + newDiscount;
      return total_amount;
    },
    calculateNumberOfDays(checkin, checkout) {
      var startDate = moment(checkin);
      var endDate = moment(checkout);
      var result = endDate.diff(startDate, "days") + 1;
      return result || "--";
    },
    formatTime(time) {
      return moment(time, "HH:mm:ss").format("HH:mm");
    },
  },
  watch: {
    paymentDetail: function (val) {
      this.paymentParams.payment_no = val.payment_no;
      this.paymentParams.payment_date = this.customFormatter(val.payment_date);
      this.paymentParams.invoice_id = val.invoice.invoice_no;
      this.paymentParams.client_name = val.client.display_name;
      this.paymentParams.phone = val.client.phone;
      this.paymentParams.email = val.client.email;
      this.paymentParams.contact = val.client.preferred_contact_by ? val.client.preferred_contact_by.reference_value_text : null;
      this.paymentParams.remarks = val.remarks;
      this.paymentParams.payment_line_item = val.payment_line_item;
      this.paymentParams.user_id = val.client.id;
      this.paymentParams.cheque = val.cheque;
      this.paymentParams.cheque_bank = val.cheque_bank;
      this.paymentParams.payment_mode_id = val.payment_mode ? val.payment_mode : this.listPaymentMode[0];
      // this.paymentParams.signature_client = val.signature_client;
      this.paymentParams.signature_tgor_officer = val.signature_tgor_officer;
      this.paymentParams.transaction = val.transaction;
      this.paymentParams.partial_payment = !isNull(val.partial_payments) ? val.partial_payments : [];
      if (val.admin) {
        this.paymentParams.officer = val.admin.display_name;
      }
      if (val.signature_tgor_officer) {
        this.$emit("isSave", true);
        this.isSave = true;
      }
      this.paymentParams.gst_id = val?.invoice?.sale_agreement?.gst_id || null;
      if (this.paymentParams.gst_id) {
        let prms = {
          gst_id: this.paymentParams.gst_id,
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
    },
    "paymentParams.payment_mode_id": function (val) {
      if (val != null) {
        this.$v.paymentParams.$reset();
        switch (val.reference_value_text) {
          case "Cheque":
            this.isCheque = false;
            this.isCash = true;
            this.isBank = true;
            this.isPayNow = true;
            break;
          case "Cash":
            this.isCheque = true;
            this.isCash = false;
            this.isBank = true;
            this.isPayNow = false;
            break;
          case "Multiple Payments":
            this.isCheque = true;
            this.isCash = false;
            this.isBank = true;
            this.isPayNow = false;
            break;
          case "Bank Transfer":
            this.isCheque = true;
            this.isCash = true;
            this.isBank = false;
            this.isPayNow = true;
            break;
          case "PayNow":
            this.isCheque = true;
            this.isCash = false;
            this.isBank = true;
            this.isPayNow = false;
            break;
          default:
            this.isCheque = false;
            this.isCash = true;
            this.isBank = true;
            this.isPayNow = true;
            break;
        }
      }
    },
    // listPaymentMode: function(val) {
    //   if (val.length && !this.paymentParams.payment_mode_id) {
    //     this.paymentParams.payment_mode_id = val[0];
    //   }
    // },
    "paymentParams.payment_line_item": function (val) {
      if (val != null) {
        var arr = [];
        val.map((item) => {
          arr.push(item.id);
        });
        let prms = {
          type: 3,
          arr_id: arr,
        };
        this.getSumTotal(prms).then((res) => {});
      }
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
.btn-add-partial {
  background: #71c5a1;
  color: #ffffff;
  font-size: 16px;
  width: 174px;
  height: 39px;
}
.icon-trash {
  margin-left: 10px;
  width: 20px;
  height: 20px;
}
.icon-trash:hover {
  cursor: pointer;
}
</style>
