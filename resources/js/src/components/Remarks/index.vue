<template>
  <div class="content-tab-table" v-bind:class="{ 'run-loading': isLoading }">
    <div class="outside-tab-table">
      <ControllTable
        :isShowIconAdd="true"
        :isShowIconTrash="false"
        :optionSearch="optionsFilter"
        :onChangeSearch="onChangeSearch"
        @deleteItems="deleteItem"
        :onCreate="onCreateRemarks"
        :isShowIconDownLoad="true"
        @downloadItems="downloadFileZip"
      />
      <TableCustom
        ref="adminRoomBookingLogTable"
        @Items="getItems"
        :tableFields="columnActive.fields"
        :tableItems="listServiceRemarks.data"
        @rowClicked="showModal"
      >
        <template slot="tgor_table:date" slot-scope="data">
          {{
            data.item.created_at ? customFormatter(data.item.created_at) : "--"
          }}
        </template>
        <template slot="tgor_table:time" slot-scope="data">
          {{
            data.item.created_at
              ? customFormatterTime(data.item.created_at)
              : "--"
          }}
        </template>
        <template slot="tgor_table:name_file" slot-scope="data">
          <!-- {{ data.item.name_file ? splitFileName(data.item.name_file) : "" }} -->
           <router-link class="download" @click.native="downloadItem(data.item.id)" to="">
               {{ data.item.name_file ? splitFileName(data.item.name_file) : "" }}
          </router-link>
        </template>
      </TableCustom>
      <b-row class="pagination">
        <b-col md="12" class="end">
          <span>
            {{
              listServiceRemarks.from
                ? `${listServiceRemarks.from}-${listServiceRemarks.to} of ${listServiceRemarks.total}`
                : "0-0 of 0"
            }}
          </span>
          <span class="icon" @click="prevPanigate">
            <b-img
              class="image"
              src="/images/left.png"
              fluid
              alt="Responsive image"
            ></b-img>
          </span>
          <span class="icon" @click="nextPanigate">
            <b-img
              class="image"
              src="/images/right.png"
              fluid
              alt="Responsive image"
            ></b-img>
          </span>
        </b-col>
      </b-row>
      <b-modal
        centered
        ref="other_modal"
        hide-footer
        id="extension"
        size="sm"
        :title="titlePopup"
      >
        <b-container fluid="lg">
          <b-row>
            <b-col cols="12">
              <b-form-group>
                <label class="_label_input">Remarks <span class="_require">*</span></label>
                <b-form-textarea
                  id="textarea"
                  placeholder="Enter something..."
                  rows="4"
                  max-rows="6"
                  :class="{
                    'form-group--error': $v.prmsRemarks.remarks.$error,
                  }"
                  v-model.trim="$v.prmsRemarks.remarks.$model"
                ></b-form-textarea>
                <div
                  class="error"
                  v-if="
                    !$v.prmsRemarks.remarks.required &&
                      $v.prmsRemarks.remarks.$error
                  "
                >
                  Field is required
                </div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="12">
              <b-form-group label="Upload File">
                <b-form-file
                  v-model="prmsRemarks.file"
                  class="mt-3"
                  id="remarks-file"
                  plain
                ></b-form-file>
                <label for="remarks-file" class="btn-file-report"
                  >Select File</label
                >
                <div class="name-file">
                  {{
                    prmsRemarks.file && prmsRemarks.file.name
                      ? prmsRemarks.file.name
                      : ""
                  }}
                </div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="btn-submit">
            <b-col cols="12">
              <div class="submit" @click="onSubmit">
                Submit
              </div>
            </b-col>
          </b-row>
        </b-container>
      </b-modal>
    </div>
  </div>
</template>

<script>
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";
import { mapActions, mapState } from "vuex";
import moment from "moment";
import { required } from "vuelidate/lib/validators";
import axios from "axios";
export default {
  components: {
    ControllTable,
    TableCustom,
  },
  data() {
    return {
      isLoading: false,
      remarksParams: {
        page: 1,
        filter: {},
        booking_line_item_id: this.$router.history.current.params.id,
      },
      ids: [],
      tabIndex: 0,
      activeFilter: false,
      activeStatus: "Active",
      activeClass: false,
      allSelected: false,
      selected: [],
      prmsRemarks: {
        id: "",
        booking_line_item_id: this.$router.history.current.params.id,
        user_id: JSON.parse(localStorage.getItem("admin_profile")).id,
        remarks: "",
        file: null,
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
            key: "date",
            label: "Date",
            isActive: 1,
            keySearch: "id",
            type: "text",
            thStyle: "width: 120px",
            isFilter: true,
          },
          {
            key: "time",
            label: "Time",
            isActive: 1,
            isFilter: true,
            thStyle: "width: 100px",
          },
          {
            key: "user.display_name",
            label: "User Name",
            isActive: 1,
            isFilter: true,
            thStyle: "width: 150px",
          },
          {
            key: "remarks",
            label: `Remarks`,
            isActive: 1,
            isFilter: true,
          },
          {
            key: "name_file",
            label: `Attachment File`,
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
          name: "Date",
          value: "date",
        },
        {
          name: "Time",
          value: "time",
        },
        {
          name: "User Name",
          value: "user_name",
        },
        {
          name: "Remarks",
          value: "remarks",
        },
      ],
      titlePopup: "Add Remarks",
    };
  },
  validations: {
    prmsRemarks: {
      remarks: {
        required,
      },
    },
  },
  created() {
    this.handlePanigate(this.remarksParams);
  },
  computed: mapState({
    listServiceRemarks: (state) => state.service.listServiceRemarks,
  }),
  methods: {
    ...mapActions({
      getListRemarks: "service/getListRemarks",
      createRemarks: "service/createRemarks",
      updateRemarks: "service/updateRemarks",
      downloadRemarksFile: "service/downloadRemarksFile",
      downloadRemarksZip: "service/downloadRemarksZip"
    }),
    gotoContractorInfo(item) {
      this.$router.push({ name: "ContractorInfo", params: { id: item.id } });
    },
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    customFormatterTime(date) {
      return moment(date).format("HH:mm");
    },
    handlePanigate(params) {
      this.isLoading = true;
      this.getListRemarks(params)
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
      let { current_page } = this.listServiceRemarks;
      if (current_page != 1) {
        this.remarksParams.page = current_page - 1;
        this.handlePanigate(this.remarksParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listServiceRemarks;
      if (current_page != last_page) {
        this.remarksParams.page = current_page + 1;
        this.handlePanigate(this.remarksParams);
      }
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listServiceRemarks;
      clearTimeout(this.actionSearch);
      this.remarksParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.remarksParams);
        }, 300);
      } else {
        this.remarksParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.remarksParams);
        }, 300);
      }
    },
    deleteItem() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find remarks.",
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
            let prms = { type: "Room", ids: this.ids };
            this.deleteService(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handlePanigate(this.roomParams);
                this.$refs.adminRoomBookingLogTable.reloadData();
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
    onCreateRemarks() {
      this.titlePopup = "Add Remarks";
      this.prmsRemarks.id = "";
      this.prmsRemarks.remarks = "";
      this.prmsRemarks.file = null;
      this.$v.prmsRemarks.$reset();
      this.$refs.other_modal.show();
    },
    async showModal(item) {
      this.titlePopup = "Edit Remarks";
      this.prmsRemarks.id = item.id;
      this.prmsRemarks.remarks = item.remarks;
      if (item.file_url) {
        await this.getFile(item.file_url);
      } else {
        this.prmsRemarks.file = null;
      }
      this.$refs.other_modal.show();
    },
    onSubmit() {
      this.$v.prmsRemarks.$touch();
      if (this.$v.prmsRemarks.$anyError) {
        return;
      }
      let formData = new FormData();
      let prms = this.prmsRemarks;
      for (let key in prms) {
        let val = prms[key] == null ? "" : prms[key];
        formData.append(key, val);
      }
      if (this.prmsRemarks.id != "") {
        let data = {
          id: this.prmsRemarks.id,
          formData: formData,
        };
        this.updateRemarks(data)
          .then((res) => {
            this.handlePanigate(this.remarksParams);
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });
            this.$refs.other_modal.hide();
          })
          .catch((error) => {
            this.isLoading = false;
            this.$swal({
              icon: "error",
              title: "Oops...",
              text: error.response.data.errors,
            });
            this.$refs.other_modal.hide();
          });
        return;
      }
      this.createRemarks(formData)
        .then((res) => {
          this.handlePanigate(this.remarksParams);
          this.$swal({
            icon: "success",
            title: "Notifcation",
            text: res.data.status,
          });
          this.$refs.other_modal.hide();
        })
        .catch((error) => {
          this.isLoading = false;
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
          this.$refs.other_modal.hide();
        });
    },
    getFile(url) {
      this.isLoading = true;
      return new Promise((resolve, reject) => {
        axios
          .get(url, {
            responseType: "arraybuffer",
          })
          .then((result) => {
            let parts = url.split("/");
            let name = parts[parts.length - 1];
            let blob = new Blob([result.data], {
              type: result.headers["content-type"],
            });
            let file = new File([blob], name, blob);
            this.prmsRemarks.file = file;
            this.isLoading = false;
            resolve(file);
          })
          .catch((error) => {
            this.isLoading = false;
            reject(error);
          });
      });
    },
    splitFileName(url) {
      // let parts = url.split("/");
      // let name = parts[parts.length - 1];
      return url;
    },
    downloadFileZip(){
      let prms = { ids: this.ids };
      if(prms.ids.length == 0){
        this.$swal({
            icon: "warning",
            title: "Warning!",
            text: "Please choose remarks",
          });
          return;
      }
      this.downloadRemarksZip(prms).then((res) => {
        if(res.data){
            const url = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement("a");
            link.href = url;
            let filename = 'remarks.zip';
            link.setAttribute("download", filename);
            document.body.appendChild(link);
            link.click();
        }
      });
    },
    downloadItem(id){
      let prms = { id: id };
      this.downloadRemarksFile(prms).then((res) => {
            let url = res.data.data.file_url;
            return new Promise((resolve, reject) => {
                return axios.request({
                    baseURL: `${window.location.origin}/`,
                    method: 'GET',
                    url: url,
                    responseType: 'blob',
                })
                .then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', res.data.data.name_file);
                    document.body.appendChild(link);
                    link.click();
                    });
                });
      });
    }
  },
  watch: {
  },
};
</script>

<style lang="scss" scoped>
#remarks-file {
  opacity: 0;
  position: absolute;
  z-index: -1;
}
.name-file{
  overflow: hidden;
  text-overflow: ellipsis;
}


</style>
