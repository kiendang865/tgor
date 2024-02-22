<template>
  <div class="payment-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Receipt Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
          <b-button class="btn-send-agreement" @click="onSendEmail" >Send Email</b-button>
          <b-button class="btn-print" @click="onPrint">Print</b-button>
        </div>
      </div>
      <div>
        <DonateInfo ref="PaymentInfo"/>
      </div>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import DonateInfo from "@/components/DonateInfo"
import Calendar from '@/components/Icons/Calendar';
import {
    mapActions,
    mapState
} from 'vuex'
export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    DonateInfo
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
      Calendar
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
      // @click="goBack"
      this.$router.push('/payments')
    },
    onSave(){ 
      this.$refs.PaymentInfo.onSaveSignture();
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