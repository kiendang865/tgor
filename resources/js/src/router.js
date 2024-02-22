import VueRouter from "vue-router";

// Pages
import Home from "./pages/Home";
import Register from "./pages/Register";
import Login from "./pages/Login";
import NewBooking from "./pages/Booking"
import ServiceNiches from './pages/ServiceNiche';
import BookingInfoServiceNiches from './pages/BookingInfo/ServiceNichesBooking';
import BookingInfoServiceRoom from './pages/BookingInfo/ServiceRoomBooking';
import ServiceOtherBookingFields from './pages/BookingInfo/ServiceOtherBooking'
import Others from './pages/Others';
import Report from './pages/Report'
import AdminMemorialRoom from "./pages/AdminMemorialRoom"
import AdminOther from "./pages/AdminOther"
import Customer from "./pages/Customers"
import Contractors from "./pages/Partners"
import ContractorInfo from "./pages/ContractorInfo"
import FuneralDirectorInfo from "./pages/FuneralDirectorInfo"
import CustomerInfo from "./pages/CustomerInfo"
import AdminOtherInfo from "./pages/AdminOtherInfo"
import AdminRoomInfo from "./pages/AdminRoomInfo"
import SaleAgreementInfo from "./pages/SaleAgreementInfo"
import BookingGeneral from "./pages/BookingGeneral"
import BookingGeneralInfo from "./pages/BookingGeneralInfo"
import CreateCustomer from "./pages/CustomerInfo/createCustomer.vue"
import CreateOther from "./pages/AdminOtherInfo/createOther.vue"
import CreateRoom from "./pages/AdminRoomInfo/createRoom.vue"
import CreateContractor from "./pages/ContractorInfo/createContractor.vue"
import CreateDirector from "./pages/FuneralDirectorInfo/createDireactor.vue"
import MyAccount from "./pages/MyAccount"
import UserManagement from "./pages/UserManagement"
import UserInfo from './pages/UserInfo'
import CreateUser from './pages/UserInfo/createUser.vue'
import DonateInfo from './pages/DonateInfo'
import AdminDiscount from './pages/AdminDiscount'
import AdminDiscountInfo from './pages/AdminDiscountInfo'
import NicheReserved from './pages/NicheReserved'
import NewNicheReserved from './pages/NicheReservedInfo/createNicheReserved'
import MasterAddess from './pages/MasterAddress'
import MasterAddessInfo from './pages/MasterAddressInfo'
import GST from './pages/GST'

import TheContainer from "./components/TheContainer.vue";
import SaleAgreements from './pages/SaleAgreements';
import Invoices from './pages/Invoices';
import InvoiceInfo from "./pages/InvoiceInfo"
import MemorialRooms from './pages/MemorialRooms';
import Niches from './pages/AdminNiches';
import Payment from './pages/Payment';
import PaymentInfo from './pages/PaymentInfo';
import AdminNichesInfo from './pages/AdminNichesInfo';
import ForgetPassword from './pages/ForgetPassword.vue'
import ConfirmPassword from './pages/ConfirmPassword.vue'
import PartialPaymentInfo from './pages/PartialPaymentInfo';
import PartialPayment from './pages/PartialPayment';

//
import store from './store/module/auth'
// Routes
const routes = [
  {
    path: "/",
    name: "Container",
    component: TheContainer,
    meta: {
      requiresAuth : true,
    },
    children: [
      {
        path: "/",
        name: "ServiceNiches",
        component: ServiceNiches,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "service-niches",
        name: "ServiceNiches",
        component: ServiceNiches,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/new-booking",
        name: "NewBooking",
        component: NewBooking,
        meta: {
          requiresAuth : true,
        },
      },

      {
        path: "service-niches/booking-info/:id",
        name: "BookingInfoServiceNiches",
        component: BookingInfoServiceNiches,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "service-room/booking-info/:id",
        name: "BookingInfoServiceRoom",
        component: BookingInfoServiceRoom,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "other/booking-info/:id",
        name: "ServiceOtherBookingFields",
        component: ServiceOtherBookingFields,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/others",
        name: "Others",
        component: Others,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/sale-agreements",
        name: "SaleAgreements",
        component: SaleAgreements,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/invoices",
        name: "Invoices",
        component: Invoices,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/invoice-info/:id",
        name: "InvoiceInfo",
        component: InvoiceInfo,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/memorial-rooms",
        name: "MemorialRooms",
        component: MemorialRooms,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/admin-niches",
        name: "AdminNiches",
        component: Niches,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "admin-niches/niches",
        name: "AdminNichesCreate",
        component: AdminNichesInfo,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "admin-niches/niches/:id",
        name: "AdminNichesInfo",
        component: AdminNichesInfo,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "/payments",
        name: "Payment",
        component: Payment,
      },
      {
        path: "/payment-info/:id",
        name: "PaymentInfo",
        component: PaymentInfo,
      },
      {
        path: "/partial-payment/:id",
        name: "PartialPayment",
        component: PartialPayment,
      },
      {
        path: "/partial-payment/:id/partial-payment-info/:id_partial",
        name: "PartialPaymentInfo",
        component: PartialPaymentInfo,
      },
      {
        path: "/report",
        name: "Report",
        component: Report,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/admin-memorial-room",
        name: "AdminMemorialRoom",
        component: AdminMemorialRoom,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "/admin-other",
        name: "AdminOther",
        component: AdminOther,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "/admin-customer",
        name: "Customer",
        component: Customer,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/admin-partners",
        name: "Contractors",
        component: Contractors,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "/contractors-info/:id",
        name: "ContractorInfo",
        component: ContractorInfo,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "/new-contractors",
        name: "CreateContractor",
        component: CreateContractor,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "/new-director",
        name: "CreateDirector",
        component: CreateDirector,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      
      {
        path: "/funeral-director-info/:id",
        name: "FuneralDirectorInfo",
        component: FuneralDirectorInfo,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      }, 
      {
        path: "/customer-info/:id",
        name: "CustomerInfo",
        component: CustomerInfo,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/new-customer",
        name: "CreateCustomer",
        component: CreateCustomer,
        meta: {
          requiresAuth : true,
        },
      },  
      {
        path: "/admin-other-info/:id",
        name: "AdminOtherInfo",
        component: AdminOtherInfo,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      }, 
      {
        path: "/admin-new-other",
        name: "CreateOther",
        component: CreateOther,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      }, 
      {
        path: "/admin-new-room",
        name: "CreateRoom",
        component: CreateRoom,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      }, 
      {
        path: "/admin-room-info/:id",
        name: "AdminRoomInfo",
        component: AdminRoomInfo,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      }, 
      {
        path: "/sale-agreement-info/:id",
        name: "SaleAgreementInfo",
        component: SaleAgreementInfo,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/booking-general",
        name: "BookingGeneral",
        component: BookingGeneral,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/booking-general-info/:id",
        name: "BookingGeneralInfo",
        component: BookingGeneralInfo,
        meta: {
          requiresAuth : true,
        },
      },    
      {
        path: "/my-account",
        name: "MyAccount",
        component: MyAccount,
        meta: {
          requiresAuth : true,
        },
      },  
      {
        path: "/user-management",
        name: "UserManagement",
        component: UserManagement,
        meta: {
          requiresAuth : true,
        },
      }, 
      {
        path: "/user-info/:id",
        name: "UserInfo",
        component: UserInfo,
        meta: {
          requiresAuth : true,
        },
      }, 
      {
        path: "/user-info",
        name: "CreateUser",
        component: CreateUser,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/donate-info/:id",
        name: "DonateInfo",
        component: DonateInfo,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/admin-discount",
        name: "AdminDiscount",
        component: AdminDiscount,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "/admin-discount-info",
        name: "AdminDiscountInfo",
        component: AdminDiscountInfo,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "/admin-discount-info/:id",
        name: "AdminDiscountInfo",
        component: AdminDiscountInfo,
        meta: {
          requiresAuth : true,
          requiresSuperAdmin: true
        },
      },
      {
        path: "/reserved-niches",
        name: "NicheReserved",
        component: NicheReserved,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/new-niches-reserved",
        name: "NewNicheReserved",
        component: NewNicheReserved,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/reserved-niches/:id",
        name: "NicheReserved",
        component: NewNicheReserved,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/master-address",
        name: "MasterAddess",
        component: MasterAddess,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/new-address",
        name: "NewAddress",
        component: MasterAddessInfo,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/master-address/:id",
        name: "AddressInfo",
        component: MasterAddessInfo,
        meta: {
          requiresAuth : true,
        },
      },
      {
        path: "/admin-gst",
        name: "GST",
        component: GST,
        meta: {
          requiresAuth : true,
        },
      },
    ],
  },
  {
    path: "/register",
    name: "register",
    component: Register,
    
  },
  {
    path: "/login",
    name: "login",
    component: Login,
    meta: {
      requiresAuth : false,
    },
  },
  {
    path: "/forget-password",
    name: "ForgetPassword",
    component: ForgetPassword,
    meta: {
      requiresAuth : false,
    },
  },
  {
    path: "/confirm-password",
    name: "ConfirmPassword",
    component: ConfirmPassword,
    meta: {
      requiresAuth : false,
    },
  },
];



const router = new VueRouter({
  history: true,
  mode: "history",
  routes,
});

router.beforeEach((to, from, next) => {
  // redirect to login page if not logged in and trying to access a restricted page
  const {meta, name: routeName} = to;
  
  const loggedIn = localStorage.getItem('token_admin');
  if(localStorage && localStorage.getItem('admin_profile')){
    var role = JSON.parse(localStorage.getItem('admin_profile')).roles_id;
  }else{
    var role = 0;
  }
  const authRequired = meta.requiresAuth;
  if((routeName == 'login' ||  routeName == 'register' || routeName == 'ForgetPassword') && loggedIn){
    return next("/")
  }
  if (authRequired && !loggedIn) {
    return next('/login');
  }
  if(meta.requiresSuperAdmin && role != 1){
    return next("/")
  }
  return next();
})

export default router;
