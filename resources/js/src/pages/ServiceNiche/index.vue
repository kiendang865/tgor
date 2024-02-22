<template>
  <div class="service-niches">
    <b-container fluid="lg">
      <div class="columbarium-niches">
        <div class="title">
          <span class="title-name">Columbarium Niches</span>
        </div>
      </div>
      <div class="wrapper-table">
        <b-container fluid="lg" v-bind:class="{ 'run-loading': isLoading }">
          <div class="outside-table">
            <ControllTable
              :isShowIconAdd="false"
              :isShowIconTrash="false"
              :optionSearch="optionsFilter"
              :onChangeSearch="onChangeSearch"
              @deleteItems="deleteItem"
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
                    <template
                      v-else-if="data.item.information.length == key + 1"
                    >{{ item.full_name ? ", " + item.full_name : "" }}</template>
                    <template v-else>{{ item.full_name ? item.full_name : "" }}</template>
                  </span>
                </div>
                <div v-else>--</div>
              </template>
            </Table>
            <b-row class="pagination">
              <b-col md="12" class="end">
                <span>
                  {{
                    listServiceNiches.from
                      ? `${listServiceNiches.from}-${listServiceNiches.to} of ${listServiceNiches.total}`
                      : "0-0 of 0"
                  }}
                </span>
                <span class="icon" @click="prevPanigate">
                  <b-img
                    class="image"
                    src="images/left.png"
                    fluid
                    alt="Responsive image"
                  ></b-img>
                </span>
                <span class="icon" @click="nextPanigate">
                  <b-img
                    class="image"
                    src="images/right.png"
                    fluid
                    alt="Responsive image"
                  ></b-img>
                </span>
              </b-col>
            </b-row>
          </div>
        </b-container>
      </div>
    </b-container>
  </div>
</template>
<script>
import ControllTable from "@/components/customViews/controllTable.vue";
import Table from "@/components/Table";
import { mapActions, mapState } from "vuex";
import moment from "moment";

export default {
  name: "ServiceNiches",
  components: { ControllTable, Table },
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
      isLoading: false,
      ids: [],
      serviceNicheParams: {
        page: 1,
        type: "Niche",
        filter: {},
      },
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        {
          name: "Client Name",
          value: "client_name",
        },
        {
          name: "Niche ID",
          value: "niche_id",
        },
        {
          name: "Occupant",
          value: "occupant",
        }
      ],
      columnActive: {
        fields: [
          {
            key: "index",
            label: "",
            thClass: "checkbox-column text-center",
            tdClass: "checkbox-column text-center",
            thStyle: "width:20px",
            isActive: 1,
          },
          {
            key: "booking_no",
            label: "Item #",
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
            key: "niche.location",
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
            key: "status.reference_value_text",
            label: "Status",
            isActive: 1,
            thStyle: "width:100px",
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
    };
  },
  created() {
    this.handlePanigate(this.serviceNicheParams);
  },
  computed: mapState({
    listServiceNiches: (state) => state.service.listServiceNiches,
  }),
  methods: {
    ...mapActions({
      getListServiceNiches: "service/getListServiceNiches",
      deleteService: "service/deleteService",
      exportNiches: "service/exportNiches",
    }),
    gotoServiceNiches(item) {
      this.$router.push({
        name: "BookingInfoServiceNiches",
        params: { id: item.id },
      });
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
      if(date && date != 'undefinded')
        return moment(date).format("DD/MM");
      else
        return '--';
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
        }, 1000);
      } else {
        this.serviceNicheParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.serviceNicheParams);
        }, 1000);
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
  },
};
</script>
