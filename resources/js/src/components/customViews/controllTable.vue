<template>
  <b-container fluid="lg" class="controllTable" :class="{ 'mb-15': activeFilter || isShowFilter }">
    <b-row class="head-actions">
      <b-col cols="4" class="pull-content-left">
        <div class="content-left d-flex">
          <span v-on:click="onCreate" class="search" v-if="isShowIconAdd">
            <b-img class="image" src="/images/Create.png" fluid alt="Responsive image"></b-img>
          </span>
          <span class="search" @click="deleteItems" v-if="isShowIconTrash">
            <b-img class="image" src="/images/trash.png" fluid alt="Responsive image"></b-img>
          </span>
          <span class="search" @click="downloadItems" v-if="isShowIconDownLoad">
            <b-img class="image" src="/images/download.jpg" fluid alt="Responsive image"></b-img>
          </span>
          <span class="btn-extentsion" @click="extension" v-if="isShowBtnExt">Extension</span>
          <span class="bulk-action" v-if="isShowBulkAction">
            <b-dropdown text="Bulk Actions">
              <b-dropdown-item @click.native="setBulkAction('Extension Prices')">Extension Prices</b-dropdown-item>
              <b-dropdown-item @click.native="setBulkAction('Niche Prices')">Niche Prices</b-dropdown-item>
              <b-dropdown-item @click.native="setBulkAction('Niche Status')">Niche Status</b-dropdown-item>
            </b-dropdown>
          </span>
        </div>
      </b-col>
      <b-col lg="6" xl="7" class="filter">
        <!-- name="fade" --->
        <transition name="fade">
          <b-row class="show" v-if="activeFilter || isShowFilter">
            <b-col cols="2"> </b-col>
            <b-col cols="6" class="pr-0">
              <b-form-input @input="onChangeInputSearch" class="input-form"></b-form-input>
            </b-col>
            <b-col cols="4" class="pr-0">
              <multiselect
                open-direction="bottom"
                class="w-95"
                :show-labels="false"
                deselect-label=""
                v-model="valueSearch"
                :options="optionSearch"
                placeholder="Select"
                label="name"
                track-by="name"
              ></multiselect>
            </b-col>
          </b-row>
        </transition>
      </b-col>
      <b-col lg="2" xl="1" class="pull-content-right pl-0">
        <div style="cursor: pointer" v-if="isShowIconSearch" @click="activeFilter = !activeFilter">
          <b-icon-search style="fill: #999999; width: 14px; height: 14px" />
        </div>
        <transition name="fade-button" mode="out-in" v-if="isShowIconFilter">
          <div
            style="cursor: pointer"
            @click="
              active = !active;
              showFilterOnParent(active);
            "
          >
            <b-img v-if="!active" class="image" src="/images/filter.png" alt="Responsive image"></b-img>
            <b-img v-if="active" class="image" src="/images/filter_active.png" alt="Responsive image"></b-img>
          </div>
        </transition>

        <div v-if="isShowIconExport" v-on:click="onExport">
          <b-img class="image" src="/images/export.png" fluid alt="Responsive image"></b-img>
        </div>
      </b-col>
    </b-row>
    <b-row v-if="quantilySelect != 0">
      <b-col cols="12">
        <div class="alert-select text-center p-2 mb-1">
          <span
            >All <strong>{{ quantilySelect }}</strong> niches on this page are selected
          </span>
          <span class="ml-3 select-all" @click="selectAllNiches">Select all niches that match this search</span>
        </div>
      </b-col>
    </b-row>
  </b-container>
</template>

<script src="https://cdnjs.cloudflare.com/ajax/libs/velocity/1.2.3/velocity.min.js"></script>
<script>
import Multiselect from "vue-multiselect";
import { BIconSearch } from "bootstrap-vue";
export default {
  components: {
    Multiselect,
    BIconSearch,
  },
  props: {
    isShowIconAdd: {
      type: Boolean,
      default: true,
    },
    isShowIconTrash: {
      type: Boolean,
      default: true,
    },
    isShowBtnExt: {
      type: Boolean,
      default: false,
    },
    isShowIconExport: {
      type: Boolean,
      default: false,
    },
    onCreate: {
      type: Function,
      default: () => null,
    },
    onExport: {
      type: Function,
      default: () => null,
    },
    onChangeSearch: {
      type: Function,
      default: () => null,
    },
    optionSearch: {
      type: Array,
      default: [],
    },
    isShowFilter: {
      type: Boolean,
      default: false,
    },
    isShowIconSearch: {
      type: Boolean,
      default: true,
    },
    isShowIconFilter: {
      type: Boolean,
      default: false,
    },
    isShowBulkAction: {
      type: Boolean,
      default: false,
    },
    isShowIconDownLoad: {
      type: Boolean,
      default: false,
    },
    quantilySelect: {
      type: [String, Number],
      default: 0,
    },
  },
  data() {
    return {
      activeFilter: false,
      valueSearch: null,
      dataTextSearch: null,
      admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
      active: false,
    };
  },
  created() {
    if (this.optionSearch.length) {
      this.valueSearch = this.optionSearch[0];
    }
  },
  methods: {
    onChangeInputSearch(textSearch) {
      this.dataTextSearch = textSearch;
      this.onChangeSearch(textSearch, this.valueSearch);
    },
    deleteItems() {
      this.$emit("deleteItems");
    },
    extension() {
      this.$emit("extension");
    },
    showFilterOnParent(action) {
      this.$emit("showFilter", action);
    },
    setBulkAction(name) {
      this.$emit("bulkAction", name);
    },
    downloadItems() {
      this.$emit("downloadItems");
    },
    selectAllNiches() {
      this.$emit("selectAllNiches");
    },
  },
  watch: {
    valueSearch: function (val) {
      if (this.dataTextSearch != null) {
        this.onChangeSearch(this.dataTextSearch, val);
      }
    },
  },
};
</script>
<style lang="scss">
.alert-select {
  font-size: 14px;
  background: #ace5cd;
  margin-top: 40px;
  > span:nth-child(1) {
    opacity: 0.7;
  }
  > span:nth-child(2) {
    font-weight: bold;
  }
  .select-all {
    cursor: pointer;
    &:hover {
      text-decoration: underline;
    }
  }
}
</style>
