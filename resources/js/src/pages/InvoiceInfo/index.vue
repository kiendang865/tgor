<template>
  <div class="page-invoice-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title" @click="goBack">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Invoice Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-generate-payment" @click="generatePayment">Generate Payment</b-button>
          <b-button class="btn-save" @click="onSave">Save</b-button>
          <b-button class="btn-send-agreement" @click="onSendEmail">Send Email</b-button>
          <b-button class="btn-print" @click="onPrint">Print</b-button>
        </div>
      </div>
      <div>
        <InvoiceInfo ref="InvoicesInfo" @isSave="checkBtn"/>
      </div>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import InvoiceInfo from "@/components/InvoiceInfo"
import Calendar from '@/components/Icons/Calendar';
import {
    mapActions,
    mapState
} from 'vuex'

export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    InvoiceInfo,
    Calendar
  },
  data() {
    return {
      tabIndex: 0,
      data:{},
      disSave:false
    }
  },
  metaInfo: {
    title: 'Invoice Info',
    meta: [{
    vmid: 'description',
    name: 'description',
    content: 'Invoice Info Description'
    }]
  },
  created(){
    let prms = {id: this.$router.history.current.params.id}
    this.getDetailInvoices(prms).then(res => {
      this.data = res
    })
  },
  computed: mapState({
      invoiceDetail: state => state.invoices.invoiceDetail,
  }),
  methods: {
    ...mapActions({
        getDetailInvoices: "invoice/getDetailInvoices",  
        printInvoices: "invoice/printInvoices",
        sendEmail: "invoice/sendEmail"   
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return '';
      }
    },
    goBack(){
      this.$router.push('/invoices')
    },
    onSave(){ 
      this.$refs.InvoicesInfo.onSaveSignture();
    },
    generatePayment(){
      this.$refs.InvoicesInfo.onGeneratePay()
    },
    onPrint(){
       let prms = {id: this.$router.history.current.params.id};
        this.printInvoices(prms).then(res => {
          const url = window.URL.createObjectURL(new Blob([res.data], {type: 'application/pdf'}));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', `Invoice_${prms.id}.pdf`);
          document.body.appendChild(link);
          link.click();
        }).catch(error =>{
                this.$swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.response.data.errors,
                });
        })
    },
    checkBtn(item){
      if(item) return this.disSave = item
    },
    onSendEmail(){
      let prms = {id: this.$router.history.current.params.id};
      this.sendEmail(prms).then(res => {
        this.getDetailInvoices(prms).then(res => {
          this.data = res
        });
        this.$swal({
          icon: 'success',
          title: 'Success!',
          text: res.data.status,
        });
      }).catch(error =>{
        this.$swal({
          icon: 'error',
          title: 'Oops...',
          text: 'Please try again later.',
        });
      })
    }
  },
}
</script>