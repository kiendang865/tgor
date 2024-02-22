<template>
  <div v-bind="{ 'run-loading': isLoading }" class="side-bar">
    <div class="logo_tgor">
      <div class="logo">
        <b-img src="/images/logo_tgor.png" fluid alt="Responsive image"></b-img>
      </div>
    </div>
    <div class="profile_name justify-content-between">
      <div class="user_name">Hello, {{ admin_profile.display_name }}</div>
      <div class="btn-edit-profile" @click="gotoMyAccount">
        <b-img src="/images/edit.jpg" fluid alt="Responsive image"></b-img>
      </div>
    </div>
    <div class="new-booking">
      <b-button class="btn-booking" @click="gotoNewBooking">
        <div class="btn-create">
          <b-img
            src="/images/Create_book.png"
            fluid
            alt="Responsive image"
          ></b-img>
        </div>
        <div class="pd-4">
          Add New Booking
        </div>
      </b-button>
    </div>
    <div class="list-menu">
      <div class="toggle">
        <div class="item" variant="primary">
          <router-link
            to="/booking-general"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'booking-general'"
            :class="{ active: activeTab == 'booking-general' }"
          >
            Booking
          </router-link>
        </div>
        <div class="item" variant="primary">
          <router-link
            to="/sale-agreements"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'sale-agreements'"
            :class="{ active: activeTab == 'sale-agreements' }"
          >
            Sales Agreement
          </router-link>
        </div>
        <div class="item" variant="primary">
          <router-link
            to="/invoices"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'invoices'"
            :class="{ active: activeTab == 'invoices' }"
          >
            Invoices
          </router-link>
        </div>
        <div class="item" variant="primary">
          <router-link
            to="/payments"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'receipt'"
            :class="{ active: activeTab == 'receipt' }"
          >
            Receipts
          </router-link>
        </div>
        <div class="item" variant="primary">
          <router-link
            to="/service-niches"
            class="item item-hover  px-3"
            @click.native="activeTab = 'niches'"
            :class="{ active: activeTab == 'niches' }"
          >
            Niches
          </router-link>
        </div>
        <div class="item" variant="primary">
          <router-link
            to="/memorial-rooms"
            class="item item-hover px-3"
            @click.native="activeTab = 'memorial-room'"
            :class="{ active: activeTab == 'memorial-room' }"
          >
            Memorial Rooms
          </router-link>
        </div>
        <div class="item" variant="primary">
          <router-link
            to="/others"
            class="item item-hover px-3"
            @click.native="activeTab = 'others'"
            :class="{ active: activeTab == 'others' }"
          >
            Additional Services
          </router-link>
        </div>
        <div class="item" variant="primary">
          <router-link
            to="/admin-customer"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'customers-admin'"
            :class="{ active: activeTab == 'customers-admin' }"
          >
            Client Details
          </router-link>
        </div>
        <div class="item" variant="primary">
          <router-link
            to="/report"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'reports'"
            :class="{ active: activeTab == 'reports' }"
          >
            Reports
          </router-link>
        </div>
        <!-- <div class="item" variant="primary">
                <router-link to="/admin-customer" class="item item-hover d-flex align-items-center px-3" @click.native="activeTab = 'customers-admin'" :class="{'active' :activeTab == 'customers-admin'}">
                    Customers
                </router-link>
            </div> -->
        <div
          v-if="admin_profile.roles_id == 1"
          class="item"
          v-b-toggle.collapse-3
          variant="primary"
          @click="checkToggle(3)"
        >
          <div class="item-name">
            Admin Setup
          </div>
          <div class="arrow-right">
            <span class="when-opened">
              <b-img
                src="/images/arrow.png"
                fluid
                alt="Responsive image"
              ></b-img>
            </span>
            <span class="when-closed">
              <b-img
                src="/images/arrow-down.png"
                fluid
                alt="Responsive image"
              ></b-img>
            </span>
          </div>
        </div>
        <b-collapse id="collapse-3" v-model="visibleAdmin">
          <router-link
            to="/admin-niches"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'niches-admin'"
            :class="{ active: activeTab == 'niches-admin' }"
          >
            <div class="indicator-list-icon mr-4"></div>
            Niches
          </router-link>
          <router-link
            to="/admin-memorial-room"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'memorial-room-admin'"
            :class="{ active: activeTab == 'memorial-room-admin' }"
          >
            <div class="indicator-list-icon mr-4"></div>
            Memorial Rooms
          </router-link>
          <router-link
            to="/admin-other"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'others-admin'"
            :class="{ active: activeTab == 'others-admin' }"
          >
            <div class="indicator-list-icon mr-4"></div>
            Additional Services
          </router-link>
          <router-link
            to="/admin-partners"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'contractors'"
            :class="{ active: activeTab == 'contractors' }"
          >
            <div class="indicator-list-icon mr-4"></div>
            Partners
          </router-link>
          <router-link
            to="/admin-discount"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'discount-admin'"
            :class="{ active: activeTab == 'discount-admin' }"
          >
            <div class="indicator-list-icon mr-4"></div>
            Discounts
          </router-link>
          <router-link
            to="/admin-gst"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'adminGST'"
            :class="{ active: activeTab == 'adminGST' }"
          >
            <div class="indicator-list-icon mr-4"></div>
            GST
          </router-link>
          <router-link
            to="/master-address"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'masterAddress'"
            :class="{ active: activeTab == 'masterAddress' }"
          >
            <div class="indicator-list-icon mr-4"></div>
            Master Addresses
          </router-link>
          <router-link
            to="/user-management"
            class="item item-hover d-flex align-items-center px-3"
            @click.native="activeTab = 'userManagement'"
            :class="{ active: activeTab == 'userManagement' }"
          >
            <div class="indicator-list-icon mr-4"></div>
            Users Management
          </router-link>
        </b-collapse>
      </div>
      <div class="item logout" variant="primary">
        <div class="item-name" @click="handleLogout">
          Logout
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import { EventBus } from "../event-bus";
export default {
  data() {
    return {
      visibleService: false,
      visibleFinance: false,
      visibleAdmin: false,
      activeTab: "niches",
      isLoading: false,
      admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.checkActive(this.$route.name);
    });
  },
  created() {
    if (!this.admin_profile) {
      localStorage.removeItem("token_admin");
      this.logout().then(() => {
        this.isLoading = false;
        this.$router.push("login");
      });
    }
    EventBus.$on("getMe", this.getProfile);
  },
  methods: {
    ...mapActions({
      logout: "auth/logout",
      getMe: "user/getMe",
    }),
    gotoNewBooking() {
      this.$router.push("/new-booking");
    },
    checkToggle(stt) {
      switch (stt) {
        case 1:
          this.visibleService = true;
          this.visibleFinance = false;
          this.visibleAdmin = false;
          break;
        case 2:
          this.visibleService = false;
          this.visibleFinance = true;
          this.visibleAdmin = false;
          break;

        case 3:
          this.visibleService = false;
          this.visibleFinance = false;
          this.visibleAdmin = true;
          break;

        default:
          break;
      }
    },
    checkActive(name) {
      switch (name) {
        case "ServiceNiches":
          this.activeTab = "niches";
          this.visibleService = true;
          break;
        case "Others":
          this.activeTab = "others";
          this.visibleService = true;
          break;
        case "MemorialRooms":
          this.activeTab = "memorial-room";
          this.visibleService = true;
          break;
        case "BookingGeneral":
          this.activeTab = "booking-general";
          this.visibleFinance = true;
          break;
        case "SaleAgreements":
          this.activeTab = "sale-agreements";
          this.visibleFinance = true;
          break;
        case "Invoices":
          this.activeTab = "invoices";
          this.visibleFinance = true;
          break;
        case "Payment":
          this.activeTab = "receipt";
          this.visibleFinance = true;
          break;
        case "Report":
          this.activeTab = "reports";
          break;
        case "AdminNiches":
          this.activeTab = "niches-admin";
          this.visibleAdmin = true;
          break;
        case "AdminMemorialRoom":
          this.activeTab = "memorial-room-admin";
          this.visibleAdmin = true;
          break;
        case "AdminOther":
          this.activeTab = "others-admin";
          this.visibleAdmin = true;
          break;
        case "Customer":
          this.activeTab = "customers-admin";
          this.visibleAdmin = true;
          break;
        case "Contractors":
          this.activeTab = "contractors";
          this.visibleAdmin = true;
          break;
        case "UserManagement":
          this.activeTab = "userManagement";
          this.visibleAdmin = true;
          break;
        case "AdminDiscount":
          this.activeTab = "discount-admin";
          this.visibleAdmin = true;
          break;
        case "masterAddress":
          this.activeTab = "master-address";
          this.visibleAdmin = true;
          break;
        case "adminGST":
          this.activeTab = "admin-gst";
          this.visibleAdmin = true;
          break;
        default:
          break;
      }
    },
    handleLogout() {
      this.isLoading = true;
      this.logout()
        .then(() => {
          this.isLoading = false;
          this.$router.push("login");
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
    gotoMyAccount() {
      this.$router.push({ name: "MyAccount" });
    },
    getProfile() {
      this.getMe().then((res) => {
        this.admin_profile = res;
      });
    },
  },
  watch: {
    visibleService: function(val) {},
  },
};
</script>
