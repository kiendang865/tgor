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
    >
      <template v-slot:cell(actions)="data">
        <b-form-checkbox
          :value="data.item.id"
          v-model="selected"
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
              selected.length + itemPayment < tableItems.length && selected.length > 0,
            'custom-control-label': selected.length + itemPayment == tableItems.length,
            '': selected.length == 0,
          }"
        ></b-form-checkbox>
      </template>
      <template v-slot:[`cell(${field.key})`]="data" v-for="(field,index) in tableFields">
          <slot :name="`tgor_table:${field.key}`"  v-if="field.key !== 'actions'" v-bind:item="data.item" v-bind:index="data.index">{{data.item | fieldVal(field.key)}}</slot>
          <template v-else>
            <b-form-checkbox
              :key="index"
              :value="data.item.id"
              v-model="selected"
            ></b-form-checkbox>
          </template>
      </template>
      <template v-if="showFooter" class="footerTable" colpan="5" v-slot:bottom-row="">
       <td colspan="7">
        <span class="total">
          Total
        </span>
       </td>
       <td>{{totalDetail ? totalDetail.amount :0 | formatMoney}}</td>
       <td>{{totalDetail ? totalDetail.gst_amount :0 | formatMoney}}</td>
       <td>{{totalDetail ? totalDetail.total_amount :0 | formatMoney}}</td>
      </template>
    </b-table>
  </div>
</template>
<script>
const accounting = require('accounting');
import {
    mapActions,
    mapState
} from 'vuex'
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
    showFooter:{
      type:Boolean,
      default: false
    },
    // rowClass:{
    //   type:String,
    // },
  },
  data() {
    return {
      selected: [],
      allSelected: false,
      total:{},
      itemPayment:0
    };
  },
  created() {
  },
  computed: mapState({
      totalDetail: state => state.saleareement.totalDetail,
  }),
  watch: {
    selected: function(val) {
      if (val.length == 0) {
        this.allSelected = false;
      } else { 
      let slItem = (val.length + this.itemPayment)
        if (slItem == this.tableItems.length) {
          this.allSelected = true;
        }
      }
      this.$emit('Items',val)
    },
    tableItems: function(val){
        if(val.length){
            val.map(item => {
                if(item.is_payment == 1){
                    this.itemPayment += 1;
                }
            })
        }
    }
  },
  filters:{
      fieldVal: function(value,key) {
          if(!key) return '';
          let key_tmp = key.split(".");
          if(key_tmp.length > 1){
              let objData = value;
              for (let i = 0 ; i < key_tmp.length; i++){
                  objData = (objData && objData[key_tmp[i]]) ? objData[key_tmp[i]] : null;
                  if(objData === null) return null;
              }
              return objData;
          }
          return value[key];
      },
      formatMoney(val) {
          return accounting.formatMoney(val, { format: { pos : "%s %v", neg : "%s (%v)", zero: "--" } })

      }
  },
  methods: {
    rowClicked(item) {
      this.$emit("rowClicked",item)
    },
    toggleSelected() {  
      if (this.allSelected) {
        if(this.selected.length == 0)
        {
          this.selected = [];
          for (let i in this.tableItems) {
              if(this.tableItems[i].time_now != true){
                  this.selected.push(this.tableItems[i].id);
              }
          }
          return;
        }

      }
      else{
         this.selected = [];
      }
      if (!this.allSelected && this.tableItems.length == this.selected.length + this.itemPayment) {
        this.selected = [];
      }
    },
    reloadData(){
      this.allSelected = false;
      this.selected = [];
    },
    rowClass(item){ 
      if(item!= null && item.time_now == true){
        return 'hidden-row'
      }
      return "";
    }
  },
};
</script>
