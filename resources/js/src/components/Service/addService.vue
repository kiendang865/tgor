<template>
  <b-container fluid="lg">
    <div class="service" :class="{ 'run-loading': isLoading }">
      <template v-if="isDisibleSA">
        <b-button class="controll">
          <b-img class="image" src="/images/trash.png" fluid alt="Responsive image"></b-img>
        </b-button>
      </template>
      <template v-else>
        <b-button class="controll" @click="removeService">
          <b-img class="image" src="/images/trash.png" fluid alt="Responsive image"></b-img>
        </b-button>
      </template>
      <div class="service-type">
        <b-row>
          <b-col cols="12">
            <div class="title-input">Select Service</div>
          </b-col>
          <b-col cols="3">
            <multiselect
              :disabled="isDisibleSA || isDisable"
              :class="{ 'bg-white': !isDisable, 'bg-gray': isDisable }"
              :show-labels="false"
              deselect-label=""
              v-model="value"
              :options="serviceType"
              placeholder="Select Service"
              track-by="id"
              label="reference_value_text"
            ></multiselect>
          </b-col>
        </b-row>
        <div v-if="value != null && value.reference_value_text == 'Niches'" class="service-item">
          <div class="line-horizonal"></div>
          <ServiceNichesBookingFields
            :isDisibleSA="isDisibleSA"
            :hasSaleAgreement="hasSaleAgreement"
            :item="data"
            @serviceItem="mergeServiceItem"
            @loading="setLoading"
            :isAdmin="isAdmin"
            :isInvoice="isInvoice"
          />
        </div>

        <div v-if="value != null && value.reference_value_text == 'Memorial Rooms'" class="service-item">
          <div class="line-horizonal"></div>
          <ServiceRoomBookingFields
            :isDisibleSA="isDisibleSA"
            :hasSaleAgreement="hasSaleAgreement"
            :item="data"
            @serviceItem="mergeServiceItem"
            :isAdmin="isAdmin"
            :isInvoice="isInvoice"
          />
        </div>

        <div v-if="value != null && value.reference_value_text == 'Additional Services'" class="service-item">
          <div class="line-horizonal"></div>
          <ServiceOtherBookingFields
            :isDisibleSA="isDisibleSA"
            :hasSaleAgreement="hasSaleAgreement"
            :item="data"
            @serviceItem="mergeServiceItem"
            :isAdmin="isAdmin"
            :isInvoice="isInvoice"
          />
        </div>
      </div>
    </div>
  </b-container>
</template>

<script>
import ServiceNichesBookingFields from "../BookingInfo/ServiceNichesBookingFields";
import ServiceOtherBookingFields from "../BookingInfo/ServiceOtherBookingFields";
import ServiceRoomBookingFields from "../BookingInfo/ServiceRoomBookingFields";
import Multiselect from "vue-multiselect";

export default {
  components: {
    ServiceNichesBookingFields,
    ServiceOtherBookingFields,
    ServiceRoomBookingFields,
    Multiselect,
  },
  props: {
    serviceType: {
      type: Array,
      default: () => [],
    },
    itemService: {
      type: Object,
      default: () => {},
    },
    status: {
      type: String,
      default: "",
    },
    hasSaleAgreement: {
      type: Boolean,
      default: false,
    },
    isDisibleSA: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      value: null,
      data: "",
      isDisable: false,
      isLoading: false,
      admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
      isAdmin: false,
      isInvoice: false,
    };
  },
  mounted() {},
  created() {
    if (this.admin_profile) {
      this.isAdmin = this.admin_profile?.roles_id !== 1 ? true : false;
    }
    if (this.itemService != null) {
      this.value = this.itemService.booking_type;
      this.data = this.itemService;
      if (this.itemService.booking_type) {
        this.isDisable = true;
      }
      if (this.itemService.booking?.is_invoice === 1) {
        this.isInvoice = true;
      }
    }
  },
  methods: {
    setLoading(val) {
      this.isLoading = val;
    },
    removeService() {
      this.$emit("removeService");
    },
    mergeServiceItem(item, status) {
      if (!status) {
        let prms = item;

        prms.booking_type_id = this.itemService.booking_type_id;

        this.itemService = Object.assign(this.itemService, prms);
        if (this.itemService.other) {
          if (this.itemService.other.children) {
            delete this.itemService.other.children;
            delete this.itemService.other.created_at;
            delete this.itemService.other.updated_at;
            delete this.itemService.other.deleted_at;
          }
        }
        delete this.itemService.deleted_at;
        delete this.itemService.booking;
        delete this.itemService.niche;
        delete this.itemService.room;
        delete this.itemService.client;
        delete this.itemService.event;
        delete this.itemService.booking_type;
        delete this.itemService.get_discount;
        delete this.itemService.other;
        delete this.itemService.referral;
        this.$emit("chooseType", this.itemService);
        return;
      }
    },
  },
  watch: {
    value: function (val) {
      if (val != null && val != "" && val != "undefined") {
        this.itemService.booking_type_id = val.id;
      } else {
        this.itemService.booking_type_id = "";
      }
    },
  },
};
</script>
