<template>
  <div class="sale-agreements-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Sales Agreement Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-generate-invoice" @click="generateInvoices" :disabled="isCheck">Generate Invoice</b-button>
          <b-button class="btn-save"  @click="onSave" :disabled="isCheck">Save</b-button>
          <b-button class="btn-send-agreement" @click="onSendEmail" :disabled="isAdditionalServices">Send Email</b-button>
          <b-button class="btn-print" @click="onPrint" :disabled="isAdditionalServices">Print</b-button>
        </div>
      </div>
      <div>
        <SaleAgreementInfo ref="SaleAgreementInfo" :saleItem="data" @isSave="checkBtn"/>
      </div>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import SaleAgreementInfo from "@/components/SaleAgreementInfo"
import Calendar from '@/components/Icons/Calendar';
import {
    mapActions,
    mapState
} from 'vuex'
export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    SaleAgreementInfo,
    Calendar
  },
  metaInfo: {
      title: 'Sales Agreement Info',
      meta: [{
      vmid: 'description',
      name: 'description',
      content: 'Sales Agreement Info Description'
      }]
  },   
  data() {
    return {
      tabIndex: 0,
      data:{},
      disSave:false,
      isCheck:false,
      isAdditionalServices: false,
    }
  },
  created(){
    let prms = {id: this.$router.history.current.params.id}
    this.getDetailSaleAreement(prms).then(res => {
      this.data = res;
      this.isCheck = res.is_invoice
      this.isAdditionalServices = res.sale_agreement_item.booking_line_item.booking_type.reference_value_text == "Additional Services";
    })
  },
  computed: mapState({
      saleDetail: state => state.saleareement.saleDetail,
  }),
  methods: {
    ...mapActions({
        getDetailSaleAreement: "saleareement/getDetailSaleAreement",  
        printNichesLicense: "saleareement/printNichesLicense",
        printAgreement: "saleareement/printAgreement",
        sendAgreement: "saleareement/sendAgreement"
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return '';
      }
    },
    goBack(){
      this.$router.push('/sale-agreements')
    },
    onSave(){
      this.$refs.SaleAgreementInfo.onSaveSignture();
    },
    checkBtn(item){
      if(item) return this.disSave = item
    },
    generateInvoices(){
      this.$refs.SaleAgreementInfo.onGenerateInv()
    },
    onPrint(){
        let prms = {id: this.$router.history.current.params.id};
        this.printAgreement(prms).then(res => {
          const url = window.URL.createObjectURL(new Blob([res.data], {type: 'application/pdf'}));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', `Sales_Agreement_${prms.id}.pdf`);
          document.body.appendChild(link);
          link.click();
          if(this.data.sale_agreement_item.booking_line_item.booking_type.reference_value_text == "Niches"){
              this.printNichesLicense(prms).then(res => {
              const url = window.URL.createObjectURL(new Blob([res.data], {type: 'application/pdf'}));
              const link = document.createElement('a');
              link.href = url;
              link.setAttribute('download', `Niches_License_${prms.id}.pdf`);
              document.body.appendChild(link);
              link.click();
            })
          }
        }).catch(error => {
                this.$swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please try again later.',
                });
        })
    },
    onSendEmail(){
      let prms = {id: this.$router.history.current.params.id};
      this.sendAgreement(prms).then(res => {
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
          text: 'Please try again later.',
        });
      })
    }
  },
}
</script>