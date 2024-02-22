<template>
  <b-container fluid="lg">
    <div class="columbarium-niches">
      <div class="title">
        <span class="title-name">Columbarium Niches</span>
      </div>
    </div>

    <div class="others-table">
      <div class="content" v-bind:class="{ 'run-loading': isLoading }">
        <div class="outside-table">
          <ControllTable
            :isShowIconAdd="true"
            :isShowIconTrash="showIconTrash"
            :optionSearch="optionsFilter"
            :onChangeSearch="onChangeSearch"
            :onCreate="onCreateNiche"
            @deleteItems="deleteItem"
            :isShowIconFilter="true"
            :isShowFilter="true"
            :isShowIconSearch="false"
            @showFilter="showFilterInTable"
            @bulkAction="showModelAction"
            :isShowBulkAction="true"
            :isShowIconExport="true"
            :onExport="exportNichesService"
            :quantilySelect="ids.length"
            @selectAllNiches="checkAllNiches"
          />
          <TableCustom
            class="table-admin-niche"
            ref="adminNichesTable"
            @Items="getItems"
            :tableFields="columnActive.fields"
            :tableItems="listNiches.data"
            @rowClicked="gotoAdminNicheInfo"
            :selectedAll="arr_ids"
          >
            <!-- <template slot="tgor_table:occupant" slot-scope="data">
                          {{data.item.booking_line_item && data.item.occupant != '' ? data.item.occupant : '--'}}
                    </template> -->
            <template v-if="isShowFilter" slot="tgor_head_table:reference_no" slot-scope="data">
              <div>{{ data.item.label }}</div>
              <b-form-input v-model="search.id" class="input-table-search"></b-form-input>
            </template>
            <template v-if="isShowFilter" slot="tgor_head_table:type.reference_value_text" slot-scope="data">
              <div>{{ data.item.label }}</div>
              <multiselect
                v-model="search.type"
                class="input-table-search bg-white"
                :show-labels="false"
                deselect-label=""
                :options="listType"
                placeholder="Select"
                track-by="name"
                label="name"
              ></multiselect>
            </template>
            <template v-if="isShowFilter" slot="tgor_head_table:location" slot-scope="data">
              <div>{{ data.item.label }}</div>
              <b-form-input v-model="search.location" class="input-table-search"></b-form-input>
            </template>
            <template v-if="isShowFilter" slot="tgor_head_table:status" slot-scope="data">
              <div>{{ data.item.label }}</div>
              <multiselect
                v-model="search.status"
                class="input-table-search bg-white"
                :show-labels="false"
                deselect-label=""
                :options="listStatusNiche"
                placeholder="Select"
                track-by="name"
                label="name"
              ></multiselect>
            </template>
            <template v-if="isShowFilter" slot="tgor_head_table:occupant" slot-scope="data">
              <div>{{ data.item.label }}</div>
              <b-form-input v-model="search.occupant_name" class="input-table-search"></b-form-input>
            </template>
            <template v-if="isShowFilter" slot="tgor_head_table:duration_of_lease" slot-scope="data">
              <div>{{ data.item.label }}</div>
              <b-form-input v-model="search.duration_of_lease" class="input-table-search"></b-form-input>
            </template>
            <template slot="tgor_table:occupant" slot-scope="data">
              <div v-if="data.item.booking_line_item">
                <span v-for="(item, key) in data.item.booking_line_item.information" :key="key">
                  <template v-if="data.item.booking_line_item.information.length == 1">
                    {{ item.full_name ? item.full_name : "" }}
                  </template>
                  <template v-else-if="data.item.booking_line_item.information.length == key + 1">
                    {{ item.full_name ? ", " + item.full_name : "" }}
                  </template>
                  <template v-else>{{ item.full_name ? item.full_name : "" }}</template>
                </span>
              </div>
              <div v-else>--</div>
            </template>
            <template slot="tgor_table:lease_expiry_date" slot-scope="data">
              {{
                data.item.booking_line_item && data.item.booking_line_item.lease_expiry_date != ""
                  ? customFormatter(data.item.booking_line_item.lease_expiry_date)
                  : "--"
              }}
            </template>
          </TableCustom>
          <b-row class="pagination">
            <b-col md="12" class="end">
              <span>
                {{ listNiches.from ? `${listNiches.from}-${listNiches.to} of ${listNiches.total}` : "0-0 of 0" }}
              </span>
              <span @click="prevPanigate" class="icon">
                <b-img class="image" src="images/left.png" fluid alt="Responsive image"></b-img>
              </span>
              <span @click="nextPanigate" class="icon">
                <b-img class="image" src="images/right.png" fluid alt="Responsive image"></b-img>
              </span>
            </b-col>
          </b-row>
        </div>
      </div>
    </div>
    <b-modal centered ref="bulk_action" hide-footer id="extension" size="sm" :title="title_bulk_action">
      <b-container fluid="lg">
        <b-row v-if="title_bulk_action == 'Extension Prices'">
          <b-col cols="12">
            <b-form-group>
              <label class="_label_input">Extension Duration <span class="_require">*</span></label>
              <b-form-input
                v-model.trim="$v.prmsBulkAction.exten_year.$model"
                :class="{
                  'form-group--error': $v.prmsBulkAction.exten_year.$error,
                }"
                placeholder="0"
                class="input-table-search"
              ></b-form-input>
              <div class="error" v-if="!$v.prmsBulkAction.exten_year.required && $v.prmsBulkAction.exten_year.$error">Field is required</div>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group>
              <label class="_label_input">Price (Excl. GST) <span class="_require">*</span></label>
              <masked-input
                v-model.trim="$v.prmsBulkAction.price.$model"
                :class="{
                  'form-group--error': $v.prmsBulkAction.price.$error,
                }"
                type="text"
                class="form-control"
                :mask="numberAmountMask()"
                :guide="true"
                placeholder="$0.00"
              >
              </masked-input>
              <div class="error" v-if="!$v.prmsBulkAction.price.required && $v.prmsBulkAction.price.$error">Field is required</div>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row v-if="title_bulk_action == 'Niche Status'">
          <b-col cols="12">
            <b-form-group>
              <label class="_label_input">Status <span class="_require">*</span></label>
              <multiselect
                :class="['input-table-search', 'bg-white', { 'bg-gray': true, 'form-group--error': $v.prmsBulkAction.status.$error }]"
                v-model.trim="$v.prmsBulkAction.status.$model"
                :show-labels="false"
                :allow-empty="false"
                deselect-label=""
                :options="listStatusNiche"
                placeholder="Select"
                track-by="name"
                label="name"
              ></multiselect>
              <div class="error" v-if="!$v.prmsBulkAction.status.required && $v.prmsBulkAction.status.$error">Field is required</div>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row v-if="title_bulk_action == 'Niche Prices'">
          <b-col cols="12">
            <b-form-group>
              <label class="_label_input">Price (Excl. GST) <span class="_require">*</span></label>
              <masked-input
                v-model.trim="$v.prmsBulkAction.price.$model"
                type="text"
                :class="{
                  'form-group--error': $v.prmsBulkAction.price.$error,
                }"
                class="form-control"
                :mask="numberAmountMask()"
                :guide="true"
                placeholder="$0.00"
              >
              </masked-input>
              <div class="error" v-if="!$v.prmsBulkAction.price.required && $v.prmsBulkAction.price.$error">Field is required</div>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row class="btn-submit">
          <b-col cols="12">
            <div class="submit" @click="handleSubmitBulkAction">Submit</div>
          </b-col>
        </b-row>
      </b-container>
    </b-modal>
  </b-container>
</template>

<script>
//import
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";
import Multiselect from "vue-multiselect";
import { mapActions, mapState } from "vuex";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
import { required } from "vuelidate/lib/validators";
const accounting = require("accounting");
import moment from "moment";

export default {
  components: {
    ControllTable,
    TableCustom,
    Multiselect,
    MaskedInput,
  },
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
      showIconTrash: true,
      admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
      isLoading: false,
      actionSearch: null,
      ids: [],
      tabIndex: 0,
      activeFilter: false,
      activeStatus: "Active",
      activeClass: false,
      allSelected: false,
      selected: [],
      isShowFilter: false,
      prmsBulkAction: {
        exten_year: "",
        status: "",
        price: "",
      },
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        {
          name: "Niche ID",
          value: "id",
        },
        {
          name: "Type",
          value: "type",
        },
        {
          name: "Location",
          value: "location",
        },
        {
          name: "Status",
          value: "status",
        },
        {
          name: "Occupant Name",
          value: "occupant_name",
        },
        {
          name: "Lease Expiry Date",
          value: "expiry_day",
        },
      ],

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
            key: "reference_no",
            label: "ID",
            isActive: 1,
            keySearch: "id",
            type: "text",
            thStyle: "width: 110px",
            isFilter: true,
          },
          {
            key: "type.reference_value_text",
            label: "Type",
            isActive: 1,
            thStyle: "width: 150px",
            isFilter: true,
          },
          {
            key: "location",
            label: `Location`,
            thStyle: "width: 400px",
            isActive: 1,
          },
          {
            key: "status",
            label: `Status`,
            isActive: 1,
            thStyle: "width: 180px",
            isFilter: true,
          },
          {
            key: "occupant",
            label: "Occupant Name",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "lease_expiry_date",
            label: "Lease Expiry Date",
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
      listType: [{ name: "Single" }, { name: "Double" }],
      listStatusNiche: [{ name: "Available" }, { name: "Unavailable" }, { name: "Reserved" }],
      title_bulk_action: "",
      search: {
        id: "",
        type: null,
        location: "",
        status: null,
        occupant_name: "",
        duration_of_lease: "",
      },
      filterParams: {
        page: 1,
        filter: {},
      },
      isShowSelectAll: false,
      arr_ids: [],
    };
  },
  validations() {
    if (this.title_bulk_action == "Extension Prices") {
      return {
        prmsBulkAction: {
          exten_year: {
            required,
          },
          price: {
            required,
          },
        },
      };
    } else if (this.title_bulk_action == "Niche Status") {
      return {
        prmsBulkAction: {
          status: {
            required,
          },
        },
      };
    } else if (this.title_bulk_action == "Niche Prices") {
      return {
        prmsBulkAction: {
          price: {
            required,
          },
        },
      };
    }
  },
  created() {
    if (this.admin_profile.roles_id != 1) {
      this.showIconTrash = false;
    }
    this.handlePanigate(this.filterParams);
  },
  computed: mapState({
    listNiches: (state) => state.niche.listNiches,
  }),
  watch: {
    "search.id": function () {
      this.onChangeFilter();
    },
    "search.type": function () {
      this.onChangeFilter();
    },
    "search.location": function () {
      this.onChangeFilter();
    },
    "search.status": function () {
      this.onChangeFilter();
    },
    "search.occupant_name": function () {
      this.onChangeFilter();
    },
    "search.duration_of_lease": function () {
      this.onChangeFilter();
    },
  },
  methods: {
    ...mapActions({
      getListNiches: "niche/getListNiches",
      deleteNiches: "niche/deleteNiches",
      createBulkAction: "niche/createBulkAction",
      exportNichesForAdmin: "niche/exportNichesForAdmin",
      getIdAllNichesFilter: "niche/getIdAllNichesFilter",
    }),
    gotoAdminNicheInfo(item) {
      this.$router.push({
        name: "AdminNichesInfo",
        params: {
          id: item.id,
        },
      });
    },
    onCreateNiche() {
      this.$router.push({
        name: "AdminNichesCreate",
      });
    },
    handlePanigate(params) {
      this.isLoading = true;
      // if (Object.keys(params.filter).length == 0) {
      //   delete params.filter;
      // }
      this.getListNiches(params)
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
      let { current_page } = this.listNiches;
      if (current_page != 1) {
        this.filterParams.page = current_page - 1;
        this.handlePanigate(this.filterParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listNiches;
      if (current_page != last_page) {
        this.filterParams.page = current_page + 1;
        this.handlePanigate(this.filterParams);
      }
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listNiches;
      clearTimeout(this.actionSearch);
      this.filterParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.filterParams);
        }, 300);
      } else {
        this.filterParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.filterParams);
        }, 300);
      }
    },
    onChangeFilter() {
      let { current_page, last_page } = this.listNiches;
      clearTimeout(this.actionSearch);
      let filter = { ...this.search };
      if (filter.type) {
        filter["type"] = filter.type.name;
      }
      if (filter.status) {
        filter["status"] = filter.status.name;
      }
      this.filterParams.filter = filter;
      this.actionSearch = setTimeout(() => {
        this.handlePanigate(this.filterParams);
      }, 300);
    },
    deleteItem() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find niche.",
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
            this.deleteNiches(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handlePanigate(this.filterParams);
                this.$refs.adminNichesTable.reloadData();
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
      if (!item.length) {
        this.arr_ids = [];
      }
    },
    unformatter(val) {
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
    showFilterInTable(val) {
      this.isShowFilter = val;
    },
    showModelAction(val) {
      if (this.ids.length && val) {
        (this.prmsBulkAction.exten_year = ""), (this.prmsBulkAction.status = ""), (this.prmsBulkAction.price = ""), (this.title_bulk_action = val);
        this.$refs.bulk_action.show();
      } else {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Please Choose Niches",
        });
      }
    },
    onSubmitBulkAction(prms) {
      this.$v.prmsBulkAction.$touch();
      if (this.$v.prmsBulkAction.$anyError) {
        return;
      }
      this.createBulkAction(prms)
        .then((res) => {
          this.$swal({
            icon: "success",
            title: "Success!",
            text: res.data.status,
          });
          this.handlePanigate(this.filterParams);
          this.$refs.adminNichesTable.reloadData();
          this.$refs.bulk_action.hide();
          this.isLoading = false;
        })
        .catch((error) => {
          this.isLoading = false;
          this.$refs.bulk_action.hide();
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
    handleSubmitBulkAction() {
      if (this.title_bulk_action == "Extension Prices") {
        let prms = {
          type_action: "extension",
          ids: this.ids,
          extension_duration: this.prmsBulkAction.exten_year,
          price: this.unformatter(this.prmsBulkAction.price),
        };
        this.onSubmitBulkAction(prms);
      } else if (this.title_bulk_action == "Niche Status") {
        let prms = {
          type_action: "status",
          ids: this.ids,
          status: this.prmsBulkAction.status.name,
        };
        this.onSubmitBulkAction(prms);
      } else if (this.title_bulk_action == "Niche Prices") {
        let prms = {
          type_action: "price",
          ids: this.ids,
          price: this.unformatter(this.prmsBulkAction.price),
        };
        this.onSubmitBulkAction(prms);
      }
    },
    exportNichesService() {
      if (!this.ids.length) {
        this.$swal({
          icon: "warning",
          title: "Warning",
          text: "Please select niche.",
        });
        return;
      }
      let prms = { ids: this.ids };

      this.exportNichesForAdmin(prms).then((res) => {
        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "export_niches.csv");
        document.body.appendChild(link);
        link.click();
      });
    },
    checkAllNiches() {
      this.getIdAllNichesFilter(this.filterParams).then((response) => {
        this.arr_ids = response.data;
      });
    },
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
  },
};
</script>

<style lang="scss" scoped>
/* .table-admin-niche {
  .input-table-search {
  }
} */
</style>
