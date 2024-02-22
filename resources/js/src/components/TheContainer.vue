<template>
  <div id="main">
    <div class="sidebar">
      <RVWSidebar />
    </div>
    <div id="content">
      <router-view></router-view>
    </div>
  </div>
</template>
<script>
import RVWSidebar from "./TheSidebar.vue";
export default {
  name: "TheContainer",
  components: {
    RVWSidebar
  }
};
</script>
<style lang="scss" scoped>
    #main{
      display: flex;
      background: #F9F9F9;
      .sidebar{
        width:300px;
        position: fixed;
      }
      #content{
        padding:0 0 0 10px;
        height:100vh;
        //width: calc(100vw - 300px)!important;
        width: 100%;
        padding-left: 310px;
      }
    }
</style>