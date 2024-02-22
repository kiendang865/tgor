<template>
<div class="vertical-center background-brilliance" v-bind:class="{'run-loading':isLoading}">
    <!-- 
                      ^--- Added class  -->
    <div  class="container">
        <div class="content-login">
            <div class="logo">
                <b-img center src="images/logo_tgor.png" fluid alt="Responsive image"></b-img>
            </div>

            <b-form v-on:keyup.enter="handleLogin">
                <b-form-group id="input-group-1" label="User ID" label-for="input-1" label-size="lg">
                    <b-form-input name="email" id="email"  v-model="form.email" placeholder="Your Email" size="lg" class="input-login">
                    </b-form-input>
                </b-form-group>

                <b-form-group id="input-group-1" label="Password" label-for="input-1" label-size="lg">
                    <div style="position : relative;">
                        <b-form-input name="password" id="password" :type="!showPassword ? 'password': 'text'"  v-model="form.password" placeholder="Your Password" size="lg" class="input-login">
                        </b-form-input>
                        <div class="show-password">
                            <b-button class="btn-see" style="padding: 0" @click="showPassword=!showPassword">
                                <b-img v-if="!showPassword" src="/images/eye.png" width="19" height="11" fluid alt="Responsive image"></b-img>
                                <b-img v-if="showPassword" src="/images/eyeon.png" width="19" height="11" fluid alt="Responsive image"></b-img>
                            </b-button>
                        </div>
                    </div>
                </b-form-group>

                <!-- <b-button class="btn-forgot float-right">Forget Password</b-button> -->
                <div class="btn-forgot">
                    <router-link to="/forget-password">Forgot Password</router-link>
                </div>
                <div class="clearfix"></div>
                <b-button @click="handleLogin" class="btn-submit" block type="button">Log In</b-button>

            </b-form>

        </div>
    </div>
</div>
</template>

<script>
import {
    mapActions
} from 'vuex';

export default {
    data() {
        return {
            form: {
                email: '',
                password: ''
            },
            isLoading: false,
            showPassword: false,
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
    
    methods: {
        ...mapActions({
            login: 'auth/login'
        }),
        handleLogin() {
            this.isLoading = true;

            this.login(this.form).then(result => {
                this.form.isLoading = false;
                this.$router.replace('/new-booking');
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
