<template>
  <b-container fluid="lg">
    <div class="columbarium-niches">
      <div class="title">
        <span class="title-name">Discounts</span>
      </div>
    </div>

    <div class="others-table">
      <div class="content" v-bind:class="{ 'run-loading': isLoading }">
        <div class="outside-table">
          <ControllTable
            :isShowIconTrash="showIconTrash"
            :optionSearch="optionsFilter"
            :isShowIconExport="false"
            :onChangeSearch="onChangeSearch"
            @deleteItems="deleteItem"
            :onCreate="onCreateNiche"
          />
          <TableCustom
            ref="adminRoomTable"
            :tableFields="columnActive.fields"
            @Items="getItems"
            :tableItems="listDiscount.data"
            @rowClicked="gotoDiscountInfo"
          >
            <template slot="tgor_table:amount" slot-scope="data">
              {{
                data.item.type_amount &&
                data.item.type_amount.reference_value_text == "Value"
                  ? "$" + data.item.amount
                  : data.item.amount
              }}
            </template>
            <!-- <template slot="tgor_table:price_hourly" slot-scope="data">
                          {{data.item.price_hourly && data.item.price_hourly != ''? data.item.price_hourly : 0 | formatMoney }}
                    </template> -->
          </TableCustom>
          <b-row class="pagination">
            <b-col md="12" class="end">
              <span>
                {{
                  listDiscount.from
                    ? `${listDiscount.from}-${listDiscount.to} of ${listDiscount.total}`
                    : "0-0 of 0"
                }}
              </span>
              <span @click="prevPanigate" class="icon">
                <b-img
                  class="image"
                  src="images/left.png"
                  fluid
                  alt="Responsive image"
                ></b-img>
              </span>
              <span @click="nextPanigate" class="icon">
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
      </div>
    </div>
  </b-container>
</template>

<script>
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";
import { mapActions, mapState } from "vuex";
const accounting = require("accounting");
export default {
  components: {
    ControllTable,
    TableCustom,
  },
  metaInfo: {
    title: "Memorial Rooms",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Memorial Rooms Description",
      },
    ],
  },

  data() {
    return {
      showIconTrash: true,
      admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
      isLoading: false,
      discountParams: {
        page: 1,
        filter: {},
      },
      ids: [],
      tabIndex: 0,
      activeFilter: false,
      activeStatus: "Active",
      activeClass: false,
      allSelected: false,
      selected: [],
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        {
          name: "Discount Name",
          value: "discount_code",
        },
        {
          name: "Discount Type",
          value: "discount_type",
        },
        // {
        //     name: 'Niche Category',
        //     value: 'niche_category'
        // },
        // {
        //     name: 'Niche Type',
        //     value: 'niche_type'
        // },
        // {
        //     name: 'Minimum Qty',
        //     value: 'minimum_qty'
        // },
        {
          name: "	Amount",
          value: "amount",
        },
        {
          name: "	Remarks",
          value: "remarks",
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
            key: "discount_code",
            label: "Discount Name",
            isActive: 1,
            keySearch: "id",
            type: "text",
            thStyle: "width: 150px;",
            isFilter: true,
          },
          {
            key: "type_discount.reference_value_text",
            label: "Discount Type",
            isActive: 1,
            isFilter: true,
          },
          // {
          //     key: `category.reference_value_text`,
          //     label: 'Niche Category',
          //     isActive: 1,
          //     isFilter: true
          // },
          // {
          //     key: 'type.reference_value_text',
          //     label: 'Niche Type',
          //     isActive: 1,
          //     isFilter: true
          // },
          // {
          //     key: 'minimum_qty',
          //     label: 'Minimum Qty',
          //     isActive: 1,
          //     isFilter: true
          // },
          {
            key: "amount",
            label: "Amount",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "remarks",
            label: "Remarks",
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
    if (this.admin_profile.roles_id != 1) {
      this.showIconTrash = false;
    }
    this.handlePanigate(this.discountParams);
  },
  computed: mapState({
    listDiscount: (state) => state.discount.listDiscount,
  }),
  methods: {
    ...mapActions({
      getListDicount: "discount/getListDicount",
      deleteDicount: "discount/deleteDicount",
    }),
    gotoDiscountInfo(item) {
      this.$router.push(`/admin-discount-info/${item.id}`);
    },
    handlePanigate(params) {
      this.isLoading = true;
      this.getListDicount(params)
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
      let { current_page } = this.listDiscount;
      if (current_page != 1) {
        this.discountParams.page = current_page - 1;
        this.handlePanigate(this.discountParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listDiscount;
      if (current_page != last_page) {
        this.discountParams.page = current_page + 1;
        this.handlePanigate(this.discountParams);
      }
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listDiscount;
      clearTimeout(this.actionSearch);
      this.discountParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.discountParams);
        }, 300);
      } else {
        this.discountParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.discountParams);
        }, 300);
      }
    },
    deleteItem() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find discount.",
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
            this.deleteDicount(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handlePanigate(this.discountParams);
                this.$refs.adminRoomTable.reloadData();
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
    onCreateNiche() {
      this.$router.push({
        name: "AdminDiscountInfo",
      });
    },
  },
  filters: {
    formatMoney(val) {
      return accounting.formatMoney(val, {
        format: { pos: "%s %v", neg: "%s (%v)", zero: "--" },
      });
    },
  },
  watch: {},
};
</script>

<style lang="scss" scoped></style>
