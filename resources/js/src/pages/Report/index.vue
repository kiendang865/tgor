<template>
  <b-container fluid="lg">
    <div class="columbarium-niches">
      <div class="title">
        <span class="title-name">Reports</span>
      </div>
    </div>

    <div class="others-table">
      <div class="content" :class="{ 'run-loading': isLoading }">
        <div class="outside-table">
          <ControllTable
            @deleteItems="deleteItem"
            :optionSearch="optionsFilter"
            :isShowIconExport="false"
            :onChangeSearch="onChangeSearch"
            :onCreate="onCreateReport"
          />
          <TableCustom :tableFields="columnActive.fields" :tableItems="listReport.data" @Items="getItems">
            <template ref="reportTable" slot="tgor_table:remarks" slot-scope="data">
              <router-link class="download" @click.native="forceFileDownload(data.item)" to=""> Download </router-link>
            </template>
            <template ref="reportTable" slot="tgor_table:start_time" slot-scope="data">
              {{ data.item.start_time ? customFormatter(data.item.start_time) : "--" }}
            </template>
            <template ref="reportTable" slot="tgor_table:end_time" slot-scope="data">
              {{ data.item.end_time ? customFormatter(data.item.end_time) : "--" }}
            </template>
          </TableCustom>
          <b-row class="pagination">
            <b-col md="12" class="end">
              <span>
                {{ listReport.from ? `${listReport.from}-${listReport.to} of ${listReport.total}` : "0-0 of 0" }}
              </span>
              <span class="icon">
                <b-img class="image" src="images/left.png" fluid alt="Responsive image"></b-img>
              </span>
              <span class="icon">
                <b-img class="image" src="images/right.png" fluid alt="Responsive image"></b-img>
              </span>
            </b-col>
          </b-row>
        </div>
      </div>
    </div>
    <b-modal centered ref="other_modal" hide-footer id="extension" size="sm" :title="`${reportParams.id != '' ? 'Edit' : 'Add'} Report`">
      <b-container fluid="lg">
        <b-row>
          <b-col cols="12" class="mt">
            <b-form-group>
              <label class="_label_input">Start Date <span class="_require">*</span></label>
              <div class="position-relative input-date">
                <Datepicker
                  v-model="$v.reportParams.start_time.$model"
                  :format="customFormatter"
                  class="choose-date start-date"
                  placeholder="dd/mm/yyyy"
                ></Datepicker>
                <Calendar />
              </div>
              <div class="error" v-if="!$v.reportParams.start_time.required && $v.reportParams.start_time.$error">Field is required</div>
            </b-form-group>
          </b-col>
          <b-col cols="12" class="mt">
            <b-form-group>
              <label class="_label_input">End Date <span class="_require">*</span></label>
              <div class="position-relative input-date">
                <Datepicker
                  v-model="$v.reportParams.end_time.$model"
                  :format="customFormatter"
                  class="choose-date start-date"
                  placeholder="dd/mm/yyyy"
                ></Datepicker>
                <Calendar />
              </div>
              <div class="error" v-if="!$v.reportParams.end_time.required && $v.reportParams.end_time.$error">Field is required</div>
            </b-form-group>
          </b-col>
          <b-col cols="12" class="mt">
            <b-form-group>
              <label class="_label_input">Select Service <span class="_require">*</span></label>
              <multiselect
                :show-labels="false"
                :allow-empty="false"
                deselect-label=""
                v-model="$v.reportParams.service.$model"
                :options="listServiceReport"
                placeholder="Select one"
                label="reference_value_text"
                track-by="id"
              ></multiselect>
              <div class="error" v-if="!$v.reportParams.service.required && $v.reportParams.service.$error">Field is required</div>
            </b-form-group>
          </b-col>
          <b-col cols="12" class="mt">
            <b-form-group>
              <label class="_label_input">Report name <span class="_require">*</span></label>
              <b-form-input
                :class="{
                  'form-group--error': $v.reportParams.name.$error,
                }"
                v-model.trim="$v.reportParams.name.$model"
                class="input-form"
              ></b-form-input>
              <div class="error" v-if="!$v.reportParams.name.required && $v.reportParams.name.$error">Field is required</div>
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
//import
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";
import axios from "axios";
import Multiselect from "vue-multiselect";
import Datepicker from "vuejs-datepicker";
import Calendar from "@/components/Icons/Calendar";
import moment from "moment";

import { mapActions, mapState } from "vuex";
import { required, minLength, between } from "vuelidate/lib/validators";

export default {
  components: {
    ControllTable,
    TableCustom,
    Multiselect,
    Datepicker,
    Calendar,
  },
  metaInfo: {
    title: "Reports",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Reports Description",
      },
    ],
  },
  data() {
    return {
      isLoading: false,
      reportParams: {
        page: 1,
        filter: {},
      },
      ids: [],
      items: [
        {
          id: 4,
          report: "INV-004",
          remarks: "Download",
        },
        {
          id: 3,
          report: "INV-003",
          remarks: "Download",
        },
        {
          id: 2,
          report: "INV-002",
          remarks: "Download",
        },
        {
          id: 1,
          report: "INV-001",
          remarks: "Download",
        },
      ],
      tabIndex: 0,
      activeFilter: false,
      activeStatus: "Active",
      activeClass: false,
      allSelected: false,
      selected: [],

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
            key: "id",
            label: "#",
            isActive: 1,
            keySearch: "id",
            type: "text",
            thStyle: "width: 150px",
            isFilter: true,
          },
          {
            key: "name",
            label: "Report Title",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "start_time",
            label: "Start Date",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "end_time",
            label: "End Date",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "remarks",
            label: `Remarks`,
            isActive: 1,
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
          name: "ID",
          value: "id",
        },
        {
          name: "Report",
          value: "title",
        },
      ],
      reportParams: {
        id: "",
        start_time: "",
        end_time: "",
        service: "",
        name: "",
      },
      listTypeContact: [],
    };
  },
  validations: {
    reportParams: {
      start_time: {
        required,
      },
      end_time: {
        required,
      },
      service: {
        required,
      },
      name: {
        required,
      },
    },
  },
  created() {
    this.handlePanigate(this.reportParams);
    this.getListServiceReport();
  },
  computed: mapState({
    listReport: (state) => state.report.listReport,
    listServiceReport: (state) => state.report.listServiceReport,
  }),
  methods: {
    ...mapActions({
      getListReport: "report/getListReport",
      deleteReport: "report/deleteReport",
      addReport: "report/addReport",
      createReport: "report/createReport",
      updateReport: "report/updateReport",
      getListServiceReport: "report/getListServiceReport",
      downloadReportFile: "report/downloadReportFile",
    }),
    handlePanigate(params) {
      this.isLoading = true;
      this.getListReport(params)
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
      let { current_page } = this.listReport;
      if (current_page != 1) {
        this.reportParams.page = current_page - 1;
        this.handlePanigate(this.reportParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listReport;
      if (current_page != last_page) {
        this.reportParams.page = current_page + 1;
        this.handlePanigate(this.reportParams);
      }
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listReport;
      clearTimeout(this.actionSearch);
      this.reportParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.reportParams);
        }, 300);
      } else {
        this.reportParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.reportParams);
        }, 300);
      }
    },
    deleteItem() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find report.",
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
            this.deleteReport(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handlePanigate(this.reportParams);
                this.$refs.reportTable.reloadData();
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
    forceFileDownload(item) {
      this.downloadReportFile({ id: item.id })
        .then((response) => {
          const url = window.URL.createObjectURL(new Blob([response]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", item.name + ".xlsx");
          document.body.appendChild(link);
          link.click();
        })
        .catch((error) => {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
    onCreateReport() {
      // this.$v.contactPersonParams.$reset()
      this.reportParams.id = "";
      this.reportParams.title = "";
      this.reportParams.file = "";
      this.$v.reportParams.$reset();
      this.$refs.other_modal.show();
    },
    showModal(item) {
      this.$v.reportParams.$reset();
      this.reportParams.id = item.id;
      this.reportParams.title = item.title;
      this.reportParams.file = { name: item.name };
      this.$refs.other_modal.show();
    },
    onSubmit() {
      this.$v.reportParams.$touch();
      if (this.$v.reportParams.$anyError) {
        return;
      }

      let payload = {
        end_time: this.customFormatForSave(this.reportParams.end_time),
        start_time: this.customFormatForSave(this.reportParams.start_time),
        name: this.reportParams.name,
        service: this.reportParams.service.id,
      };

      this.createReport(payload)
        .then((res) => {
          this.handlePanigate(this.reportParams);
          this.$swal({
            icon: "success",
            title: "Notifcation",
            text: res.data.status,
          });
          //reset form
          for (let key in this.reportParams) {
            this.reportParams[key] = "";
          }
          //reset validate
          this.$v.$reset();
          this.$refs.other_modal.hide();
        })
        .catch((error) => {
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
    customFormatForSave(date) {
      return moment(date).format("YYYY-MM-DD");
    },
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
  },
  watch: {
    file: function (val) {},
  },
};
</script>

<style lang="scss" scoped></style>
