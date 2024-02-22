<template>
  <b-table
    :fields="tableFields"
    :items="tableItems"
    responsive
    fixed
    class="table table-filter"
    thead-class="visibility-collapse"
  >
    <template
      v-slot:[`cell(${field.key})`]="data"
      v-for="(field) in tableFields"
    >
      <slot
        :name="`tgor_table:${field.key}`"
        v-if="field.key !== 'actions'"
        v-bind:item="data.item"
        v-bind:index="data.index"
        >{{ data.item | fieldVal(field.key) }}</slot
      >
    </template>
    <template
      v-if="showFooter"
      class="footerTable"
      colpan="5"
      v-slot:bottom-row=""
    >
      <td colspan="7">
        <span class="total">
          Amount Payable
        </span>
      </td>
      <td></td>
      <td></td>
      <td></td>
      <td>{{totalDetail ? unFormatter(totalDetail.total_minus_discount) - minusAmountPaid() : 0 | formatMoney}}</td>
    </template>
  </b-table>
</template>
<script>
const accounting = require("accounting");
import { mapActions, mapState } from "vuex";
export default {
  name: "Table",
  props: {
    tableFields: {
      type: Array,
      default: () => [],
    },
    tableItems: {
      type: Array,
      default: () => [],
    },
    showFooter: {
      type: Boolean,
      default: false,
    },
    // rowClass:{
    //   type:String,
    // },
  },
  data() {
    return {
      selected: [],
      allSelected: false,
      total: {},
    };
  },
  computed: mapState({
    totalDetail: (state) => state.saleareement.totalDetail,
  }),
  watch: {
    selected: function(val) {
      if (val.length == 0) {
        this.allSelected = false;
      } else {
        if (val.length == this.tableItems.length) {
          this.allSelected = true;
        }
      }
      this.$emit("Items", val);
    },
  },
  filters: {
    fieldVal: function(value, key) {
      if (!key) return "";
      let key_tmp = key.split(".");
      if (key_tmp.length > 1) {
        let objData = value;
        for (let i = 0; i < key_tmp.length; i++) {
          objData = objData && objData[key_tmp[i]] ? objData[key_tmp[i]] : null;
          if (objData === null) return null;
        }
        return objData;
      }
      return value[key];
    },
    formatMoney(val) {
      return accounting.formatMoney(val, {
        format: { pos: "%s %v", neg: "%s (%v)", zero: "--" },
      });
    },
  },
  methods: {
    unFormatter(val) {
      return accounting.unformat(val);
    },
    rowClicked(item) {
      this.$emit("rowClicked", item);
    },
    toggleSelected() {
      if (this.allSelected) {
        this.selected = [];
        for (let i in this.tableItems) {
          this.selected.push(this.tableItems[i].id);
        }
        return;
      }
      if (!this.allSelected && this.tableItems.length == this.selected.length) {
        this.selected = [];
      }
    },
    minusAmountPaid(){
      let amount = 0;
      if(this.tableItems.length){
        this.tableItems.map(item => {
          amount += parseFloat(item);
        });
      }
      return amount;
    },
    reloadData() {
      this.allSelected = false;
      this.selected = [];
    },
    rowClass(item) {},
  },
};
</script>
<style lang="scss">
.table-booking-log, .table-admin-niche {
  .table-filter {
    overflow-x: unset !important;
    table {
      &.table {
        th {
          vertical-align: top !important;
          padding: 8px 5px !important;
          .custom-control {
            margin-top: 0 !important;
          }
        }
      }
    }
  }
}
</style>