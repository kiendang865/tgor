<template>
  <div class="content-tab-table" v-bind:class="{ 'run-loading': isLoading }">
    <div class="outside-tab-table">
      <ControllTable
        :isShowIconExport="true"
        :isShowIconAdd="false"
        :isShowIconTrash="false"
        :optionSearch="optionsFilter"
        :onChangeSearch="onChangeSearch"
        :onExport="exportBookingLog"
        :isShowIconFilter="true"
        @showFilter="showFilterInTable"
        @deleteItems="deleteItem"
      />
      <TableCustom
        class="table-booking-log"
        ref="adminRoomBookingLogTable"
        @Items="getItems"
        :tableFields="columnActive.fields"
        :tableItems="listBookingLog.data"
      >
        <template v-if="isShowFilter" slot="tgor_head_table:check_in_date" slot-scope="data">
          <div>{{ data.item.label }}</div>
          <b-form-input
            v-model="search.check_in_date"
            class="input-table-search"
            placeholder="dd/mm/yyyy"
          ></b-form-input>
        </template>
        <template v-if="isShowFilter" slot="tgor_head_table:check_out_date" slot-scope="data">
          <div>{{ data.item.label }}</div>
          <b-form-input
            v-model="search.check_out_date"
            class="input-table-search"
            placeholder="dd/mm/yyyy"
          ></b-form-input>
        </template>

        <template slot="tgor_table:booking_date" slot-scope="data">
          {{
            data.item.booking_date
              ? customFormatter(data.item.booking_date)
              : "--"
          }}
        </template>
        <template slot="tgor_table:check_in_date" slot-scope="data">
          {{
            data.item.check_in_date
              ? customFormatter(data.item.check_in_date) +
                " " +
                (data.item.check_in_time ? data.item.check_in_time : "")
              : "--"
          }}
        </template>
        <template slot="tgor_table:check_out_date" slot-scope="data">
          {{
            data.item.check_out_date
              ? customFormatter(data.item.check_out_date) +
                " " +
                (data.item.check_out_time ? data.item.check_out_time : "")
              : "--"
          }}
        </template>
      </TableCustom>
      <b-row class="pagination">
        <b-col md="12" class="end">
          <span>
            {{
              listBookingLog.from
                ? `${listBookingLog.from}-${listBookingLog.to} of ${listBookingLog.total}`
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
</template>

<script>
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";
import { mapActions, mapState } from "vuex";
import moment from "moment";
export default {
  components: {
    ControllTable,
    TableCustom,
  },
  data() {
    return {
      isLoading: false,
      roomParams: {
        page: 1,
        filter: {},
        id: this.$router.history.current.params.id,
      },
      ids: [],
      items: [
        {
          id: 2,
          index: "02",
          bookedBy: "Contractor A",
          bookedDate: "James Smith",
          checkedIn: "985684623",
          checkedOut: "james@email.com",
          funeralDirector: "Installing",
          status: "Checked-out",
        },
        {
          id: 2,
          index: "02",
          bookedBy: "Contractor A",
          bookedDate: "James Smith",
          checkedIn: "985684623",
          checkedOut: "james@email.com",
          funeralDirector: "Installing",
          status: "Checked-out",
        },
      ],
      tabIndex: 0,
      activeFilter: false,
      activeStatus: "Active",
      activeClass: false,
      allSelected: false,
      selected: [],
      isShowFilter: false,
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
          // {
          //     key: 'id',
          //     label: 'ID',
          //     isActive: 1,
          //     keySearch: 'id',
          //     type: 'text',
          //     isFilter: true,

          // },
          {
            key: "client.display_name",
            label: "Booked By",
            isActive: 1,
            isFilter: true,
          },
          // {
          //     key: 'booking_date',
          //     label: 'Booked Date',
          //     isActive: 1,
          //     isFilter: true
          // },
          {
            key: "check_in_date",
            label: `Checked In`,
            isActive: 1,
            isFilter: true,
          },
          {
            key: "check_out_date",
            label: `Checked Out`,
            isActive: 1,
            isFilter: true,
          },
          {
            key: "funeral_director.company_name",
            label: `Funeral Director`,
            isActive: 1,
            isFilter: true,
          },
          // {
          //     key: 'status.reference_value_text',
          //     label: `Status`,
          //     isActive: 1,
          //     isFilter: true
          // },
        ],
        show: [],
        hide: [],
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
          name: "Booked By",
          value: "booked_by",
        },
        // {
        //     name: 'Booked Date',
        //     value: 'booked_date'
        // },
        {
          name: "Checked In",
          value: "check_in",
        },
        {
          name: "Checked Out",
          value: "check_out",
        },
        {
          name: "Funeral Director",
          value: "funeral_director",
        },
        // {
        //     name: 'Status',
        //     value: 'status'
        // },
      ],
      search: {
        check_in_date: "",
        check_out_date: "",
      },
    };
  },
  created() {
    this.handlePanigate(this.roomParams);
  },
  computed: mapState({
    listBookingLog: (state) => state.booking.listBookingLog,
  }),
  methods: {
    ...mapActions({
      getListBookingLog: "booking/getListBookingLog",
      deleteService: "service/deleteService",
      exportLogBooking: "booking/exportLogBooking",
    }),
    gotoContractorInfo(item) {
      this.$router.push({ name: "ContractorInfo", params: { id: item.id } });
    },
    customFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    handlePanigate(params) {
      this.isLoading = true;
      this.getListBookingLog(params)
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
      let { current_page } = this.listBookingLog;
      if (current_page != 1) {
        this.roomParams.page = current_page - 1;
        this.handlePanigate(this.roomParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listBookingLog;
      if (current_page != last_page) {
        this.roomParams.page = current_page + 1;
        this.handlePanigate(this.roomParams);
      }
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listBookingLog;
      clearTimeout(this.actionSearch);
      this.roomParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.roomParams);
        }, 300);
      } else {
        this.roomParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.roomParams);
        }, 300);
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
    exportBookingLog() {
      if(!this.ids.length){
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Please Choose Booking Log Item.",
        });
        return;
      }
      let prms = { ids: this.ids };
      this.exportLogBooking(prms).then((res) => {
        const url = window.URL.createObjectURL(new Blob([res.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "booking-log.csv");
        document.body.appendChild(link);
        link.click();
      });
    },
    showFilterInTable(val){
        this.isShowFilter = val
    }
  },
  watch: {
    "search.check_in_date": function(val) {
      this.onChangeSearch(val, { value: "check_in" });
    },
    "search.check_out_date": function(val) {
      this.onChangeSearch(val, { value: "check_out" });
    },
  },
};
</script>

<style lang="scss" scoped>
.outside-tab-table {
  .input-table-search {
    max-width: 180px;
  }
  .h-53px {
    height: 53px;
  }
}
</style>
