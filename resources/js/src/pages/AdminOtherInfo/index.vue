<template>
  <div class="admin-other-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title" @click="goBack">
          <span class="title-name">
            <ChevronLeft />
            Additional Services Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div>
        <b-container fluid="lg" class="other-info-admin">
          <b-row>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Service / Product <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.otherParams.service_name.$error,
                  }"
                  v-model.trim="$v.otherParams.service_name.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.otherParams.service_name.required && $v.otherParams.service_name.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Sale / Rental <span class="_require">*</span></label>
                <multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :class="{ 'form-group--error': $v.otherParams.type.$error }"
                  v-model.trim="$v.otherParams.type.$model"
                  :options="listTypeOther"
                  placeholder="Select one"
                  track-by="id"
                  label="reference_value_text"
                ></multiselect>
                <div class="error" v-if="!$v.otherParams.type.required && $v.otherParams.type.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <!-- <b-col cols="3">
                <b-form-group label="Price">
                    <masked-input
                      type="text"
                      :class="{'form-group--error': checkPrice }"
                      v-model.trim="$v.otherParams.price.$model"
                      class="form-control"
                      :mask="numberAmountMask()"
                      :guide="true"
                      placeholder="$0.00">
                    </masked-input>
                    <div class="error" v-if="checkPrice">Field is required</div>
                </b-form-group>
              </b-col> -->
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Contractor Required? <span class="_require">*</span></label>
                <multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :class="{
                    'form-group--error': $v.otherParams.is_contractor.$error,
                  }"
                  v-model.trim="$v.otherParams.is_contractor.$model"
                  :options="listContractorRequired"
                  placeholder="Select one"
                  track-by="id"
                  label="reference_value_text"
                ></multiselect>
                <div class="error" v-if="!$v.otherParams.is_contractor.required && $v.otherParams.is_contractor.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Status <span class="_require">*</span></label>
                <Multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  class="select-type-niche"
                  :class="{ 'form-group--error': $v.otherParams.status.$error }"
                  v-model.trim="$v.otherParams.status.$model"
                  :options="listOtherStatus"
                  track-by="id"
                  label="reference_value_text"
                  placeholder="Select a status"
                ></Multiselect>
                <div class="error" v-if="!$v.otherParams.status.required && $v.otherParams.status.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Category <span class="_require">*</span></label>
                <Multiselect
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  class="select-type-niche"
                  :class="{
                    'form-group--error': $v.otherParams.category.$error,
                  }"
                  v-model.trim="$v.otherParams.category.$model"
                  :options="listOtherCategory"
                  track-by="id"
                  label="reference_value_text"
                  placeholder="Select a category"
                ></Multiselect>
                <!-- <div
                  class="error"
                  v-if="
                    !$v.otherParams.category.required &&
                      $v.otherParams.category.$error
                  "
                >
                  Field is required
                </div> -->
              </b-form-group>
            </b-col>
          </b-row>
          <label class="text-label mt" for="tile-table">Description</label>
          <div class="contact-person-outside-table">
            <div class="content-table" v-bind:class="{ 'run-loading': isLoading }">
              <div class="table">
                <ControllTable :optionSearch="optionsFilter" :onChangeSearch="onChangeSearch" :onCreate="onCreateOther" @deleteItems="deleteItem" />
                <TableCustom
                  ref="adminServiceTypeTable"
                  :tableFields="columnActive.fields"
                  :tableItems="listServiceType.data"
                  @Items="getItems"
                  @rowClicked="showModal"
                >
                  <template slot="tgor_table:price" slot-scope="data">
                    {{ data.item.price && data.item.price != "" ? +data.item.price : 0 | formatMoney }}
                  </template>
                </TableCustom>
              </div>
              <b-row class="pagination">
                <b-col md="12" class="end">
                  <span>
                    {{ listServiceType.from ? `${listServiceType.from}-${listServiceType.to} of ${listServiceType.total}` : "0-0 of 0" }}
                  </span>
                  <span @click="prevPanigate" class="icon">
                    <b-img class="image" src="/images/left.png" fluid alt="Responsive image"></b-img>
                  </span>
                  <span @click="nextPanigate" class="icon">
                    <b-img class="image" src="/images/right.png" fluid alt="Responsive image"></b-img>
                  </span>
                </b-col>
              </b-row>
            </div>
          </div>
        </b-container>
      </div>
      <b-modal centered ref="other_modal" hide-footer id="extension" size="sm" :title="`${serviceOther.id != '' ? 'Edit' : 'Add'} Service Type`">
        <b-container fluid="lg">
          <b-row>
            <b-col cols="12">
              <b-form-group label="Name">
                <b-form-input
                  class="input-form"
                  :class="{
                    'form-group--error': $v.serviceOther.service_name.$error,
                  }"
                  v-model.trim="$v.serviceOther.service_name.$model"
                ></b-form-input>
                <div class="error" v-if="!$v.serviceOther.service_name.required && $v.serviceOther.service_name.$error">Field is required</div>
              </b-form-group>
            </b-col>
            <b-col cols="12 mt">
              <b-form-group label="Price (Excl. GST)">
                <masked-input
                  type="text"
                  :class="{ 'form-group--error': checkPrice }"
                  v-model.trim="$v.serviceOther.price.$model"
                  class="form-control"
                  :mask="numberAmountMask()"
                  :guide="true"
                  placeholder="$0.00"
                >
                </masked-input>
                <div class="error" v-if="checkPrice">Field is required</div>
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
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import OtherInfo from "@/components/OtherInfo";
import Calendar from "@/components/Icons/Calendar";
import Multiselect from "vue-multiselect";
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table/serviceProductTable";
const accounting = require("accounting");

import { required, minLength, between } from "vuelidate/lib/validators";
import { mapActions, mapState } from "vuex";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";

export default {
  name: "",
  components: {
    ChevronLeft,
    OtherInfo,
    Multiselect,
    Calendar,
    MaskedInput,
    ControllTable,
    TableCustom,
  },
  metaInfo: {
    title: "Others Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Others Info Description",
      },
    ],
  },
  data() {
    return {
      tabIndex: 0,
      isLoading: false,
      ids: [],
      otherParams: {
        service_name: "",
        type: "",
        is_contractor: "",
        status: "",
        category: "",
      },
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        {
          name: "Description",
          value: "service_type_name",
        },
        {
          name: "Price",
          value: "price",
        },
      ],
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
            key: "service_name",
            label: "Description",
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
      serviceTypeParams: {
        parent_id: this.$router.history.current.params.id,
        page: 1,
        filter: {},
      },
      serviceOther: {
        id: "",
        parent_id: this.$router.history.current.params.id,
        service_name: "",
        price: "",
      },
    };
  },
  validations: {
    otherParams: {
      service_name: {
        required,
      },
      type: {
        required,
      },
      is_contractor: {
        required,
      },
      status: {
        required,
      },
      category: {},
    },
    serviceOther: {
      service_name: {
        required,
      },
      price: {
        required,
      },
    },
  },
  created() {
    let idOther = this.$router.history.current.params.id;

    this.getDetailOther(idOther);

    this.getListTypeOther();

    this.getListContractorRequired();

    this.getListOtherStatus();

    this.isLoading = true;
    this.getListServiceType(this.serviceTypeParams).then((res) => {
      this.isLoading = false;
    });
    this.getListOtherCategory();
  },
  computed: {
    ...mapState({
      listTypeOther: (state) => state.other.listTypeOther,
      listContractorRequired: (state) => state.other.listContractorRequired,
      listServiceType: (state) => state.other.listServiceType,
      listOtherStatus: (state) => state.other.listOtherStatus,
      listOtherCategory: (state) => state.other.listOtherCategory,
    }),
  },
  methods: {
    ...mapActions({
      getListTypeOther: "other/getListTypeOther",
      updateOther: "other/updateOther",
      getOtherDetail: "other/getOtherDetail",
      getListContractorRequired: "other/getListContractorRequired",
      getListServiceType: "other/getListServiceType",
      createServiceType: "other/createServiceType",
      updateServiceType: "other/updateServiceType",
      deleteOther: "other/deleteOther",
      deleteServiceType: "other/deleteServiceType",
      getListOtherStatus: "other/getListOtherStatus",
      getListOtherCategory: "other/getListOtherCategory",
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return "";
      }
    },
    goBack() {
      this.$router.push("/admin-other");
    },
    onSave() {
      this.$v.otherParams.$touch();
      if (this.$v.otherParams.$anyError) {
        if (this.otherParams.price == "") {
          this.checkPrice = true;
        }
        return;
      } else {
        let prms = { ...this.otherParams };

        prms.type = prms.type.id;
        // prms.price = this.formatMoney(prms.price);
        prms.is_contractor = prms.is_contractor.id;
        prms.status = prms.status.id;
        prms.category_type = prms.category.id;
        this.updateOther(prms)
          .then((res) => {
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });
          })
          .catch((error) => {
            this.$swal({
              icon: "error",
              title: "Oops...",
              text: error.response.data.errors,
            });
          });
      }
    },
    getDetailOther(idOther) {
      this.getOtherDetail(idOther).then((res) => {
        this.otherParams = res.data.data;
        this.otherParams.is_contractor = this.otherParams.contractor;
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
    onCreateOther() {
      this.serviceOther.id = "";
      this.serviceOther.service_name = "";
      this.serviceOther.price = " ";
      this.$v.serviceOther.$reset();
      this.checkPrice = false;
      this.$refs.other_modal.show();
    },
    showModal(item) {
      this.serviceOther.id = item.id;
      this.serviceOther.service_name = item.service_name;
      this.serviceOther.price = item.price;
      this.$refs.other_modal.show();
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listServiceType;
      clearTimeout(this.actionSearch);
      this.serviceTypeParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.getListServiceType(this.serviceTypeParams);
        }, 300);
      } else {
        this.serviceTypeParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.getListServiceType(this.serviceTypeParams);
        }, 300);
      }
    },
    prevPanigate() {
      let { current_page } = this.listServiceType;
      if (current_page != 1) {
        this.serviceTypeParams.page = current_page - 1;
        this.getListServiceType(this.serviceTypeParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listServiceType;
      if (current_page != last_page) {
        this.serviceTypeParams.page = current_page + 1;
        this.getListServiceType(this.serviceTypeParams);
      }
    },
    onSubmit() {
      this.$v.serviceOther.$touch();
      if (this.$v.serviceOther.$anyError) {
        if (this.serviceOther.price == "") {
          this.checkPrice = true;
        }
        return;
      } else {
        this.isLoading = true;

        let action = "createServiceType";

        if (this.serviceOther.id != "") {
          action = "updateServiceType";
        }
        let prms = { ...this.serviceOther };

        prms.price = this.formatMoney(prms.price);

        this[action](prms)
          .then((res) => {
            this.$refs.other_modal.hide();
            this.isLoading = false;
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });

            this.getListServiceType(this.serviceTypeParams);
            this.$refs.adminServiceTypeTable.reloadData();
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
    },
    deleteItem() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find service type.",
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
            this.deleteServiceType(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.getListServiceType(this.serviceTypeParams);
                this.$refs.adminServiceTypeTable.reloadData();
                this.isLoading = false;
              })
              .catch((error) => {
                this.isLoading = false;
                this.$swal({
                  icon: "error",
                  title: "Oops...",
                  text: error.response.data.errors,
                });
                this.$refs.adminServiceTypeTable.reloadData();
              });
          }
        });
      }
    },
    getItems(item) {
      this.ids = item;
    },
  },
  watch: {
    listTypeOther: function (val) {
      if (this.otherParams.type == "" && this.otherParams.type == null) {
        this.otherParams.type = val[0];
      }
    },
    "serviceOther.price": function (val) {
      if (val == "") {
        this.checkPrice = true;
      } else {
        this.checkPrice = false;
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
<style lang="scss">
.tr-hidden-checkbox {
  pointer-events: none;
  cursor: not-allowed;
  .checkbox-column {
    /* .custom-checkbox {
    } */
    .custom-control-label {
      &:after {
        background: #dadada;
        background-color: #dadada !important;
      }
    }
  }
}
</style>
