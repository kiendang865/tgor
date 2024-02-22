<template>
  <div class="payment-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Partial Payment
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
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
      title: 'Receipt Info',
      meta: [{
      vmid: 'description',
      name: 'description',
      content: 'Receipt Info Description'
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
        printPayment: "payment/printPayment",
        sendPayment: "payment/sendPayment"     
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return '';
      }
    },
    goBack(){
      this.$router.push({name:"PaymentInfo", params: { id: this.$router.history.current.params.id}});
    },
    onSave(){ 
      this.$refs.PartialPaymentInfo.onSaveSignture();
    },
    onPrint(){ 
      let prms = {id: this.$router.history.current.params.id};
      this.printPayment(prms).then(res => {
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
          text: 'Please save the payment.',
        });
      })
    },
    checkBtn(item){
      if(item) return this.disSave = item
    },
    onSendEmail(){
      let prms = {id: this.$router.history.current.params.id};
        this.sendPayment(prms).then(res => {
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
            text: 'Please save the payment.',
          });
        })
    }
  },
}
</script>