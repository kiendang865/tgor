<template>
  <div class="admin-contractor-info">
    <b-container fluid="lg">
      <div class="columbarium-niches d-flex justify-content-between">
        <div class="title">
          <span class="title-name" @click="goBack">
            <ChevronLeft />
            Contractor Info
          </span>
        </div>
        <div class="wrapper-btn">
          <b-button class="btn-save" @click="onSave">Save</b-button>
        </div>
      </div>
      <div v-bind:class="{ 'run-loading': isLoadingMain }">
        <b-container fluid="lg" class="contractor-info-admin">
          <b-row>
            <b-col cols="3">
              <b-form-group>
                <label class="_label_input">Company Name <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.contractorParams.company_name.$error,
                  }"
                  v-model.trim="$v.contractorParams.company_name.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contractorParams.company_name.required && $v.contractorParams.company_name.$error">
                  Field is required
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Postal Code">
                <b-form-input
                  v-model.trim="$v.contractorParams.postal_code.$model"
                  :class="{
                    'form-group--error': $v.contractorParams.postal_code.$error,
                  }"
                  class="input-form"
                  maxlength="6"
                ></b-form-input>
                <div class="error" v-if="!$v.contractorParams.postal_code.decimal && $v.contractorParams.postal_code.$error">Please enter number</div>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group label="Address">
                <b-form-input v-model="contractorParams.address" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="Company Main Tel">
                <b-form-input
                  :class="{
                    'form-group--error': $v.contractorParams.account_number.$error,
                  }"
                  v-model.trim="$v.contractorParams.account_number.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contractorParams.company_main_tel.decimal && $v.contractorParams.company_main_tel.$error">
                  Please enter number
                </div>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Website">
                <b-form-input v-model="contractorParams.website" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="UEN No.">
                <b-form-input
                  :class="{
                    'form-group--error': $v.contractorParams.uen_no.$error,
                  }"
                  v-model.trim="$v.contractorParams.uen_no.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contractorParams.uen_no.decimal && $v.contractorParams.uen_no.$error">Please enter number</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="3">
              <b-form-group label="Bank Name">
                <b-form-input v-model="contractorParams.bank_name" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
            <b-col cols="3">
              <b-form-group label="Account Number">
                <b-form-input
                  :class="{
                    'form-group--error': $v.contractorParams.account_number.$error,
                  }"
                  v-model.trim="$v.contractorParams.account_number.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contractorParams.account_number.decimal && $v.contractorParams.account_number.$error">
                  Please enter number
                </div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="mt">
            <b-col cols="6">
              <b-form-group label="Services">
                <multiselect
                  v-model="contractorParams.service_id"
                  :show-labels="false"
                  deselect-label=""
                  :options="otherByContractor"
                  placeholder="Select one"
                  :multiple="true"
                  :taggable="true"
                  @tag="addTag"
                  track-by="id"
                  label="service_name"
                ></multiselect>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group label="Remarks">
                <b-form-input v-model="contractorParams.remarks" class="input-form"></b-form-input>
              </b-form-group>
            </b-col>
          </b-row>
          <label class="text-label mt" for="tile-table">Contact Person</label>
          <div class="contact-person-outside-table">
            <div class="content-table" v-bind:class="{ 'run-loading': isLoading }">
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
                      {{ listContactPerson.from ? `${listContactPerson.from}-${listContactPerson.to} of ${listContactPerson.total}` : "0-0 of 0" }}
                    </span>
                    <span @click="prevPanigate" class="icon">
                      <b-img class="image" src="/images/left.png" fluid alt="Responsive image"></b-img>
                    </span>
                    <span @click="nextPanigate" class="icon">
                      <b-img class="image" src="/images/right.png" fluid alt="Responsive image"></b-img>
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
        :title="`${contactPersonParams.id != '' ? 'Edit' : 'Add'} Contact Person`"
      >
        <b-container fluid="lg">
          <b-row>
            <b-col cols="12">
              <b-form-group>
                <label class="_label_input">Name <span class="_require">*</span></label>
                <b-form-input
                  :class="{
                    'form-group--error': $v.contactPersonParams.display_name.$error,
                  }"
                  v-model.trim="$v.contactPersonParams.display_name.$model"
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contactPersonParams.display_name.required && $v.contactPersonParams.display_name.$error">
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
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contactPersonParams.phone.required && $v.contactPersonParams.phone.$error">Field is required</div>
                <div class="error" v-else-if="!$v.contactPersonParams.phone.maxLength && $v.contactPersonParams.phone.$error">
                  The number phone must be
                  {{ $v.contactPersonParams.phone.$params.maxLength.max }}
                  number
                </div>
                <div class="error" v-else-if="!$v.contactPersonParams.phone.decimal && $v.contactPersonParams.phone.$error">
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
                  class="input-form"
                ></b-form-input>
                <div class="error" v-if="!$v.contactPersonParams.email.required && $v.contactPersonParams.email.$error">Field is required</div>
                <div class="error" v-else-if="!$v.contactPersonParams.email.email && $v.contactPersonParams.email.$error">Please enter email</div>
              </b-form-group>
            </b-col>
          </b-row>
          <b-row class="btn-submit">
            <b-col cols="12">
              <div class="submit" @click="onSubmit">Submit</div>
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
import Multiselect from "vue-multiselect";
import ControllTable from "../../components/customViews/controllTable.vue";
import TableCustom from "../../components/Table";

import { required, minLength, maxLength, email, decimal, between } from "vuelidate/lib/validators";
import { validationMixin } from "vuelidate";
import { mapActions, mapState } from "vuex";

export default {
  name: "ServiceNichesBooking",
  components: {
    ChevronLeft,
    Calendar,
    Multiselect,
    ControllTable,
    TableCustom,
  },
  metaInfo: {
    title: "Contractor Info",
    meta: [
      {
        vmid: "description",
        name: "description",
        content: "Contractor Info Description",
      },
    ],
  },
  data() {
    return {
      showIconTrash: true,
      admin_profile: JSON.parse(localStorage.getItem("admin_profile")),
      ids: [],
      tabIndex: 0,
      isLoading: false,
      ids: [],
      contractorParams: {
        id: "",
        company_name: "",
        bank_name: "",
        account_number: "",
        address: "",
        website: "",
        service_id: "",
        postal_code: "",
        company_main_tel: "",
        uen_no: "",
        remarks: "",
      },
      value: null,
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
          {
            key: "display_name",
            label: "Name",
            isActive: 1,
            isFilter: true,
          },
          {
            key: "phone",
            label: "Mobile",
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
      contactPersonParams: {
        id: "",
        company_id: this.$router.history.current.params.id,
        display_name: "",
        email: "",
        phone: "",
      },
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
          name: "Mobile",
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
      old_postal: "",
      isLoadingMain: false,
    };
  },
  validations: {
    contractorParams: {
      company_name: {
        required,
      },
      account_number: {
        decimal,
      },
      postal_code: {
        decimal,
      },
      company_main_tel: {
        decimal,
      },
      uen_no: {
        decimal,
      },
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
  created() {
    if (this.admin_profile.roles_id != 1) {
      this.showIconTrash = false;
    }
    let idContractor = this.$router.history.current.params.id;

    this.getContractorDetail(idContractor);

    this.getListOtherByContractRequired();

    this.handlePanigate(this.contactParams);
  },
  computed: mapState({
    listTypeBooking: (state) => state.contractor.listTypeBooking,
    otherByContractor: (state) => state.other.otherByContractor,
    listContactPerson: (state) => state.user.listContactPerson,
  }),

  methods: {
    ...mapActions({
      updateContractor: "contractor/updateContractor",
      contractorDetail: "contractor/contractorDetail",
      getListTypeBooking: "contractor/getListTypeBooking",
      getListOtherByContractRequired: "other/getListOtherByContractRequired",
      getListContactPerson: "user/getListContactPerson",
      createContactPerson: "user/createContactPerson",
      updateContactPerson: "user/updateContactPerson",
      deleteContactPerson: "user/deleteContactPerson",
      findAdress: "address/findAdress",
    }),
    linkClass(idx) {
      if (this.tabIndex === idx) {
        return;
      } else {
        return "";
      }
    },
    goBack() {
      // @click="goBack"
      this.$router.push("/admin-partners/#contractor");
    },
    onSave() {
      this.$v.contractorParams.$touch();
      if (this.$v.contractorParams.$anyError) {
        return;
      } else {
        let prms = { ...this.contractorParams };

        prms.service_id = [];

        this.contractorParams.service_id.map((item, key) => {
          prms.service_id.push(item.id);
        });

        this.updateContractor(prms)
          .then((res) => {
            this.$swal({
              icon: "success",
              title: "Notifcation",
              text: res.data.status,
            });
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
    getContractorDetail(idContractor) {
      this.contractorDetail(idContractor).then((res) => {
        this.contractorParams.service_id = res.services;
        this.contractorParams.company_name = res.company_name;
        this.contractorParams.bank_name = res.bank_name;
        this.contractorParams.account_number = res.account_number;
        this.contractorParams.website = res.website;
        this.contractorParams.address = res.address;
        this.contractorParams.id = res.id;
        this.contractorParams.postal_code = res.postal_code;
        this.contractorParams.company_main_tel = res.company_main_tel;
        this.contractorParams.uen_no = res.uen_no;
        this.contractorParams.remarks = res.remarks;
        this.old_postal = res.postal_code;
      });
    },
    addTag(newTag) {
      const tag = {
        name: newTag,
        code: newTag.substring(0, 2) + Math.floor(Math.random() * 10000000),
      };
      this.options.push(tag);
      this.contractorParams.service_id.push(tag);
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
  watch: {
    "contractorParams.postal_code": {
      deep: true,
      handler: _.debounce(function () {
        if (this.contractorParams.postal_code.toString().length == 6) {
          if (!!this.contractorParams.postal_code && this.contractorParams.postal_code !== this.old_postal) {
            this.isLoadingMain = true;
            let prms = { postal_code: this.contractorParams.postal_code };
            this.findAdress(prms)
              .then((res) => {
                this.isLoadingMain = false;
                this.contractorParams.address = res.data.data?.address;
              })
              .catch((error) => {
                this.isLoadingMain = false;
                this.contractorParams.address = "";
              });
          }
        }
      }, 200),
    },
  },
};
</script>
