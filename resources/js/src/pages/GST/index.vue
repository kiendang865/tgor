<template>
  <div class="admin-other-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name">
            GST
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
                <label class="_label_input"
                  >Current Rate (%) <span class="_require">*</span></label
                >
                <b-form-input
                  autofocus
                  :class="{
                    'form-group--error': $v.gstParams.current_rate.$error,
                  }"
                  v-model.trim="$v.gstParams.current_rate.$model"
                  class="input-form"
                ></b-form-input>
                <div
                  class="error"
                  v-if="
                    !$v.gstParams.current_rate.required &&
                    $v.gstParams.current_rate.$error
                  "
                >
                  Field is required
                </div>
                <div
                  class="error"
                  v-else-if="
                    !$v.gstParams.current_rate.decimal &&
                    $v.gstParams.current_rate.$error
                  "
                >
                  Please enter number
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input"
                  >Effective Start Date <span class="_require">*</span></label
                >
                <div class="position-relative input-date">
                  <Datepicker
                    v-model="$v.gstParams.effective_start_date.$model"
                    :class="{
                      'form-group--error':
                        $v.gstParams.effective_start_date.$error,
                    }"
                    :format="customFormatter"
                    class="choose-date"
                    placeholder="dd/mm/yyyy"
                    :disabled-dates="disabledDates"
                  >
                  </Datepicker>
                  <Calendar />
                </div>
                <div
                  class="error"
                  v-if="
                    !$v.gstParams.effective_start_date.required &&
                    $v.gstParams.effective_start_date.$error
                  "
                >
                  Field is required
                </div>
              </b-form-group>
            </b-col>
          </b-row>
          <label class="text-label mt" for="tile-table">GST History Log</label>
          <div class="contact-person-outside-table">
            <div
              class="content-table"
              v-bind:class="{ 'run-loading': isLoading }"
            >
              <div class="table" style="margin-top:5px">
                <!-- <ControllTable
                    :optionSearch="optionsFilter"
                /> -->
                <TableCustom
                  ref="adminServiceTypeTable"
                  :tableFields="columnActive.fields"
                  :tableItems="listGST"
                >
                  <template slot="tgor_table:gst_start_date" slot-scope="data">
                        {{data.item.gst_start_date && data.item.gst_start_date != '' ? customFormatter(data.item.gst_start_date): ''}}
                  </template>
                  <template slot="tgor_table:gst_end_date" slot-scope="data">
                        {{data.item.gst_end_date && data.item.gst_end_date != '' ? customFormatter(data.item.gst_end_date): ''}}
                  </template>
                </TableCustom>
              </div>
              <b-row class="pagination">
                <b-col md="12" class="end">
                  <span>
                    {{
                      listGST.from
                        ? `${listGST.from}-${listGST.to} of ${listGST.total}`
                        : "0-0 of 0"
                    }}
                  </span>
                  <span @click="prevPanigate" class="icon">
                    <b-img
                      class="image"
                      src="/images/left.png"
                      fluid
                      alt="Responsive image"
                    ></b-img>
                  </span>
                  <span @click="nextPanigate" class="icon">
                    <b-img
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
        </b-container>
      </div>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import OtherInfo from "@/components/OtherInfo";
import Calendar from "@/components/Icons/Calendar";
import Multiselect from "vue-multiselect";
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";
import Datepicker from "vuejs-datepicker";

import {
  required,
  minLength,
  between,
  decimal,
} from "vuelidate/lib/validators";
import { mapActions, mapState } from "vuex";
import MaskedInput from "vue-text-mask";
import createNumberMask from "text-mask-addons/dist/createNumberMask";
var accounting = require("accounting");
import moment from "moment";


export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    OtherInfo,
    Multiselect,
    Calendar,
    MaskedInput,
    ControllTable,
    TableCustom,
    Datepicker,
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
      gstParams: {
        current_rate: "",
        effective_start_date: "",
      },
      contractorStatus: ["Yes", "No"],
      columnActive: {
        fields: [
          {
            key: "xxx",
            label: "",
            thClass: "checkbox-column text-center",
            tdClass: "checkbox-column text-center",
            thStyle: "width: 50px",
            isActive: 1,
          },
          {
            key: "name",
            label: "#",
            isActive: 1,
            keySearch: "id",
            type: "text",
            isFilter: true,
          },
          {
            key: "gst_start_date",
            label: "Effective Start Date",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "gst_end_date",
            label: "Effective End Date",
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
      optionsFilter:[],
      items: [
        {
          id: 2,
          index: "02",
          name: "AV Eqpt",
          phone: "$100.00",
        },
      ],
      checkPrice: false,
      historyLogParams: {
        page: 1,
      },
      disabledDates: {
        to: new Date(Date.now() - 8640000)
      }
    };
  },
  validations: {
    gstParams: {
      current_rate: {
        required,
        decimal,
      },
      effective_start_date: {
        required,
      },
    },
  },
  created() {
    this.getListGST(this.historyLogParams);
    this.gstdetail().then(res => {
        this.gstParams.current_rate = Math.floor(res.data.data.rate*100);
        this.gstParams.effective_start_date = res.data.data.gst_start_date
    }).catch(error =>{

    })
    
  },
  computed: {
    ...mapState({
      listGST: (state) => state.gst.listGST,
    }),
  },
  methods: {
    ...mapActions({
      getListGST: "gst/getListGST",
      createGST: "gst/createGST",
      gstdetail: "gst/gstdetail"
    }),
    goBack() {
      //   this.$router.push("/admin-other");
    },
    onSave() {
      this.$v.gstParams.$touch();
      if (this.$v.gstParams.$anyError) {
        return;
      } else {
        let prms = { ...this.gstParams };
        // prms.type = prms.type.id;
        // prms.status = prms.status.id;
        // prms.is_contractor = prms.is_contractor.id;

        this.createGST(prms)
          .then((res) => {
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });
            this.getListGST(this.historyLogParams);
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
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    prevPanigate() {
        let {current_page} = this.listGST;
        if (current_page != 1) {
            this.serviceTypeParams.page = current_page - 1
           this.getListServiceType(this.gstParams)
        }
    },
    nextPanigate() {
        let {current_page, last_page} = this.listGST;
        if (current_page != last_page) {
            this.serviceTypeParams.page = current_page + 1
            this.getListServiceType(this.gstParams)
        }
    },
  },
  watch: {
  },
};
</script>