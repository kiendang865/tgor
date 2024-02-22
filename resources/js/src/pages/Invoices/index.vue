<template>
  <b-container fluid="lg">
    <div class="columbarium-niches">
      <div class="title">
        <span class="title-name">Invoices</span>
      </div>
    </div>

    <div class="others-table">
      <div class="content" v-bind:class="{ 'run-loading': isLoading }">
        <div class="outside-table">
          <ControllTable
            :isShowIconAdd="true"
            :isShowIconTrash="isTrash"
            :onCreate="createInvoice"
            :optionSearch="optionsFilter"
            :isShowIconExport="false"
            :onChangeSearch="onChangeSearch"
            @deleteItems="deleteItem"
          />
          <TableCustom
            ref="InvoicesTable"
            :tableFields="columnActive.fields"
            @Items="getItems"
            :tableItems="listInvoices.data"
            @rowClicked="gotoInvoiceInfo"
          >
            <template slot="tgor_table:invoice_date" slot-scope="data">
              {{ data.item.invoice_date ? customFormatter(data.item.invoice_date) : "--" }}
            </template>
            <template slot="tgor_table:payment" slot-scope="data">
              <div v-if="data.item.payment.length">
                <span v-for="(item, key) in data.item.payment" :key="key">
                  <template v-if="data.item.payment.length == 1">
                    <router-link :to="{ name: 'PaymentInfo', params: { id: item.id } }" class="underline">
                      {{ item.payment_no }}
                    </router-link>
                  </template>
                  <template v-else-if="data.item.payment.length == key + 1">
                    <router-link :to="{ name: 'PaymentInfo', params: { id: item.id } }" class="underline">
                      {{ item.payment_no }}
                    </router-link>
                  </template>
                  <template v-else>
                    <router-link :to="{ name: 'PaymentInfo', params: { id: item.id } }" class="underline">
                      {{ item.payment_no }}
                    </router-link>
                  </template>
                </span>
              </div>
              <div v-else>--</div>
            </template>
            <template slot="tgor_table:total_amount" slot-scope="data">
              {{ data.item.total_amount ? data.item.total_amount : 0 | formatMoney }}
            </template>
            <template slot="tgor_table:total_tax_amount" slot-scope="data">
              {{ data.item.total_tax_amount ? data.item.total_tax_amount : 0 | formatMoney }}
            </template>
            <template slot="tgor_table:total_discount" slot-scope="data">
              {{ data.item.total_discount ? data.item.total_discount : 0 | formatMoney }}
            </template>
            <template slot="tgor_table:total" slot-scope="data">
              {{ data.item.total ? data.item.total : 0 | formatMoney }}
            </template>
            <template slot="tgor_table:remark" slot-scope="data">
              {{ data.item.remark ? data.item.remark : "--" }}
            </template>
          </TableCustom>
          <b-row class="pagination">
            <b-col md="12" class="end">
              <span>
                {{ listInvoices.from ? `${listInvoices.from}-${listInvoices.to} of ${listInvoices.total}` : "0-0 of 0" }}
              </span>
              <span class="icon">
                <b-img @click="prevPanigate" class="image" src="images/left.png" fluid alt="Responsive image"></b-img>
              </span>
              <span class="icon">
                <b-img @click="nextPanigate" class="image" src="images/right.png" fluid alt="Responsive image"></b-img>
              </span>
            </b-col>
          </b-row>
        </div>
      </div>
    </div>
    <b-modal centered ref="other_modal" hide-footer id="extension" size="sm" title="Add Invoice">
      <b-container fluid="lg">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Booking #">
              <multiselect
                :show-labels="false"
                deselect-label=""
                v-model="$v.invoiceParam.booking_id.$model"
                :options="listServiceInvoice"
                placeholder="Select one"
                label="booking_no"
                track-by="id"
              ></multiselect>
              <div class="error" v-if="!$v.invoiceParam.booking_id.required && $v.invoiceParam.booking_id.$error">Field is required</div>
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
    <b-modal centered ref="invoice_modal" hide-footer hide-header id="extension" size="lg">
      <b-container fluid="lg">
        <b-row>
          <div class="content_modal">
            <p class="title">This booking haven't had Sale Sgreemnet for Memorial Room and Niche</p>
            <span class="question">Please generate Sale Agreement.</span>
          </div>
        </b-row>
        <b-row class="btn-group-modal">
          <b-button class="btn-yes" @click="btnNo" type="button">OK</b-button>
        </b-row>
      </b-container>
    </b-modal>
  </b-container>
</template>

<script>
//import
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";
const accounting = require("accounting");
import moment from "moment";
import Multiselect from "vue-multiselect";
import { showAlertError, showAlertSuccess } from "../../common/utils";

import { mapActions, mapState } from "vuex";
import { required, minLength, between } from "vuelidate/lib/validators";

export default {
  components: {
    ControllTable,
    TableCustom,
    Multiselect,
  },
  metaInfo: {
    title: "Invoices",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Invoices Description",
      },
    ],
  },
  data() {
    return {
      isLoading: false,
      invoiceParams: {
        page: 1,
        filter: {},
      },
      ids: [],
      tabIndex: 0,
      activeFilter: false,
      activeStatus: "Active",
      activeClass: false,
      allSelected: false,
      selected: [],
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        {
          name: "Invoice #",
          value: "invoice_no",
        },
        {
          name: "Receipt #",
          value: "payment",
        },
        {
          name: "Date",
          value: "date",
        },
        {
          name: "Client's Name",
          value: "clients_name",
        },
        {
          name: "Remarks",
          value: "clients_name",
        },
        {
          name: "Amount",
          value: "amount",
        },
        {
          name: "GST",
          value: "gst",
        },
        {
          name: "Total",
          value: "total",
        },
      ],
      columnActive: {
        fields: [
          {
            key: "invoice_no",
            label: "Invoice #",
            isActive: 1,
            keySearch: "id",
            type: "text",
            isFilter: true,
          },
          {
            key: "payment",
            label: "Receipt #",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "invoice_date",
            label: `Date`,

            isActive: 1,
            isFilter: true,
          },
          {
            key: "client.display_name",
            label: `Client's Name`,
            isActive: 1,
            isFilter: true,
          },
          {
            key: "remark",
            label: "Remarks",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "total_amount",
            label: "Amount",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "total_tax_amount",
            label: "GST",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "total_discount",
            label: "Discount",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "total",
            label: "Total",
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
      invoiceParam: {
        booking_id: "",
      },
      isTrash: false,
      admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
    };
  },
  validations: {
    invoiceParam: {
      booking_id: {
        required,
      },
    },
  },
  filters: {
    formatMoney(val) {
      return accounting.formatMoney(val, { format: { pos: "%s %v", neg: "%s (%v)", zero: "--" } });
    },
  },
  created() {
    if (this.admin_profile.roles_id === 1) {
      this.isTrash = true;
      this.columnActive.fields.unshift({
        key: "actions",
        label: "",
        thClass: "checkbox-column text-center",
        tdClass: "checkbox-column text-center",
        thStyle: "width: 50px",
        isActive: 1,
      });
    } else {
      this.columnActive.fields.unshift({
        key: "xxx",
        label: "",
        thClass: "checkbox-column text-center",
        tdClass: "checkbox-column text-center",
        thStyle: "width: 50px",
        isActive: 1,
      });
    }
    this.handlePanigate(this.invoiceParams);
    this.getListBookingAddInvoice().catch((error) => {
      showAlertError(this.$swal, error.response.data.errors);
    });
  },
  computed: mapState({
    listInvoices: (state) => state.invoice.listInvoices,
    listServiceInvoice: (state) => state.invoice.listServiceInvoice,
  }),
  methods: {
    ...mapActions({
      getListInvoices: "invoice/getListInvoices",
      deleteInvoices: "invoice/deleteInvoices",
      getListBookingAddInvoice: "invoice/getListBookingAddInvoice",
      addInvoices: "invoice/addInvoices",
    }),
    gotoInvoiceInfo(item) {
      this.$router.push({ name: "InvoiceInfo", params: { id: item.id } });
    },
    handlePanigate(params) {
      this.isLoading = true;
      this.getListInvoices(params)
        .then((response) => {
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
    },
    prevPanigate() {
      let { current_page } = this.listInvoices;
      if (current_page != 1) {
        this.invoiceParams.page = current_page - 1;
        this.handlePanigate(this.invoiceParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listInvoices;
      if (current_page != last_page) {
        this.invoiceParams.page = current_page + 1;
        this.handlePanigate(this.invoiceParams);
      }
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listInvoices;
      clearTimeout(this.actionSearch);
      this.invoiceParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.invoiceParams);
        }, 300);
      } else {
        this.invoiceParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.invoiceParams);
        }, 300);
      }
    },
    deleteItem() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find invoice.",
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
            let prms = { ids: this.ids };
            this.deleteInvoices(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.message,
                });
                this.handlePanigate(this.invoiceParams);
                this.$refs.InvoicesTable.reloadData();
                this.isLoading = false;
              })
              .catch((error) => {
                console.log(error);
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
    getItems(item) {
      this.ids = item;
    },
    onCreateNiche() {
      this.$router.push({
        name: "CreateRoom",
      });
    },
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    unformatter(val) {
      return accounting.unformat(val);
    },
    createInvoice() {
      this.$refs.other_modal.show();
    },
    onSubmit() {
      this.$v.invoiceParam.$touch();
      if (this.$v.invoiceParam.$anyError) {
        return;
      }

      let prms = { ...this.invoiceParam.booking_id };
      let flag = false;
      prms.booking_line_items.map((item, index) => {
        if (item.booking_type.reference_value_text == "Memorial Rooms" || item.booking_type.reference_value_text == "Niches") {
          flag = true;
        }
      });
      this.$refs.other_modal.hide();
      if (flag) {
        this.$refs.invoice_modal.show();
      } else {
        this.handleGenerateSA();
      }
    },
    resetValidate() {
      this.$v.invoiceParam.$reset;
      this.invoiceParam.booking_id = null;
    },
    btnNo() {
      this.$refs.invoice_modal.hide();
      this.resetValidate();
    },
    handleGenerateSA() {
      //invoice info (pass id)
      this.addInvoices({ booking_id: this.invoiceParam.booking_id.id })
        .then((response) => {
          this.$router.push({ name: "InvoiceInfo", params: { id: response.data.id } });
        })
        .catch((error) => {
          showAlertError(this.$swal, error.response.data.errors);
        });
      this.$refs.invoice_modal.hide();
      this.resetValidate();
    },
  },
  watch: {},
};
</script>

<style lang="scss" scoped>
.content_modal {
  text-align: center;
  padding-left: 20px;
  padding-right: 20px;
  padding-top: 50px;
  padding-bottom: 30px;
}
.content_modal .title {
  font-size: 20px;
  font-weight: 800;
}
.content_modal .question {
  font-size: 16px;
  font-weight: normal;
}
.btn-group-modal {
  display: flex;
  justify-content: space-evenly;
}
.btn-cancel {
  background: #b70050;
  border-radius: 10px;
  padding: 15px 35px;
}
.btn-yes {
  background: #71c5a1;
  border-radius: 10px;
  padding: 15px 35px;
}
</style>
