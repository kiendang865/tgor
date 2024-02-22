<template>
      <router-view></router-view>
</template>

<script>
  import 'vue2-timepicker/dist/VueTimepicker.css'
 
  export default {
    data() {
      return {
        //
      }
    },
    metaInfo: {
      title: 'The Garden of Rememberance',
      meta: [{
      vmid: 'description',
      name: 'description',
      content: 'The Garden of Rememberance Description'
      }]
  },   
  }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss" scoped>
    #main{
      display: flex;
      background: #F9F9F9;
      .sidebar{
        // float:left;
        width:300px;
      }
      #content{
        padding:0 0 0 10px;
        height:100vh;
        width: calc(100vw - 300px)!important;

      }
    }
    @import '~bootstrap/dist/css/bootstrap.css';
    @import '~bootstrap-vue/dist/bootstrap-vue.css';
    @import '../../sass/app.scss';
    
</style>