<template>
    <form id="register-form" @submit.prevent="register_user">
         <h2>Register</h2>
        <div class="form-group">
            <input class="form-control" v-model="user.name" type="text" placeholder="Name"/>
             <div v-if="showErrors">
                <p v-if="!$v.user.name.required" class="alert alert-danger">The name field is required</p>
            </div>
          </div>
        <div class="form-group">
            <input class="form-control" v-model="user.email" type="text" placeholder="Email"/>
             <div v-if="showErrors">
                <p v-if="!$v.user.email.required" class="alert alert-danger">The email field is required</p>
                <p v-else-if="!$v.user.email.email" class="alert alert-danger">Enter a valid email address</p>
            </div>
        </div>
        <div class="form-group">
            <input class="form-control" v-model="user.password" type="password" placeholder="Password"/>
             <div v-if="showErrors">
                <p v-if="!$v.user.password.required" class="alert alert-danger">The password field is required</p>
                <p v-else-if="!$v.user.password.minLength" class="alert alert-danger">The password must be atleast 8 characters</p>
            </div>
        </div>
        <div class="form-group">
            <input class="form-control" v-model="user.password_confirmation" type="password" placeholder="Confirm password"/>
             <div v-if="showErrors">
                <p v-if="!$v.user.password_confirmation.required" class="alert alert-danger">Confirmed password required</p>
                <p v-else-if="!$v.user.password_confirmation.sameAsPassword" class="alert alert-danger">Password is not the same</p>
            </div>
        </div>
        <button class="btn btn-light btn-block" type="submit">Register</button>
        <hr>
    </form>  
</template>
<script>
import { required, email, minLength, sameAs} from 'vuelidate/lib/validators'

 export default {
    props: ['user'],
    data(){
        return{
            showErrors: false
        }
    },
    validations: {
        user: {
            name:{
                required: required,
            },
            email:{
                required: required,
                email: email
            },
            password:{
                required: required,
                minLength: minLength(8)
            },
            password_confirmation:{
                required: required,
                sameAsPassword: sameAs('password')
            }
        }
    },
    methods: {
        register_user(){
            this.showErrors = true

            if(!this.$v.user.$invalid){
                axios.post('api/register', this.user)
                .then(res => {
                    this.$store.commit('registerComplete');
                    alert('You are now registered! Log in!');
                })
                .catch(err => console.log(err)); 
            }
        }
    }
  }   
</script>