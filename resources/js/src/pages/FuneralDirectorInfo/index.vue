<template>
  <div class="admin-funeral-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Funeral Director Info
          </span>
        </div>
        <div class="wrapper-btn">
          <!-- <b-button class="btn-extension">Extension</b-button> -->
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div v-bind:class="{ 'run-loading': isLoadingMain }">
        <b-container fluid="lg" class="funeral-info-admin">
          <b-row>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input"
                  >Company Name <span class="_require">*</span></label
                >
                <b-form-input
                  autofocus
                  :class="{
                    'form-group--error': $v.direactorParams.company_name.$error,
                  }"
                  v-model.trim="$v.direactorParams.company_name.$model"
                  class="input-form "
                ></b-form-input>
                <div
                  class="error"
                  v-if="
                    !$v.direactorParams.company_name.required &&
                      $v.direactorParams.company_name.$error
                  "
                >
                  Field is required
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Postal Code">
                <b-form-input
                  v-model.trim="$v.direactorParams.postal_code.$model"
                  class="input-form "
                  :class="{
                    'form-group--error': $v.direactorParams.postal_code.$error,
                  }"
                ></b-form-input>
                <div
                  class="error"
                  v-if="
                    !$v.direactorParams.postal_code.decimal &&
                      $v.direactorParams.postal_code.$error
                  "
                >
                  Please enter number
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group label="Address">
                <b-form-input
                  v-model="direactorParams.address"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="Company Main Tel">
                <b-form-input
                  v-model="direactorParams.company_main_tel"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Website">
                <b-form-input
                  v-model="direactorParams.website"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="UEN No.">
                <b-form-input
                  v-model="direactorParams.uen_no"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="Bank Name">
                <b-form-input
                  v-model="direactorParams.bank_name"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Account Number">
                <b-form-input
                  v-model="direactorParams.account_number"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="6">
              <b-form-group label="Remarks">
                <b-form-input
                  v-model="direactorParams.remarks"
                  class="input-form "
                ></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
          <label class="text-label mt" for="tile-table">Contact Person</label>
          <div class="contact-person-outside-table">
            <div
              class="content-table"
              v-bind:class="{ 'run-loading': isLoading }"
            >
              <div class="table">
                <ControllTable
                  :isShowIconTrash="showIconTrash"
                  :optionSearch="optionsFilter"
                  :isShowIconExport="false"
                  :onChangeSearch="onChangeSearch"
                  :onCreate="onCreateContactPerson"
                  @deleteItems="deleteItem"
                />
                <TableCustom
                  ref="adminContactTable"
                  :tableFields="columnActive.fields"
                  :tableItems="listContactPerson.data"
                  @Items="getItems"
                  @rowClicked="showModal"
                >
                </TableCustom>
                <b-row class="pagination">
                  <b-col md="12" class="end">
                    <span>
                      {{
                        listContactPerson.from
                          ? `${listContactPerson.from}-${listContactPerson.to} of ${listContactPerson.total}`
                          : "0-0 of 0"
                      }}
                    </span>
                    <span @click="prevPanigate" class="icon">
                      <b-img
                        class="image"
                        src="/images/left.png"
                        fluid
                        alt="Responsive image"
                      ></b-img>
                    </span>
                    <span @click="nextPanigate" class="icon">
                      <b-img
                        class="image"
                        src="/images/right.png"
                        fluid
                        alt="Responsive image"
                      ></b-img>
                    </span>
                  </b-col>
                </b-row>
              </div>
            </div>
          </div>
        </b-container>
      </div>
      <b-modal
        centered
        ref="other_modal"
        hide-footer
        id="extension"
        size="sm"
        :title="
          `${contactPersonParams.id != '' ? 'Edit' : 'Add'} Contact Person`
        "
      >
        <b-container fluid="lg">
          <b-row>
            <b-col cols="12">
              <b-form-group>
                <label class="_label_input">Name <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error':
                      $v.contactPersonParams.display_name.$error,
                  }"
                  v-model.trim="$v.contactPersonParams.display_name.$model"
                  class="input-form "
                ></b-form-input>
                <div
                  class="error"
                  v-if="
                    !$v.contactPersonParams.display_name.required &&
                      $v.contactPersonParams.display_name.$error
                  "
                >
                  Field is required
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="12 mt">
              <b-form-group>
                <label class="_label_input">Mobile <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.contactPersonParams.phone.$error,
                  }"
                  v-model.trim="$v.contactPersonParams.phone.$model"
                  class="input-form "
                ></b-form-input>
                <div
                  class="error"
                  v-if="
                    !$v.contactPersonParams.phone.required &&
                      $v.contactPersonParams.phone.$error
                  "
                >
                  Field is required
                </div>
                <div
                  class="error"
                  v-else-if="
                    !$v.contactPersonParams.phone.maxLength &&
                      $v.contactPersonParams.phone.$error
                  "
                >
                  The number phone must be
                  {{ $v.contactPersonParams.phone.$params.maxLength.max }}
                  number
                </div>
                <div
                  class="error"
                  v-else-if="
                    !$v.contactPersonParams.phone.decimal &&
                      $v.contactPersonParams.phone.$error
                  "
                >
                  Please enter the phone number
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="12 mt">
              <b-form-group>
                <label class="_label_input">Email <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.contactPersonParams.email.$error,
                  }"
                  v-model.trim="$v.contactPersonParams.email.$model"
                  class="input-form "
                ></b-form-input>
                <div
                  class="error"
                  v-if="
                    !$v.contactPersonParams.email.required &&
                      $v.contactPersonParams.email.$error
                  "
                >
                  Field is required
                </div>
                <div
                  class="error"
                  v-else-if="
                    !$v.contactPersonParams.email.email &&
                      $v.contactPersonParams.email.$error
                  "
                >
                  Please enter email
                </div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="btn-submit">
            <b-col cols="12">
              <div class="submit" @click="onSubmit">
                Submit
              </div>
            </b-col>
          </b-row>
        </b-container>
      </b-modal>
    </b-container>
  </div>
</template>
<script>
import ChevronLeft from "@/components/Icons/ChevronLeft";
import Calendar from "@/components/Icons/Calendar";
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";

import {
  required,
  minLength,
  maxLength,
  email,
  decimal,
  between,
} from "vuelidate/lib/validators";
import { validationMixin } from "vuelidate";
import { mapActions, mapState } from "vuex";
import _ from 'lodash';

export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    Calendar,
    ControllTable,
    TableCustom,
  },
  data() {
    return {
      showIconTrash: true,
      admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
      tabIndex: 0,
      isLoading: false,
      isLoadingMain: false,
      ids: [],
      direactorParams: {
        company_name: "",
        bank_name: "",
        account_number: "",
        address: "",
        website: "",
        postal_code: "",
        company_main_tel: "",
        uen_no: "",
        remarks: ""
      },
      columnActive: {
        fields: [
          {
            key: "actions",
            label: "",
            thClass: "checkbox-column text-center",
            tdClass: "checkbox-column text-center",
            thStyle: "width: 50px",
            isActive: 1,
          },
          // {
          //   key: "ordinal_number",
          //   label: "#",
          //   isActive: 1,
          //   keySearch: "id",
          //   type: "text",
          //   isFilter: true,
          // },
          {
            key: "display_name",
            label: "Name",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "phone",
            label: "Phone Number",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "email",
            label: `Email`,
            isActive: 1,
            isFilter: true,
          },
        ],
        show: [],
        hide: [],
      },
      items: [
        {
          id: 2,
          index: "02",
          name: "James Smith",
          phone: "985684623",
          email: "james@email.com",
        },
      ],
      optionsFilter: [
        {
          name: "All",
          value: "all",
        },
        {
          name: "Name",
          value: "name",
        },
        {
          name: "Phone Number",
          value: "phone",
        },
        {
          name: "Email",
          value: "email",
        },
      ],
      contactParams: {
        id: this.$router.history.current.params.id,
        page: 1,
        filter: {},
      },
      contactPersonParams: {
        id: "",
        company_id: this.$router.history.current.params.id,
        display_name: "",
        email: "",
        phone: "",
      },
      old_postal: "",
    };
  },
  validations: {
    direactorParams: {
      company_name: {
        required,
      },
      postal_code: {
        decimal
      }
    },
    contactPersonParams: {
      display_name: {
        required,
      },
      email: {
        required,
        email,
      },
      phone: {
        required,
        decimal,
        maxLength: maxLength(12),
      },
    },
  },
  metaInfo: {
    title: "Funeral Director Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Funeral Director Info Description",
      },
    ],
  },
  created() {
    if (this.admin_profile.roles_id != 1) {
      this.showIconTrash = false;
    }
    let idDireactor = this.$router.history.current.params.id;

    this.getDirectorDetail(idDireactor);

    this.handlePanigate(this.contactParams);
  },
  computed: mapState({
    listContactPerson: (state) => state.user.listContactPerson,
  }),
  watch: {
    "direactorParams.postal_code": {
      deep: true,
        handler: _.debounce(function() {
        if(this.direactorParams.postal_code.toString().length == 6){
          if(!!this.direactorParams.postal_code && this.direactorParams.postal_code !== this.old_postal)
          {
            this.isLoadingMain = true;
            let prms = {'postal_code': this.direactorParams.postal_code};
            this.findAdress(prms).then(res => { 
              this.isLoadingMain = false;
              this.direactorParams.address = res.data.data?.address
            }).catch(error => {
              this.isLoadingMain = false;
              this.direactorParams.address = '';
            })
          }
        }
        
      }, 200)
    }
  },
  methods: {
    ...mapActions({
      updateDirector: "director/updateDirector",
      directorDetail: "director/directorDetail",
      getListContactPerson: "user/getListContactPerson",
      createContactPerson: "user/createContactPerson",
      updateContactPerson: "user/updateContactPerson",
      deleteContactPerson: "user/deleteContactPerson",
      findAdress: "address/findAdress"
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return "";
      }
    },
    handlePanigate(params) {
      this.isLoading = true;
      this.getListContactPerson(params)
        .then((response) => {
          this.isLoading = false;
        })
        .catch((error) => {
          this.isLoading = false;
          this.$swal({
            icon: "error",
            title: "Oops...",
            text: error.response.data.errors,
          });
        });
    },
    goBack() {
      // @click="goBack"
      this.$router.push("/admin-partners");
    },
    onSave() {
      this.$v.direactorParams.$touch();
      if (this.$v.direactorParams.$anyError) {
        return;
      } else {
        let prms = { ...this.direactorParams };
        this.updateDirector(prms)
          .then((res) => {
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });
          })
          .catch((error) => {
            this.$swal({
              icon: "error",
              title: "Oops...",
              text: error.response.data.errors,
            });
          });
      }
    },
    getDirectorDetail(idDireactor) {
      this.directorDetail(idDireactor).then((res) => {
        this.direactorParams = res;
        if(res.postal_code)
          this.old_postal = res.postal_code;
      });
    },
    onCreateContactPerson() {
      this.contactPersonParams.id = "";
      this.contactPersonParams.display_name = "";
      this.contactPersonParams.phone = "";
      this.contactPersonParams.email = "";
      this.$v.contactPersonParams.$reset();
      this.$refs.other_modal.show();
    },
    showModal(item) {
      this.contactPersonParams.id = item.id;
      this.contactPersonParams.display_name = item.display_name;
      this.contactPersonParams.phone = item.phone;
      this.contactPersonParams.email = item.email;
      this.$refs.other_modal.show();
    },
    onSubmit() {
      this.$v.contactPersonParams.$touch();
      if (this.$v.contactPersonParams.$anyError) {
        return;
      } else {
        let action = "createContactPerson";

        if (this.contactPersonParams.id != "") {
          action = "updateContactPerson";
        }
        let prms = { ...this.contactPersonParams };
        this[action](prms)
          .then((res) => {
            this.$refs.other_modal.hide();
            this.isLoading = false;
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });
            this.handlePanigate(this.contactParams);
            this.$refs.adminContactTable.reloadData();
          })
          .catch((error) => {
            this.isLoading = false;
            this.$swal({
              icon: "error",
              title: "Oops...",
              text: error.response.data.errors,
            });
          });
      }
    },
    onChangeSearch(valueSearch, typeSearch) {
      let { current_page, last_page } = this.listContactPerson;
      clearTimeout(this.actionSearch);
      this.contactParams.filter = {};
      if (!valueSearch) {
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.contactParams);
        }, 300);
      } else {
        this.contactParams.filter[typeSearch.value] = valueSearch;
        this.actionSearch = setTimeout(() => {
          this.handlePanigate(this.contactParams);
        }, 300);
      }
    },
    prevPanigate() {
      let { current_page } = this.listContactPerson;
      if (current_page != 1) {
        this.contactParams.page = current_page - 1;
        this.handlePanigate(this.contactParams);
      }
    },
    nextPanigate() {
      let { current_page, last_page } = this.listContactPerson;
      if (current_page != last_page) {
        this.contactParams.page = current_page + 1;
        this.handlePanigate(this.contactParams);
      }
    },
    deleteItem() {
      if (this.ids.length == 0) {
        this.$swal({
          icon: "error",
          title: "Oops...",
          text: "Can not find contact person.",
        });
      } else {
        this.$swal({
          title: "Permanently delete?",
          text: "This action is irreversible.",
          icon: "warning",
          customClass: {
            container: "swal-del-item",
          },
          showCancelButton: true,
          confirmButtonText: "Yes",
          cancelButtonText: "No",
        }).then((result) => {
          if (result.value) {
            this.isLoading = true;
            let prms = { ids: this.ids };
            this.deleteContactPerson(prms)
              .then((res) => {
                this.$swal({
                  icon: "success",
                  title: "Success!",
                  text: res.data.status,
                });
                this.handlePanigate(this.contactParams);
                this.$refs.adminContactTable.reloadData();
                this.isLoading = false;
              })
              .catch((error) => {
                this.isLoading = false;
                this.$swal({
                  icon: "error",
                  title: "Oops...",
                  text: error.response.data.errors,
                });
              });
          }
        });
      }
    },
    getItems(item) {
      this.ids = item;
    },
  },
};
</script>
