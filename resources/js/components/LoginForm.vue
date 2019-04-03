<template>
    <form id="login-form" @submit.prevent="login">
        <h2>Log in</h2>
        <div class="form-group">
            <input  class="form-control" v-model="user.email" name="login_email" type="text" placeholder="Email"/>
             <div v-if="showErrors">
                <p v-if="!$v.user.email.required" class="alert alert-danger">The email field is required</p>
                <p v-else-if="!$v.user.email.email" class="alert alert-danger">Enter a valid email address</p>
            </div>
        </div>
        <div class="form-group">
            <input  class="form-control" v-model="user.password" name="login_pwd" type="password" placeholder="Password"/>
             <div v-if="showErrors">
                <p v-if="!$v.user.password.required" class="alert alert-danger">The password field is required</p>
                <p v-if="!$v.user.password.minLength" class="alert alert-danger">The password must be atleast 8 characters</p>
            </div>
        </div>
        <button class="btn btn-light btn-block" type="submit">Login</button>
        <hr>
    </form>    
</template>
<script>
import { required, email, minLength} from 'vuelidate/lib/validators'

export default {
    name: "LoginForm",
    props: ['user'],
    data(){
        return{
            showErrors: false
        }
    },
    validations: {
        user: {
            email:{
                required: required,
                email: email
            },
            password:{
                required: required,
                minLength: minLength(8)
            }
        }
    },
    methods: {
        login(){
            this.showErrors = true

            if(!this.$v.user.$invalid){
                axios.post('api/login', this.user)
                .then(res => {
                    localStorage.api_token = res.data.data.api_token;
                    this.$store.commit('login');
                })
                .catch(err => console.log(err)); 
            }
        },
    }
}      
</script>
<style scoped>
    #register-btn{
        float: right;
    }
</style>