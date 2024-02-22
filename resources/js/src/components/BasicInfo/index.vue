<template>
  <b-container fluid="lg" class="admin-setup-basic-info">
    <b-row>
      <b-col cols="3">
        <b-form-group label="Room No.">
          <b-form-input class="input-form"></b-form-input>
        </b-form-group>
      </b-col>
      <b-col cols="6">
        <b-form-group label="Location">
          <b-form-input class="input-form"></b-form-input>
        </b-form-group>
      </b-col>
    </b-row>
    <b-row class="mt">
      <b-col cols="3">
        <b-form-group label="TV Dimention">
          <multiselect
            :show-labels="false"
            deselect-label=""
            v-model="valueNiche"
            :options="optionsNiche"
            placeholder="Select one"
            label="name"
            track-by="name"
          ></multiselect>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Price">
          <b-form-input class="input-form"></b-form-input>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Status">
          <multiselect
            :show-labels="false"
            deselect-label=""
            v-model="valueNiche"
            :options="optionsNiche"
            placeholder="Select one"
            label="name"
            track-by="name"
          ></multiselect>
        </b-form-group>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import Multiselect from "vue-multiselect";

export default {
  name: "ServiceNichesBookingFields",
  components: { Datepicker, Multiselect },
  data() {
    return {
      state: {
        date1: null,
        date2: null,
      },
      valueNiche: null,
      optionsNiche: [{ name: "R001" }, { name: "R002" }, { name: "R003" }, { name: "R004" }, { name: "R005" }],
    };
  },
};
</script>
