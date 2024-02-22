<template>
  <b-container fluid="lg">
    <div class="columbarium-niches">
      <div class="title">
        <span class="title-name">Booking General</span>
      </div>
    </div>

    <div class="others-table">
      <div class="content" v-bind:class="{ 'run-loading': isLoading }">
        <div class="outside-table">
          <ControllTable
            :isShowIconAdd="false"
            :optionSearch="optionsFilter"
            :isShowIconExport="false"
            :onChangeSearch="onChangeSearch"
            @deleteItems="deleteItem"
          />
          <TableCustom
            ref="bookingGeneralTable"
            :tableFields="columnActive.fields"
            @Items="getItems"
            :tableItems="listBookingGeneral.data"
            @rowClicked="gotoBookingGeneralInfo"
          >
            <template slot="tgor_table:services" slot-scope="data">
              <div v-if="data.item.services.length">
                <span v-for="(item, key) in data.item.services" :key="key">
                  <template v-if="data.item.services.length == 1">
                    {{ item }}
                  </template>
                  <template v-else-if="data.item.services.length == key + 1">
                    {{ item }}
                  </template>
                  <template v-else> {{ item }}, </template>
                </span>
              </div>
              <div v-else>--</div>
            </template>
            <template slot="tgor_table:sale_agreement" slot-scope="data">
              <div v-if="data.item.sale_agreement.length">
                <span
                  v-for="(item, key) in data.item.sale_agreement"
                  :key="key"
                >
                  <div v-if="!item.is_add_invoice">
                    <router-link
                      :to="{
                        name: 'SaleAgreementInfo',
                        params: { id: item.id },
                      }"
                      class="underline"
                    >
                      {{ item.sale_agreement_no }}
                    </router-link>
                  </div>
                  <div v-else>
                    <span>Not Applicable</span>
                  </div>
                </span>
              </div>
              <div v-else>--</div>
            </template>
            <template slot="tgor_table:invoice" slot-scope="data">
              <div v-if="data.item.sale_agreement.length">
                <span
                  v-for="(items, key) in data.item.sale_agreement"
                  :key="key"
                >
                  <div v-if="items.invoices">
                    <span v-for="(value, key) in items.invoices" :key="key">
                      <template v-if="items.invoices.length == 1">
                        <router-link
                          :to="{
                            name: 'InvoiceInfo',
                            params: { id: value.id },
                          }"
                          class="underline"
                        >
                          {{ value.invoice_no }}
                        </router-link>
                      </template>
                      <template v-else-if="items.invoices.length == key + 1">
                        <router-link
                          :to="{
                            name: 'InvoiceInfo',
                            params: { id: value.id },
                          }"
                          class="underline"
                        >
                          {{ value.invoice_no }}
                        </router-link>
                      </template>
                      <template v-else>
                        <router-link
                          :to="{
                            name: 'InvoiceInfo',
                            params: { id: value.id },
                          }"
                          class="underline"
                        >
                          {{ value.invoice_no }},
                        </router-link>
                      </template>
                    </span>
                  </div>
                  <div v-else>--</div>
                </span>
              </div>
              <div v-else>--</div>
            </template>
            <template slot="tgor_table:payment" slot-scope="data">
              <div v-if="data.item.sale_agreement.length">
                <span
                  v-for="(items, key) in data.item.sale_agreement"
                  :key="key"
                >
                  <template v-for="(item, idx) in items.invoices">
                    <div v-if="item" :key="idx">
                      <span v-for="(value, key) in item.payment" :key="key">
                        <template v-if="item.payment.length == 1">
                          <router-link
                            :to="{
                              name: 'PaymentInfo',
                              params: { id: value.id },
                            }"
                            class="underline"
                          >
                            {{ value.payment_no }}
                          </router-link>
                        </template>
                        <template v-else-if="item.payment.length == key + 1">
                          <router-link
                            :to="{
                              name: 'PaymentInfo',
                              params: { id: value.id },
                            }"
                            class="underline"
                          >
                            {{ value.payment_no }}
                          </router-link>
                        </template>
                        <template v-else>
                          <router-link
                            :to="{
                              name: 'PaymentInfo',
                              params: { id: value.id },
                            }"
                            class="underline"
                          >
                            {{ value.payment_no }}
                          </router-link>
                        </template>
                      </span>
                    </div>
                  </template>
                </span>
              </div>
              <div v-else>--</div>
            </template>
            <template slot="tgor_table:created_at" slot-scope="data">
              {{ customFormatter(data.item.created_at) }}
            </template>
            <template slot="tgor_table:status" slot-scope="data">
              {{
                data.item.status && data.item.status.reference_value_text
                  ? data.item.status.reference_value_text
                  : "--"
              }}
            </template>
          </TableCustom>
          <b-row class="pagination">
            <b-col md="12" class="end">
              <span>
                {{
                  listBookingGeneral.from
                    ? `${listBookingGeneral.from}-${listBookingGeneral.to} of ${listBookingGeneral.total}`
                    : "0-0 of 0"
                }}
              </span>
              <span class="icon">
                <b-img
                  @click="prevPanigate"
                  class="image"
                  src="/images/left.png"
                  fluid
                  alt="Responsive image"
                ></b-img>
              </span>
              <span class="icon">
                <b-img
                  @click="nextPanigate"
                  class="image"
                  src="/images/right.png"
                  fluid
                  alt="Responsive image"
                ></b-img>
              </span>
            </b-col>
          </b-row>
        </div>
      </div>
    </div>
  </b-container>
</template>

<script>
//import
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";
import { mapActions, mapState } from "vuex";
import moment from "moment";
export default {
  components: {
    ControllTable,
    TableCustom,
  },
  metaInfo: {
    title: "Booking General",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Booking General Description",
      },
    ],
  },
  data() {
    return {
      isLoading: false,
      ids: [],
      tabIndex: 0,
      activeFilter: false,
      activeStatus: "Active",
      activeClass: false,
      allSelected: false,
      selected: [],
      bookingParams: {
        page: 1,
        filter: {},
      },
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
            key: "booking_no",
            label: "Booking #",
            isActive: 1,
            keySearch: "id",
            type: "text",
            isFilter: true,
            // thStyle: "width: 150px",
          },
          {
            key: "sale_agreement",
            label: "Sale Agreement #",
            // thStyle: "width: 250px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "invoice",
            label: "Invoice #",
            // thStyle: "width: 250px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "payment",
            label: "Receipt #",
            // thStyle: "width: 250px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "clients.display_name",
            label: `Client's Name`,
            isActive: 1,
            // thStyle: "width: 15tit0px",
            isFilter: true,
          },
          {
            key: "created_at",
            label: `Booking Date`,
            // thStyle: "width: 150px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "services",
            label: "Services",
            isActive: 1,
            thStyle: "width: 300px,max-width:300px;overflow:hidden",
            isFilter: true,
          },
          {
            key: "status",
            label: "Status",
            isActive: 1,
            thStyle: "width: 150px",
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        {
          name: "Booking #",
          value: "booking",
        },
        {
          name: "Sale Agreement #",
          value: "sale_agreement",
        },
        {
          name: "Client's Name",
          value: "clients_name",
        },
        {
          name: "Booking Date",
          value: "booking_date",
        },
        {
          name: "Services",
          value: "services",
        },
        {
          name: "Status",
          value: "status",
        },
      ],
    };
  },
  created() {
    this.handlePanigate(this.bookingParams);
  },
  computed: mapState({
    listBookingGeneral: (state) => state.booking.listBookingGeneral,
  }),
  methods: {
    ...mapActions({
      getBookingGeneralList: "booking/getBookingGeneralList",
      deleteBooking: "booking/deleteBooking",
    }),
    gotoBookingGeneralInfo(item) {
      this.$store.commit("booking/updateNRS");
      this.$router.push({
        name: "BookingGeneralInfo",
        params: { id: item.id },
      });
    },
    handlePanigate(params) {
      this.isLoading = true;
      this.getBookingGeneralList(params)
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
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    prevPanigate() {
      let { current_page } = this.listBookingGeneral;
      if (current_page != 1) {
        this.bookingParams.page = current_page - 1;
        this.handlePanigate(this.bookingParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listBookingGeneral;
      if (current_page != last_page) {
        this.bookingParams.page = current_page + 1;
        this.handlePanigate(this.bookingParams);
      }
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listBookingGeneral;
      clearTimeout(this.actionSearch);
      this.bookingParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.bookingParams);
        }, 1000);
      } else {
        this.bookingParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.bookingParams);
        }, 1000);
      }
    },
    deleteItem() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find booking.",
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
            this.deleteBooking(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handlePanigate(this.bookingParams);
                this.$refs.bookingGeneralTable.reloadData();
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
    getItems(item) {
      this.ids = item;
    },
  },
  watch: {},
};
</script>

<style lang="scss" scoped></style>
