<template>
  <b-container fluid="lg">
    <div class="columbarium-niches">
      <div class="title">
        <span class="title-name">Additional Services</span>
      </div>
    </div>

    <div class="others-table">
      <div class="content" v-bind:class="{ 'run-loading': isLoading }">
        <div class="outside-table">
          <ControllTable
            :isShowIconAdd="false"
            :isShowIconTrash="false"
            :optionSearch="optionsFilter"
            :onChangeSearch="onChangeSearch"
            @deleteItems="deleteItem"
          />
          <TableCustom
            ref="adminOtherServiceTable"
            :tableFields="columnActive.fields"
            @Items="getItems"
            :tableItems="listServiceOther.data"
            @rowClicked="gotoOtherService"
            :tableCustomSort="true"
            @dataCustomSort="handleCustomSort"
          >
            <template slot="tgor_table:created_at" slot-scope="data">
              {{
                data.item.created_at
                  ? customFormatter(data.item.created_at)
                  : "--"
              }}
            </template>
          </TableCustom>
          <b-row class="pagination">
            <b-col md="12" class="end">
              <span>
                {{
                  listServiceOther.from
                    ? `${listServiceOther.from}-${listServiceOther.to} of ${listServiceOther.total}`
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
    title: "Other Services",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Other Services Description",
      },
    ],
  },
  data() {
    return {
      ids: [],
      isLoading: false,
      serviceOtherParams: {
        page: 1,
        type: "Other",
        filter: {},
        sortby: "service_name",
        sortDesc: false
      },
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        // {
        //     name: 'ID',
        //     value: 'id'
        // },
        {
          name: "Client Name",
          value: "clients_name",
        },
        {
          name: "Service Type",
          value: "service_type",
        },
        {
          name: "Contractor",
          value: "contractor",
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
            key: "index",
            label: "",
            thClass: "checkbox-column text-center",
            tdClass: "checkbox-column text-center",
            thStyle: "width: 20px;height:61px",
            isActive: 1,
          },
          {
            key: "booking_no",
            label: "Item #",
            isActive: 1,
            keySearch: "id",
            type: "text",
            // thStyle: "width:100px",
            isFilter: true,
          },
          {
            key: "created_at",
            label: "Date",
            isActive: 1,
            keySearch: "id",
            type: "text",
            sortable: true,
            // thStyle: "width:100px",
            isFilter: true,
          },
          {
            key: "client.display_name",
            label: `Client's Name`,

            isActive: 1,
            isFilter: true,
          },
          {
            key: "other.service_name",
            label: "Service Type",
            isActive: 1,
            sortable: true,
            isFilter: true,
          },
          {
            key: "service_type.service_name",
            label: "Description",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "contractor.company_name",
            label: "Contractor",
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
    };
  },
  created() {
    this.handlePanigate(this.serviceOtherParams);
  },
  computed: mapState({
    listServiceOther: (state) => state.service.listServiceOther,
  }),
  methods: {
    ...mapActions({
      getListServiceOther: "service/getListServiceOther",
      deleteService: "service/deleteService",
    }),
    gotoOtherService(item) {
      this.$router.push({
        name: "ServiceOtherBookingFields",
        params: { id: item.id },
      });
    },
    handlePanigate(params) {
      this.isLoading = true;
      this.getListServiceOther(params)
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
      let { current_page } = this.listServiceOther;
      if (current_page != 1) {
        this.serviceOtherParams.page = current_page - 1;
        this.handlePanigate(this.serviceOtherParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listServiceOther;
      if (current_page != last_page) {
        this.serviceOtherParams.page = current_page + 1;
        this.handlePanigate(this.serviceOtherParams);
      }
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listServiceOther;
      clearTimeout(this.actionSearch);
      this.serviceOtherParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.serviceOtherParams);
        }, 300);
      } else {
        this.serviceOtherParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.serviceOtherParams);
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
                this.handlePanigate(this.serviceOtherParams);
                this.$refs.adminOtherServiceTable.reloadData();
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
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    handleCustomSort(data){
        this.serviceOtherParams.sortby = data.sortby;
        this.serviceOtherParams.sortDesc = data.sortDesc;
        this.handlePanigate(this.serviceOtherParams);
    }
  },
  watch: {},
};
</script>

<style lang="scss" scoped></style>
