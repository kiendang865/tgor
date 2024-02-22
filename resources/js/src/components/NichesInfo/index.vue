<template>
  <div v-bind:class="{ 'run-loading': isLoading }" class="booking-info-niches">
    <b-container fluid="lg">
      <div class="content-admin-niches">
        <b-container fluid="lg" class="form-group-niches">
          <b-row class="row-item-form-niche">
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Niche Reference <span class="_require">*</span></label>
                <b-form-input
                  autofocus
                  :class="{ 'form-group--error': $v.form.reference_no.$error }"
                  v-model.trim="$v.form.reference_no.$model"
                  name="reference_no"
                  class="input-form"
                  aria-describedby="reference_no_live_feedback"
                />
                <div class="error" v-if="!$v.form.reference_no.required && $v.form.reference_no.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Occupancy <span class="_require">*</span></label>
                <Multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :class="{ 'form-group--error': $v.form.type_id.$error }"
                  name="type_id"
                  track-by="id"
                  label="reference_value_text"
                  class="select-type-niche"
                  v-model.trim="$v.form.type_id.$model"
                  :options="listTypes"
                  placeholder="Select a type"
                ></Multiselect>
                <div class="error" v-if="!$v.form.type_id.required && $v.form.type_id.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Type <span class="_require">*</span></label>
                <Multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :class="{ 'form-group--error': $v.form.category_id.$error }"
                  name="type_id"
                  track-by="id"
                  label="reference_value_text"
                  class="select-type-niche"
                  v-model.trim="$v.form.category_id.$model"
                  :options="listNicheCategory"
                  placeholder="Select a type"
                ></Multiselect>
                <div class="error" v-if="!$v.form.category_id.required && $v.form.category_id.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Status <span class="_require">*</span></label>
                <Multiselect
                  ref="selectStatus"
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  class="select-type-niche"
                  :class="{ 'form-group--error': $v.form.status.$error }"
                  v-model.trim="$v.form.status.$model"
                  :options="statusNiches"
                  placeholder="Select a status"
                  @select="showAlert"
                ></Multiselect>
                <div class="error" v-if="!$v.form.status.required && $v.form.status.$error">Field is required</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="row-item-form-niche">
            <b-col cols="9">
              <b-row>
                <b-col cols="2">
                  <b-form-group label="Wing">
                    <b-form-input v-model="form.wing" class="input-form" placeholder=""></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col cols="2">
                  <b-form-group>
                    <label class="_label_input">Bay <span class="_require">*</span></label>
                    <b-form-input
                      v-model="form.bay"
                      class="input-form"
                      :class="{ 'form-group--error': $v.form.bay.$error }"
                      placeholder=""
                    ></b-form-input>
                    <div class="error" v-if="!$v.form.bay.required && $v.form.bay.$error">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="2">
                  <b-form-group>
                    <label class="_label_input">Floor <span class="_require">*</span></label>
                    <b-form-input
                      v-model="form.floor"
                      class="input-form"
                      :class="{ 'form-group--error': $v.form.floor.$error }"
                      placeholder=""
                    ></b-form-input>
                    <div class="error" v-if="!$v.form.floor.required && $v.form.floor.$error">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="2">
                  <b-form-group>
                    <label class="_label_input">Block <span class="_require">*</span></label>
                    <b-form-input
                      v-model="form.block"
                      class="input-form"
                      :class="{ 'form-group--error': $v.form.block.$error }"
                      placeholder=""
                    ></b-form-input>
                    <div class="error" v-if="!$v.form.block.required && $v.form.block.$error">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="2">
                  <b-form-group>
                    <label class="_label_input">Level <span class="_require">*</span></label>
                    <b-form-input
                      v-model="form.level"
                      class="input-form"
                      :class="{ 'form-group--error': $v.form.level.$error }"
                      placeholder=""
                    ></b-form-input>
                    <div class="error" v-if="!$v.form.level.required && $v.form.level.$error">Field is required</div>
                  </b-form-group>
                </b-col>
                <b-col cols="2">
                  <b-form-group>
                    <label class="_label_input">Unit <span class="_require">*</span></label>
                    <b-form-input
                      v-model="form.unit"
                      class="input-form"
                      :class="{ 'form-group--error': $v.form.unit.$error }"
                      placeholder=""
                    ></b-form-input>
                    <div class="error" v-if="!$v.form.unit.required && $v.form.unit.$error">Field is required</div>
                  </b-form-group>
                </b-col>
              </b-row>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Price (Excl. GST)">
                <masked-input type="text" v-model="form.price" class="form-control" :mask="numberAmountMask()" :guide="true" placeholder="$0,00">
                </masked-input>
              </b-form-group>
            </b-col>
          </b-row>
        </b-container>
        <div class="line-horizontal" v-if="titleNiches == 'Niche Info'"></div>
        <b-container fluid="lg" class="form-group-niches-price" v-if="titleNiches == 'Niche Info'">
          <div class="gstPrice">Extension Prices (Excl. GST)</div>
          <div class="contact-person-outside-table">
            <div class="content-table">
              <div class="table">
                <ControllTable :optionSearch="optionsFilter" :onChangeSearch="onChangeSearch" :onCreate="onCreateOther" @deleteItems="deleteItem" />
                <TableCustom
                  ref="adminDurationTable"
                  @Items="getItems"
                  :tableFields="columnActive.fields"
                  :tableItems="listDuration.data"
                  @rowClicked="showModal"
                >
                  <template slot="tgor_table:exten_year" slot-scope="data">
                    {{ data.item.exten_year && data.item.exten_year != "" ? +data.item.exten_year : 0 }}
                    years
                  </template>
                  <template slot="tgor_table:exten_price" slot-scope="data">
                    {{ data.item.exten_price && data.item.exten_price != "" ? +data.item.exten_price : 0 | formatMoney }}
                  </template>
                </TableCustom>
              </div>
              <b-row class="pagination">
                <b-col md="12" class="end">
                  <span>
                    {{ listDuration.from ? `${listDuration.from}-${listDuration.to} of ${listDuration.total}` : "0-0 of 0" }}
                  </span>
                  <span class="icon">
                    <b-img class="image" src="/images/left.png" fluid alt="Responsive image"></b-img>
                  </span>
                  <span class="icon">
                    <b-img class="image" src="/images/right.png" fluid alt="Responsive image"></b-img>
                  </span>
                </b-col>
              </b-row>
            </div>
          </div>
          <b-modal
            centered
            ref="duration_modal"
            hide-footer
            id="extension"
            size="sm"
            :title="`${extentParams.id != '' ? 'Edit' : 'Add'} Extension Price`"
          >
            <b-container fluid="lg">
              <b-row>
                <b-col cols="12">
                  <b-form-group label="Extension Duration">
                    <b-form-input
                      class="input-form"
                      :class="{
                        'form-group--error': $v.extentParams.exten_year.$error,
                      }"
                      v-model.trim="$v.extentParams.exten_year.$model"
                    ></b-form-input>
                    <div class="error" v-if="!$v.extentParams.exten_year.required && $v.extentParams.exten_year.$error">Field is required</div>
                    <div class="error" v-if="!$v.extentParams.exten_year.decimal && $v.extentParams.exten_year.$error">Please enter number</div>
                  </b-form-group>
                </b-col>
                <b-col cols="12 mt">
                  <b-form-group label="Price (Excl. GST)">
                    <masked-input
                      type="text"
                      :class="{ 'form-group--error': checkPriceExtent }"
                      v-model.trim="$v.extentParams.exten_price.$model"
                      class="form-control"
                      :mask="numberAmountMask()"
                      :guide="true"
                      placeholder="$0.00"
                    >
                    </masked-input>
                    <div class="error" v-if="checkPriceExtent">Field is required</div>
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
      </div>
    </b-container>
  </div>
</template>

<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import OtherInfo from "@/components/OtherInfo";
import Calendar from "@/components/Icons/Calendar";
import ServiceNichesBookingFields from "@/components/BookingInfo/ServiceNichesBookingFields";
import ItemInfoCurrentBooking from "../../components/AdminNiches/ItemInfoCurrentBooking";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
import moment from "moment";
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";

import { required, minLength, between, decimal } from "vuelidate/lib/validators";
import { validationMixin } from "vuelidate";
import { mapActions, mapState } from "vuex";
import Multiselect from "vue-multiselect";
import MaskedInput from "vue-text-mask";
var accounting = require("accounting");

export default {
  mixins: [validationMixin],
  name: "AdminNichesInfo",
  components: {
    ChevronLeft,
    OtherInfo,
    ItemInfoCurrentBooking,
    Multiselect,
    MaskedInput,
    ControllTable,
    TableCustom,
  },
  props: {
    nicheData: {
      type: Object,
      default: () => {},
    },
  },
  metaInfo: {
    title: "Niche Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Niche Info Description",
      },
    ],
  },
  data() {
    return {
      tabIndex: 0,
      titleNiches: "Add Niche",
      isLoading: false,
      Calendar,
      checkPrice: false,
      checkPriceThirtyYear: false,
      checkPriceFiftyYear: false,
      ids: [],
      statusNiches: ["Available", "Unavailable", "Reserved"],
      form: {
        id: null,
        reference_no: "",
        type_id: "",
        category_id: "",
        price: "",
        price_thirty_years: "",
        price_fifty_years: "",
        bay: "",
        wing: "",
        floor: "",
        block: "",
        level: "",
        unit: "",
        status: "",
        booking_line_item: "",
      },
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        {
          name: "ID",
          value: "id",
        },
        {
          name: "Extension Duration",
          value: "exten_year",
        },
        {
          name: "Price (Excl. GST)",
          value: "exten_price",
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
            key: "exten_year",
            label: "Extension Duration",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "exten_price",
            label: "Price (Excl. GST)",
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
      durationParams: {
        page: 1,
        id: this.$router.history.current.params.id,
        filter: {},
      },
      extentParams: {
        id: "",
        niche_id: this.$router.history.current.params.id,
        exten_year: "",
        exten_price: "",
      },
      checkPriceExtent: false,
    };
  },
  validations: {
    form: {
      reference_no: {
        required,
      },
      type_id: {
        required,
      },
      category_id: {
        required,
      },
      status: {
        required,
      },
      bay: {
        required,
      },
      floor: {
        required,
      },
      block: {
        required,
      },
      level: {
        required,
      },
      unit: {
        required,
      },
    },
    extentParams: {
      exten_year: {
        required,
        decimal,
      },
      exten_price: {
        required,
      },
    },
  },
  computed: mapState({
    listTypes: (state) => state.niche.listTypeNiches,
    nicheDetail: (state) => state.niche.nicheDetail,
    listNicheCategory: (state) => state.niche.listNicheCategory,
    listDuration: (state) => state.niche.listDuration,
  }),
  mouted() {
    this.$nextTick(() => {
      this.$v.form.$reset();
    });
  },
  created() {
    this.titleNiches = this.$router.history.current.params.id ? "Niche Info" : "Add Niche";
    this.getListTypeNiches();
    this.getListCategoryNiche();

    this.handlePanigate(this.durationParams);
  },
  watch: {
    listTypes: function (value, old) {
      if (value.length) {
        if (this.nicheDetail != null) {
          this.$v.form.type_id.$model = this.nicheDetail.type_id;
        } else {
          this.$v.form.type_id.$model = value[0];
        }
      }
    },
    listNicheCategory: function (value, old) {
      if (value.length) {
        if (this.nicheDetail != null) {
          this.$v.form.category_id.$model = this.nicheDetail.category_id;
        } else {
          this.$v.form.category_id.$model = value[0];
        }
      }
    },
    nicheDetail: function (value) {
      if (value != null && value != "" && value != "undefined") {
        this.form = value;
        if (this.form.type) {
          this.form.type_id = {
            ...this.form.type,
          };
        }
        if (this.form.category) {
          this.form.category_id = {
            ...this.form.category,
          };
        }
      }
    },
    "form.price": function (val) {
      if (val == "") {
        this.checkPrice = true;
      } else {
        this.checkPrice = false;
      }
    },
    nicheData: function (val) {
      if (Object.keys(val).length > 0) {
        this.form = val;
      }
    },
    "extentParams.exten_price": function (val) {
      if (val != "") {
        this.checkPriceExtent = false;
      }
      // else{
      //     this.checkPriceExtent = false;
      // }
    },
  },
  filters: {
    formatMoney(val) {
      return accounting.formatMoney(val, {
        format: { pos: "%s %v", neg: "%s (%v)", zero: "--" },
      });
    },
  },
  methods: {
    ...mapActions({
      getListTypeNiches: "niche/getListTypeNiches",
      createNiche: "niche/createNiche",
      updateNiche: "niche/updateNiche",
      getNicheDetail: "niche/getNicheDetail",
      getListCategoryNiche: "niche/getListCategoryNiche",
      getListDuration: "niche/getListDuration",
      createDurationNiches: "niche/createDurationNiches",
      updateDurationNiches: "niche/updateDurationNiches",
      deleteDuration: "niche/deleteDuration",
    }),
    showAlert(name) {
      let old_value = this.form.status;
      if (old_value == "Available" && name == "Unavailable") {
        this.$swal({
          title: "Warning!",
          text: "You are returning this niche to inventory. Proceed?",
          icon: "warning",
          customClass: {
            container: "swal-del-item",
          },
          showCancelButton: true,
          confirmButtonText: "Yes",
          cancelButtonText: "No",
        }).then((result) => {
          if (result.value) {
            this.form.status = name;
            setTimeout(() => {
              this.$refs.selectStatus.$refs.search.blur();
            }, 400);
          } else {
            this.form.status = old_value;
            setTimeout(() => {
              this.$refs.selectStatus.$refs.search.blur();
            }, 400);
          }
        });
      } else if (old_value == "Unavailable" && name == "Available") {
        this.$swal({
          title: "Warning!",
          text: "You are removing this niche from inventory. Proceed?",
          icon: "warning",
          customClass: {
            container: "swal-del-item",
          },
          showCancelButton: true,
          confirmButtonText: "Yes",
          cancelButtonText: "No",
        }).then((result) => {
          if (result.value) {
            this.form.status = name;
            setTimeout(() => {
              this.$refs.selectStatus.$refs.search.blur();
            }, 400);
          } else {
            this.form.status = old_value;
            setTimeout(() => {
              this.$refs.selectStatus.$refs.search.blur();
            }, 400);
          }
        });
      }
    },
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return "";
      }
    },
    handlecreate() {
      this.$v.form.$touch();
      if (this.$v.form.$anyError) {
        if (this.form.price == "") {
          this.checkPrice = true;
        }
        return;
      }

      let action = "createNiche";
      if (this.form.id) {
        action = "updateNiche";
      }
      this.isLoading = true;

      let prms = { ...this.form };

      prms.type_id = this.form.type_id.id;

      prms.category_id = this.form.category_id.id;

      prms.price = this.formatMoney(prms.price);

      prms.price_thirty_years = this.formatMoney(prms.price_thirty_years);

      prms.price_fifty_years = this.formatMoney(prms.price_fifty_years);

      if (prms.booking_line_item) {
        prms.booking_line_item = this.form.booking_line_item.id;
      }

      this[action](prms)
        .then((response) => {
          this.isLoading = false;
          this.$swal({
            icon: "success",
            title: "Notifcation",
            text: response.data.status,
          });
          if (action === "createNiche") {
            this.form.id = response.data.data.id;
            this.$router.replace(`niches/${response.data.data.id}`);
            this.titleNiches = "Niche Info";

            this.$emit("title", this.titleNiches);
            this.extentParams.niche_id = response.data.data.id;
            this.durationParams.id = response.data.data.id;
          }
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
    goBack() {
      this.$router.push("/admin-niches");
    },
    getDetail(item) {
      let prms = { id: item };

      this.getNicheDetail(prms);
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
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    onSubmit() {
      this.$v.extentParams.$touch();
      if (this.$v.extentParams.$anyError) {
        if (this.extentParams.exten_price == "") {
          this.checkPriceExtent = true;
        }
        return;
      }

      this.isLoading = true;

      let actionDuration = "createDurationNiches";

      if (this.extentParams.id != "") {
        actionDuration = "updateDurationNiches";
      }
      let prms = { ...this.extentParams };

      prms.exten_price = this.formatMoney(prms.exten_price);

      this[actionDuration](prms)
        .then((res) => {
          this.$refs.duration_modal.hide();
          this.isLoading = false;
          this.$swal({
            icon: "success",
            title: "Notifcation",
            text: res.data.status,
          });

          this.handlePanigate(this.durationParams);
          this.$refs.adminDurationTable.reloadData();
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
    onCreateOther() {
      this.extentParams.id = "";
      this.extentParams.exten_price = " ";
      this.extentParams.exten_year = "";
      this.$v.extentParams.$reset();
      this.checkPriceExtent = false;
      this.$refs.duration_modal.show();
    },
    showModal(item) {
      this.extentParams.id = item.id;
      this.extentParams.exten_price = item.exten_price;
      this.extentParams.exten_year = item.exten_year;
      this.$refs.duration_modal.show();
    },
    handlePanigate(params) {
      this.isLoading = true;
      this.getListDuration(params)
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
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listDuration;
      clearTimeout(this.actionSearch);
      this.durationParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.getListDuration(this.durationParams);
        }, 300);
      } else {
        this.durationParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.getListDuration(this.durationParams);
        }, 300);
      }
    },
    prevPanigate() {
      let { current_page } = this.listDuration;
      if (current_page != 1) {
        this.durationParams.page = current_page - 1;
        this.getListDuration(this.durationParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listDuration;
      if (current_page != last_page) {
        this.durationParams.page = current_page + 1;
        this.getListDuration(this.durationParams);
      }
    },
    deleteItem() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find this.",
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
            this.deleteDuration(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handlePanigate(this.durationParams);
                this.$refs.adminDurationTable.reloadData();
                this.isLoading = false;
              })
              .catch((error) => {
                this.isLoading = false;
                this.$swal({
                  icon: "error",
                  title: "Oops...",
                  text: error.response.data.errors,
                });
                this.$refs.adminDurationTable.reloadData();
              });
          }
        });
      }
    },
    getItems(item) {
      this.ids = item;
    },
  },
};
</script>
