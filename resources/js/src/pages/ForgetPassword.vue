<template>
<div class="vertical-center background-brilliance" v-bind:class="{'run-loading':isLoading}">
    <!-- 
                      ^--- Added class  -->
    <div  class="container">
        <div class="content-login">
            <div class="logo">
                <b-img center src="images/logo_tgor.png" fluid alt="Responsive image"></b-img>
            </div>
            <div class="noted">Forgot your password? <br> Please type in your email here:</div>
            <b-form v-on:keyup.enter="handleLogin">
                <b-form-group id="input-group-1" label="User email" label-for="input-1" label-size="lg">
                    <b-form-input name="email" id="email"  v-model="form.email" placeholder="Your Email" size="lg" class="input-login">
                    </b-form-input>
                </b-form-group>
                <div class="clearfix"></div>
                <div class="forget d-flex justify-content-between">
                    <b-button class="btn-cancel" @click='goBack' type="button" >Cancel</b-button>
                    <b-button @click="handleLogin" class="btn-forget"  type="button" variant="primary">Submit</b-button>
                </div>

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
            forgetPassword: 'user/forgetPassword'
        }),
        handleLogin() {
            this.isLoading = true;
            if(this.form.email == ''){
                 this.isLoading = false;
                this.$swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please enter email.',
                });
                return;
            }
            this.forgetPassword(this.form).then(result => {
                this.isLoading = false;              
                 this.$swal({
                    icon: 'success',
                    title: 'Success',
                    text: result.data.data,
                });
            }).catch((error) => {
                this.isLoading = false;
                this.$swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.response.data.errors,
                });
            })

        },
        goBack(){
            this.$router.push('/login')
        }
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
