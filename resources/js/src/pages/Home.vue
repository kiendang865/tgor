<template>
  <b-container fluid="lg">
    <div class="columbarium-niches">
      <div class="title">
        <span class="title-name">Columbarium Niches</span>
      </div>
    </div>
    <div>
      <b-tabs class="tabs-index" v-model="tabIndex" nav-class="nav-cus">
        <b-tab nav-class="booking" title="Booking" v-bind:class="linkClass(0)">
          <b-container fluid="lg">
            <div class="outside-table">
              <ControllTable></ControllTable>
              <Table :tableFields="columnActive.fields" :tableItems="items"/>
              <b-row class="pagination">
                <b-col md="12" class="end">
                  <span>
                    1-8 of 1240
                  </span>
                  <span class="icon">
                    <b-img
                      class="image"
                      src="/images/left.png"
                      fluid
                      alt="Responsive image"
                    ></b-img>
                  </span>
                  <span class="icon">
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
          </b-container>
        </b-tab>
        <b-tab
          nav-class="all-niches"
          title="All Niches"
          v-bind:class="linkClass(1)"
          ></b-tab
        >
      </b-tabs>
    </div>
  </b-container>
</template>

<script>
import ControllTable from "@/components/customViews/controllTable.vue";
import Table from '@/components/Table';
export default {
  components: {
    ControllTable,
    Table
  },
  data() {
    return {
      items: [
        {
          id: 1,
          niche_id: "R001",
          location: "Blk A, Level 1, Section A1, 1002",
          applicant_name: "John Smith",
          duration_of_lease: "99 Years / 99 Years Left",
          death_anniversary: "24/10",
          status: "Occupied",
        },
        {
          id: 2,
          niche_id: "R002",
          location: "Blk A, Level 1, Section A1, 1002",
          applicant_name: "John Smith",
          duration_of_lease: "99 Years / 99 Years Left",
          death_anniversary: "24/10",
          status: "Occupied",
        },
        {
          id: 3,
          niche_id: "R003",
          location: "Blk A, Level 1, Section A1, 1002",
          applicant_name: "John Smith",
          duration_of_lease: "99 Years / 99 Years Left",
          death_anniversary: "24/10",
          status: "Occupied",
        },
        {
          id: 4,
          niche_id: "R004",
          location: "Blk A, Level 1, Section A1, 1002",
          applicant_name: "John Smith",
          duration_of_lease: "99 Years / 99 Years Left",
          death_anniversary: "24/10",
          status: "Occupied",
        },
        {
          id: 5,
          niche_id: "R005",
          location: "Blk A, Level 1, Section A1, 1002",
          applicant_name: "John Smith",
          duration_of_lease: "99 Years / 99 Years Left",
          death_anniversary: "24/10",
          status: "Occupied",
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
            thStyle: "width:50px",
            isActive: 1,
          },
          {
            key: "id",
            label: "#",
            isActive: 1,
            keySearch: "id",
            type: "text",
            isFilter: true,
            thStyle: "width:50px",
          },
          {
            key: "niche_id",
            label: "Niche ID",
            thStyle: "width:50px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "location",
            label: "Location",
            thStyle: "width:300px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "applicant_name",
            label: "Applicant Name",
            thStyle: "width:200px",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "duration_of_lease",
            label: "Duration of Lease",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "death_anniversary",
            label: "Death Anniversary",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "status",
            label: "Status",
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
    };
  },
  methods: {
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return "abc";
      } else {
        return "";
      }
    },
    filterActive() {
      this.activeClass = !this.activeClass;
    },
  },
};
</script>

<style lang="scss" scoped></style>
