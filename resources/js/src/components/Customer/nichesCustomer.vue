<template>
  <div class="content-tab-table">
    <div class="outside-tab-table" v-bind:class="{ 'run-loading': isLoading }">
      <ControllTable
        :isShowIconAdd="false"
        :isShowIconTrash="false"
        :isShowBtnExt="allowExtent"
        :optionSearch="optionsFilter"
        :onChangeSearch="onChangeSearch"
        @deleteItems="deleteItem"
        @extension="extensionNiches"
        :isShowIconExport="true"
        :onExport="exportNichesService"
      ></ControllTable>
      <Table
        ref="adminNicheServiceTable"
        :tableFields="columnActive.fields"
        @Items="getItems"
        :tableItems="listServiceNiches.data"
        @rowClicked="gotoServiceNiches"
      >
        <template slot="tgor_table:booking_niche" slot-scope="data">
          <div v-if="data.item.information.length">
            <span v-for="(item, key) in data.item.information" :key="key">
              <template v-if="data.item.information.length == 1">
                {{ item.full_name ? item.full_name : "" }}
              </template>
              <template v-else-if="data.item.information.length == key + 1">
                {{ item.full_name ? ", " + item.full_name : "" }}
              </template>
              <template v-else>{{ item.full_name ? item.full_name : "" }}</template>
            </span>
          </div>
          <div v-else>--</div>
        </template>
        <template slot="tgor_table:start_date" slot-scope="data">
          {{ data.item.lease_start_date ? customFormatter(data.item.lease_start_date) : "--" }}
        </template>
        <template slot="tgor_table:expiry_date" slot-scope="data">
          {{ data.item.lease_expiry_date ? customFormatter(data.item.lease_expiry_date) : "--" }}
        </template>
      </Table>
      <b-row class="pagination">
        <b-col md="12" class="end">
          <span>
            {{ listServiceNiches.from ? `${listServiceNiches.from}-${listServiceNiches.to} of ${listServiceNiches.total}` : "0-0 of 0" }}
          </span>
          <span class="icon" @click="prevPanigate">
            <b-img class="image" src="/images/left.png" fluid alt="Responsive image"></b-img>
          </span>
          <span class="icon" @click="nextPanigate">
            <b-img class="image" src="/images/right.png" fluid alt="Responsive image"></b-img>
          </span>
        </b-col>
      </b-row>
    </div>
    <b-modal id="mass-niches" centered hide-footer ref="extensionNiche" title="Extension">
      <template v-slot:modal-header="{ close }">
        <!-- Emulate built in modal header close button action -->
        <!-- <b-button size="sm" variant="outline-danger" @click="close()">
                Close Modal
              </b-button> -->
        <h5>Extension</h5>

        <!-- <h5>Total all: $111600.00</h5> -->
        <div class="modal-right">
          <div>
            <span>Total all: {{ totalAmount | formatMoney }}</span>
          </div>
          <div class="img-close" @click="close()">
            <b-img class="icon-close" src="/images/close.png" alt="Image 2"></b-img>
          </div>
        </div>
      </template>
      <b-container fluid="lg">
        <template v-for="(v, index) in extensionParam.information">
          <b-row :key="index" :class="{ mt: index && index != 0 ? true : false }">
            <b-col cols="4">
              <b-form-group label="Niches">
                <b-form-input v-model="v.reference_no" :disabled="true" class="input-form"></b-form-input>
                <!-- <multiselect :show-labels="false" deselect-label="" :disabled="true" :class="{'form-group--error': $v.extensionParam.niche_id.$error }" v-model.trim="$v.extensionParam.niche_id.$model" :options="optionsNiche" placeholder="Select one" track-by="value" label="name" ></multiselect> -->
                <!-- <div class="error" v-if="!$v.extensionParam.niche_id.required && $v.extensionParam.niche_id.$error">Field is required</div> -->
              </b-form-group>
            </b-col>
            <b-col cols="4">
              <b-form-group label="Duration">
                <multiselect
                  :preselectFirst="true"
                  @input="priceOfInfo(v.time, index)"
                  :show-labels="false"
                  deselect-label=""
                  v-model="v.time"
                  :options="v.extension"
                  placeholder="Select one"
                  label="exten_year"
                  track-by="id"
                >
                  <template slot="singleLabel" slot-scope="{ option }">{{ option.exten_year }} years</template>
                </multiselect>
              </b-form-group>
            </b-col>
            <b-col cols="4">
              <b-form-group label="Total">
                <!-- <b-form-input v-model="v.renew_price" :disabled="true" class="input-form " placeholder="$00.00"></b-form-input> -->
                <masked-input
                  type="text"
                  v-model="v.renew_price"
                  class="form-control"
                  :mask="numberAmountMask()"
                  :guide="true"
                  :disabled="true"
                  placeholder="$0.00"
                >
                </masked-input>
              </b-form-group>
            </b-col>
          </b-row>
        </template>
        <b-row class="btn-submit">
          <b-col cols="12">
            <div class="submit" @click="onSubmit">Submit</div>
          </b-col>
        </b-row>
      </b-container>
    </b-modal>
  </div>
</template>

<script>
import ControllTable from "@/components/customViews/controllTable.vue";
import Table from "@/components/Table/nicheCustomerTable";
import { mapActions, mapState } from "vuex";
import moment from "moment";
import Multiselect from "vue-multiselect";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
var accounting = require("accounting");
import { required, minLength, between, decimal } from "vuelidate/lib/validators";

export default {
  name: "ServiceNiches",
  components: { ControllTable, Table, Multiselect, MaskedInput },
  metaInfo: {
    title: "Columbarium Niches",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Columbarium Niches Description",
      },
    ],
  },
  data() {
    return {
      totalAmount: "",
      isLoading: false,
      allowExtent: true,
      ids: [],
      optionsNiche: [
        {
          name: "30 years",
          value: 30,
        },
        {
          name: "50 years",
          value: 50,
        },
      ],
      serviceNicheParams: {
        page: 1,
        type: "Niche",
        filter: {},
        user_id: this.$router.history.current.params.id,
      },
      extensionParam: {
        information: [
          {
            id: "",
            reference_no: "",
            time: "",
            exten_year: "",
            extension: "",
            renew_price: "",
          },
        ],
      },
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        {
          name: "Niche ID",
          value: "niche_id",
        },
        {
          name: "Location",
          value: "location",
        },
        {
          name: "Started Lease Date",
          value: "started_lease_date",
        },
        {
          name: "Expired Lease Date",
          value: "expired_lease_date",
        },
      ],
      columnActive: {
        fields: [
          {
            key: "actions",
            label: "",
            thClass: "checkbox-column text-center",
            tdClass: "checkbox-column text-center",
            thStyle: "width:20px",
            isActive: 1,
          },
          {
            key: "booking.booking_no",
            label: "Booking #",
            // thStyle: "width:100px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "niche.reference_no",
            label: "Niche ID",
            // thStyle: "width:100px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "niche.full_location",
            label: "Location",
            // thStyle: "width:220px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "booking_niche",
            label: "Occupant",
            // thStyle: "width:100px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "start_date",
            label: "Started Lease Date",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "expiry_date",
            label: "Expired Lease Date",
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
    };
  },
  validations() {
    return {
      extensionParam: {
        // information:{
        //   required,
        //   $each: {
        //     reference_no:{
        //       required
        //     },
        //     time:{
        //       required
        //     },
        //     exten_year:{
        //       required
        //     },
        //     extension:{
        //     }
        //   }
        // }
      },
    };
  },
  created() {
    this.handlePanigate(this.serviceNicheParams);
    this.getListRate();
    // this.extensionParam.duration = this.optionsNiche[0];
    // this.extensionParam.infomation = []
  },
  computed: mapState({
    listServiceNiches: (state) => state.service.listServiceNiches,
    listRate: (state) => state.booking.listRate,
  }),
  methods: {
    ...mapActions({
      getListServiceNiches: "service/getListServiceNiches",
      deleteService: "service/deleteService",
      exportNiches: "service/exportNiches",
      sumTotalNiches: "niche/sumTotalNiches",
      getListRate: "booking/getListRate",
      extensionNiche: "booking/extensionNiche",
      getInfoExtensionNiche: "booking/getInfoExtensionNiche",
      extensionMutipleNiche: "booking/extensionMutipleNiche",
    }),
    gotoServiceNiches(item) {
      // this.$router.push({name:"BookingInfoServiceNiches", params: { id: item.id }})
    },
    handlePanigate(params) {
      this.isLoading = true;
      this.getListServiceNiches(params)
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
    customFormatDeath(date) {
      return moment(date).format("DD/MM");
    },
    prevPanigate() {
      let { current_page } = this.listServiceNiches;
      if (current_page != 1) {
        this.serviceNicheParams.page = current_page - 1;
        this.handlePanigate(this.serviceNicheParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listServiceNiches;
      if (current_page != last_page) {
        this.serviceNicheParams.page = current_page + 1;
        this.handlePanigate(this.serviceNicheParams);
      }
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listServiceNiches;
      clearTimeout(this.actionSearch);
      this.serviceNicheParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.serviceNicheParams);
        }, 300);
      } else {
        this.serviceNicheParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.serviceNicheParams);
        }, 300);
      }
    },
    deleteItem() {
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
            this.deleteService(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handlePanigate(this.serviceNicheParams);
                this.$refs.adminNicheServiceTable.reloadData();
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
      let prms = {
        arr_id: this.ids,
      };
      // this.sumTotalNiches(prms).then(res => {
      //   this.extensionParam.amount = res.data.data.total
      // })
      this.totalAmount = "";
      if (this.ids.length) {
        this.allowExtent = true;
        this.getInfoExtensionNiche(prms)
          .then((res) => {
            this.extensionParam.information = res.data.data;
            this.extensionParam.information.map((item, i) => {
              this.extensionParam.information[i].renew_price = "";
            });
          })
          .catch((error) => {
            this.allowExtent = false;
            this.$swal({
              icon: "error",
              title: "Oops...",
              text: error.response.data.errors,
            });
          });
      }

      // console.log(this.extensionParam)
    },
    exportNichesService() {
      this.exportNiches().then((res) => {
        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "export_niches.xlsx");
        document.body.appendChild(link);
        link.click();
      });
    },
    extensionNiches() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Please select booking.",
        });
        return;
      }
      this.$refs.extensionNiche.show();
    },
    onSubmit() {
      let extent_arr = [];

      this.extensionParam.information.map((item, i) => {
        let prms = {
          arr_id: item.line_id,
          duration: item.time ? item.time.id : null,
          user_id: this.$router.history.current.params.id,
          niche_id: item.id,
        };

        extent_arr.push(prms);
      });
      let params = {
        extent_arr: extent_arr,
        user_id: this.$router.history.current.params.id,
      };

      this.extensionMutipleNiche(params)
        .then((res) => {
          this.$refs.extensionNiche.hide();
          // console.log(res)
          this.$store.commit("booking/updateNRS");
          this.$router.push({ name: "BookingGeneralInfo", params: { id: res.data.data.id } });
          this.$swal({
            icon: "success",
            title: "Success",
            text: res.data.status,
          });
        })
        .catch((error) => {
          this.$refs.extensionNiche.hide();
          // console.log(error)
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
    priceOfInfo(value, index) {
      if (value != null) {
        this.extensionParam.information[index].extension.map((item, i) => {
          if (item.exten_year == value.exten_year) {
            this.extensionParam.information[index].renew_price = value.exten_price;
          }
        });
      } else {
        this.extensionParam.information[index].renew_price = "";
      }
      var total = 0;

      this.extensionParam.information.map((v, i) => {
        total += this.formatMoney(v.renew_price);
      });
      this.totalAmount = total;
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
  },
  filters: {
    formatMoney(val) {
      return accounting.formatMoney(val, { format: { pos: "%s %v", neg: "%s (%v)", zero: "--" } });
    },
  },
};
</script>
