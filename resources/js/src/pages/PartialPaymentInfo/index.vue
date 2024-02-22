<template>
  <div class="payment-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Partial Payment Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-delete" @click="handleDelete">Delete</b-button>
          <b-button class="btn-save" @click="onSave">Save</b-button>
          <b-button class="btn-send-agreement" @click="onSendEmail" >Send Email</b-button>
          <b-button class="btn-print" @click="onPrint" >Print</b-button>
        </div>
      </div>
      <div>
        <PartialPaymentInfo ref="PartialPaymentInfo" @isSave="checkBtn"/>
      </div>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import PartialPaymentInfo from "@/components/PartialPaymentInfo"
import Calendar from '@/components/Icons/Calendar';
import {
    mapActions,
    mapState
} from 'vuex';
export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    PartialPaymentInfo,
    Calendar
  },
  metaInfo: {
      title: 'Partial Payment',
      meta: [{
      vmid: 'description',
      name: 'description',
      content: 'Partial Payment Description'
      }]
  },  
  data() {
    return {
      tabIndex: 0,
      disSave:false
    }
  },
  methods: {
    ...mapActions({
        printPartialPayment: "payment/printPartialPayment",
        sendPartialPayment: "payment/sendPartialPayment"     
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return '';
      }
    },
    goBack(){
      this.$router.push({name:"PartialPayment", params: { id: this.$router.history.current.params.id}});
    },
    onSave(){ 
      this.$refs.PartialPaymentInfo.onUpdate();
    },
    handleDelete(){
      this.$swal({
        title: 'Permanently delete?',
        text: 'This action is irreversible.',
        icon: 'warning',
        customClass:{
          container: 'swal-del-item',
        },
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.value) {
          this.$refs.PartialPaymentInfo.onDeletePartial();
        }
      })
    },
    onPrint(){ 
        let prms = {id: this.$router.history.current.params.id_partial};
        this.printPartialPayment(prms).then(res => {
          const url = window.URL.createObjectURL(new Blob([res.data], {type: 'application/pdf'}));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'payment.pdf');
          document.body.appendChild(link);
          link.click();
        })
        .catch(error => {
          this.$swal({
            icon: 'error',
            title: 'Oops...',
            text: 'Please save the partial payment.',
          });
        })
    },
    checkBtn(item){
      if(item) return this.disSave = item
    },
    onSendEmail(){
      let prms = {id: this.$router.history.current.params.id_partial};
        this.sendPartialPayment(prms).then(res => {
          this.$swal({
            icon: 'success',
            title: 'Success!',
            text: res.data.status,
          });
        })
        .catch(error => {
          this.$swal({
            icon: 'error',
            title: 'Oops...',
            text: 'Please save the partial payment.',
          });
        })
    }
  },
}
</script>