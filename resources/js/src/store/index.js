import Vue from "vue";
import Vuex from "vuex";
import  Auth from './module/auth'
import Niche from './module/niche';
import Customer from './module/customer';
import Room from './module/room'
import Other from './module/other'
import Contractor from './module/contractor'
import Director from './module/director'
import Booking from './module/booking'
import User from './module/user'
import Report from './module/report'
import Service from './module/service'
import SaleAgreement from './module/saleAgreement'
import Invoices from './module/invoices'
import Attachment from './module/attachment'
import Payment from './module/payment'
import Discount from './module/discount'
import NicheReserved from './module/nichesReserved'
import Address from './module/address'
import Gst from './module/gst'

Vue.use(Vuex);

export default function createStore () {
  return new Vuex.Store({
    modules: {
      auth : Auth,
      niche: Niche,
      customer: Customer,
      room: Room,
      other: Other,
      contractor: Contractor,
      director: Director,
      booking: Booking,
      user: User,
      report:Report,
      service:Service,
      saleareement:SaleAgreement,
      invoice:Invoices,
      attachment:Attachment,
      payment: Payment,
      discount: Discount,
      nichereserved: NicheReserved,
      address: Address,
      gst: Gst,
    }
  })
}
