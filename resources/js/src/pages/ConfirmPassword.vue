<template>
<div class="vertical-center background-brilliance" v-bind:class="{'run-loading':isLoading}">
    <!-- 
                      ^--- Added class  -->
    <div  class="container">
        <div class="content-login">
            <div class="logo">
                <b-img center src="/images/logo_tgor.png" fluid alt="Responsive image"></b-img>
            </div>
             <div class="noted">Please set your new password</div>
            <b-form v-on:keyup.enter="handleLogin">
                <b-form-group id="input-group-1" label="New Password" label-for="input-1" label-size="lg">
                    <div style="position : relative;">
                        <b-form-input :type="!showPassword ? 'password': 'text'" :class="{'form-group--error': $v.form.password.$error }"  v-model="$v.form.password.$model" placeholder="Your Password" size="lg" class="input-login"></b-form-input>
                        <div class="error-date" v-if="!$v.form.password.required && $v.form.password.$error">Password is required</div>
                        <div class="error-date" v-if="!$v.form.password.minLength && $v.form.password.$error">Password must have at least {{ $v.form.password.$params.minLength.min }} letters.</div>
                        <div class="show-password">
                            <b-button class="btn-see" style="padding: 0" @click="showPassword=!showPassword">
                                <b-img v-if="!showPassword" src="/images/eye.png" width="19" height="11" fluid alt="Responsive image"></b-img>
                                <b-img v-if="showPassword" src="/images/eyeon.png" width="19" height="11" fluid alt="Responsive image"></b-img>
                            </b-button>
                        </div>
                    </div>
                </b-form-group>
                <b-form-group id="input-group-1" label="Confirm New Password" label-for="input-1" label-size="lg">
                    <div style="position : relative;">
                        <b-form-input :type="!showConfirmPassword ? 'password': 'text'" :class="{'form-group--error': $v.form.confirm_password.$error }"  v-model="$v.form.confirm_password.$model" placeholder="Your Password" size="lg" class="input-login"></b-form-input>
                        <div class="error-date" v-if="!$v.form.confirm_password.required && $v.form.confirm_password.$error">Comfirm password is required</div>
                        <div class="error-date" v-else-if="!$v.form.confirm_password.sameAsPassword && $v.form.confirm_password.$error">Password must be identical.</div>
                        <div class="show-password">
                            <b-button class="btn-see" style="padding: 0" @click="showConfirmPassword=!showConfirmPassword">
                                <b-img v-if="!showConfirmPassword" src="/images/eye.png" width="19" height="11" fluid alt="Responsive image"></b-img>
                                <b-img v-if="showConfirmPassword" src="/images/eyeon.png" width="19" height="11" fluid alt="Responsive image"></b-img>
                            </b-button>
                        </div>
                    </div>
                </b-form-group>
                <div class="clearfix"></div>
                <b-button @click="handleLogin" class="btn-submit" block type="button" variant="primary">Submit</b-button>
            </b-form>

        </div>
    </div>
</div>
</template>

<script>
import {
    mapActions
} from 'vuex';
  import {
    required,
    minLength,
    between,
    sameAs
} from 'vuelidate/lib/validators'
export default {
    data() {
        return {
            form: {
                code:'',
                password: '',
                confirm_password:''
            },
            isLoading: false,
            showPassword: false,
            showConfirmPassword: false,
        }
    },
      validations: {
        form:{
          password:{
              required,
              minLength: minLength(6)
          },
          confirm_password:{
              required,
              sameAsPassword: sameAs('password')
          },
        } 
  },
    metaInfo: {
        title: 'Login',
        meta: [{
        vmid: 'description',
        name: 'description',
        content: 'Login Description'
        }]
    },       
    computed: {

    },
    created(){
        this.form.code = this.$router.history.current.query.token
    },
    methods: {
        ...mapActions({
            confirmPassword: 'user/confirmPassword'
        }),
        handleLogin() {
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            this.isLoading = true;
            this.confirmPassword(this.form).then(result => {
                this.form.isLoading = false;
                this.$swal({
                    icon: 'success',
                    title: 'Success',
                    text: result.data.data,
                });
                this.$router.push('/login');
            }).catch((error) => {
                this.isLoading = false;
                this.$swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.response.data.errors,
                });
            })

        },

    }
};
</script>

<style scoped>
.vertical-center {
    min-height: 100%;
    /* Fallback for browsers do NOT support vh unit */
    min-height: 100vh;
    /* These two lines are counted as one :-)       */

    display: flex;
    align-items: center;
}
</style>
