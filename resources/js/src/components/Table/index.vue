<template>
  <div>
    <b-table
      :fields="tableFields"
      :items="tableItems"
      responsive
      fixed
      class="table table-filter"
      @row-clicked="rowClicked"
      :tbody-tr-class="rowClass"
      @sort-changed="sortChanged"
    >
      <template v-slot:cell(actions)="data">
        <b-form-checkbox
          :value="data.item.id"
          v-model="selected"
        ></b-form-checkbox>
      </template>
      <template
        v-slot:[`head(${field.key})`]="data"
        v-for="(field, index) in tableFields"
      >
        <slot
          :name="`tgor_head_table:${field.key}`"
          v-if="field.key !== 'actions'"
          v-bind:item="data"
          v-bind:index="data.index"
          >{{ field.label }}</slot
        >
        <b-form-checkbox
          v-else
          :key="index"
          :value="data.item"
          class="uniform"
          @input="toggleSelected"
          v-model="allSelected"
          :class="{
            'custom-lable-line':
              selected.length < tableItems.length && selected.length > 0,
            'custom-control-label': selected.length == tableItems.length,
            '': selected.length == 0,
          }"
        ></b-form-checkbox>
      </template>
      <template v-slot:head(actions)="data">
        <b-form-checkbox
          :value="data.item"
          class="uniform"
          @input="toggleSelected"
          v-model="allSelected"
          :class="{
            'custom-lable-line':
              selected.length < tableItems.length && selected.length > 0,
            'custom-control-label': selected.length == tableItems.length,
            '': selected.length == 0,
          }"
        ></b-form-checkbox>
      </template>
      <template
        v-slot:[`cell(${field.key})`]="data"
        v-for="(field, index) in tableFields"
      >
        <slot
          :name="`tgor_table:${field.key}`"
          v-if="field.key !== 'actions'"
          v-bind:item="data.item"
          v-bind:index="data.index"
          >{{ data.item | fieldVal(field.key) }}</slot
        >
        <template v-else>
          <b-form-checkbox
            :key="index"
            :value="data.item.id"
            v-model="selected"
          ></b-form-checkbox>
        </template>
      </template>
      <template
        v-if="showFooter"
        class="footerTable"
        colpan="5"
        v-slot:bottom-row=""
      >
        <td colspan="7">
          <span class="total">
            Total
          </span>
        </td>
        <td>{{ totalDetail ? totalDetail.amount : 0 | formatMoney }}</td>
        <td>{{ totalDetail ? totalDetail.gst_amount : 0 | formatMoney }}</td>
        <td>{{ totalDetail ? totalDetail.discount : 0 | formatMoney }}</td>
        <td>{{ totalDetail ? totalDetail.total_amount : 0 | formatMoney }}</td>
      </template>
    </b-table>
  </div>
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
    selectedAll: {
      type: Array,
      default: () => []
    },
    tableCustomSort: {
      type: Boolean,
      default: false,
    }
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
  created() {},
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
    selectedAll: function(val){
      if (val.length == 0) {
        this.allSelected = false;
      } else {
        this.allSelected = true;
        this.selected = val;
      }
    }
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
    sortChanged(e) {
      if(this.tableCustomSort){
        let item = {
          sortby: e.sortBy,
          sortDesc: e.sortDesc
        }
        this.$emit("dataCustomSort", item);
      }
    },
    rowClicked(item) {
      this.$emit("rowClicked", item);
    },
    toggleSelected() {
      if (this.allSelected && this.selectedAll.length < this.tableItems.length) {
        this.selected = [];
        for (let i in this.tableItems) {
          this.selected.push(this.tableItems[i].id);
        }
        return;
      }
      if (!this.allSelected) {
        this.selected = [];
      }
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
