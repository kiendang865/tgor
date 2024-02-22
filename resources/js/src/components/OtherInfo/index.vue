<template>
  <b-container fluid="lg" class="other-info">
    <b-row>
      <b-col cols="3">
        <b-form-group label="Product Name">
          <b-form-input class="input-form "></b-form-input>
        </b-form-group>
      </b-col>
      <b-col cols="3">
        <b-form-group label="Price">
            <b-form-input class="input-form "></b-form-input>
        </b-form-group>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
export default {
  name: "",
  components: {  },
  data() {
    return {
      state: {
        date1: null,
        date2: null
      }
    }
  },
};
</script>
