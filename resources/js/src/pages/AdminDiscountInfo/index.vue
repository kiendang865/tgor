<template>
  <div class="admin-other-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title" @click="goBack">
          <span class="title-name">
            <ChevronLeft />
            Discounts
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div>
        <b-container fluid="lg" class="other-info-admin">
          <b-row>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Service <span class="_require">*</span></label>
                <multiselect
                  :class="{ 'bg-gray': isDisable, 'bg-white': !isDisable }"
                  :show-labels="false"
                  :allow-empty="false"
                  deselect-label=""
                  :options="listTypeBooking"
                  v-model="typeService"
                  placeholder="Select one"
                  track-by="id"
                  label="reference_value_text"
                  :disabled="isDisable"
                ></multiselect>
              </b-form-group>
            </b-col>
          </b-row>
          <template v-if="typeService != null && typeService.reference_value_text == 'Niches'">
            <DiscountNiches :discountDetail="discountDetail" :serviceId="typeService.id" ref="discount_niches" />
          </template>
          <template v-if="typeService != null && typeService.reference_value_text == 'Memorial Rooms'">
            <DiscountRooms :discountDetail="discountDetail" :serviceId="typeService.id" ref="discount_rooms" />
          </template>
          <template v-if="typeService != null && typeService.reference_value_text == 'Additional Services'">
            <DiscountAdditionalServices :discountDetail="discountDetail" :serviceId="typeService.id" ref="discount_additional_services" />
          </template>
        </b-container>
      </div>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import DiscountNiches from "./DiscountNiches.vue";
import DiscountRooms from "./DiscountRooms.vue";
import DiscountAdditionalServices from "./DiscountAdditionalServices.vue";
import Multiselect from "vue-multiselect";
import { mapActions, mapState } from "vuex";
import { isEmpty, isNil } from "lodash";

export default {
  name: "",
  components: {
    ChevronLeft,
    DiscountNiches,
    Multiselect,
    DiscountRooms,
    DiscountAdditionalServices,
  },
  metaInfo: {
    title: "Others Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Others Info Description",
      },
    ],
  },
  data() {
    return {
      typeService: {},
      discountParams: {},
      isDisable: false,
    };
  },
  created() {
    this.getListTypeBooking();
    this.checkDisableField();
    let idDiscount = this.$router.history.current.params.id;
    if (idDiscount) {
      this.getDiscountDetail({
        id: idDiscount,
      })
        .then((response) => {
          this.typeService = response.data.data.service_type;
        })
        .catch((error) => {
          this.$router.replace("/admin-discount-info");
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    }
  },
  computed: {
    ...mapState({
      listTypeBooking: (state) => state.contractor.listTypeBooking,
      discountDetail: (state) => state.discount.discountDetail,
    }),
  },
  methods: {
    ...mapActions({
      getListTypeBooking: "contractor/getListTypeBooking",
      getDiscountDetail: "discount/getDiscountDetail",
    }),
    goBack() {
      this.$router.push("/admin-discount");
    },
    onSave() {
      switch (this.typeService.reference_value_text) {
        case "Niches":
          this.$refs.discount_niches.onSave();
          break;
        case "Memorial Rooms":
          this.$refs.discount_rooms.onSave();
          break;
        case "Additional Services":
          this.$refs.discount_additional_services.onSave();
          break;
        default:
          break;
      }
    },
    checkDisableField() {
      if (!isEmpty(this.$router.history.current.params.id)) {
        return (this.isDisable = true);
      }
      return (this.isDisable = false);
    },
  },
  watch: {
    listTypeBooking: function (val) {
      if (!isEmpty(val) && isEmpty(this.typeService) && isEmpty(this.$router.history.current.params.id)) this.typeService = val[0];
    },
  },
};
</script>
