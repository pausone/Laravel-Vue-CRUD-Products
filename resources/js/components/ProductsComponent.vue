<template>
    <div id="products-content" class="mt-2">
        <button type="button" class="btn btn-default" aria-label="Left Align">
  <span class="glyphicon glyphicon-align-left" aria-hidden="true"></span>
</button>
        <h1>Products</h1>
        <hr>
        <LoginForm v-if="!loggedIn && !this.$store.getters.register" v-bind:user="user"/>
        <RegisterForm v-if="this.$store.getters.register" v-bind:user="user"/>

        <!-- ADD/EDIT PRODUCT FORM -->
        <div v-if="loggedIn">
            <h3>{{action}}</h3>
            <form id="product-form" @submit.prevent="addProduct">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name: Max length 20" maxlength="20" v-model="product.name">
                    <div v-if="showErrors">
                        <p v-if="!$v.product.name.required" class="alert alert-danger">The name field is required</p>
                    </div>
                </div>
                <div class="form-group">
                    <textarea type="text" class="form-control" placeholder="Description" v-model="product.description"></textarea>
                     <div v-if="showErrors">
                        <p v-if="!$v.product.description.required" class="alert alert-danger">The description field is required</p>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Price" v-model="product.price">
                     <div v-if="showErrors">
                        <p v-if="!$v.product.price.required" class="alert alert-danger">The price field is required</p>
                        <p v-if="!$v.product.price.decimal" class="alert alert-danger">The price must be a number</p>
                        <p v-if="!$v.product.price.between" class="alert alert-danger">The price must be a positive number</p>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Image URL: 150x150px" alt="product image" v-model="product.image">
                     <div v-if="showErrors">
                        <p v-if="!$v.product.image.required" class="alert alert-danger">The image field is required</p>
                        <p v-if="!$v.product.image.url" class="alert alert-danger">URL required</p>
                    </div>
                </div>
                <button type="submit" class="btn btn-light btn-block">Save</button>
                <center><button v-if="edit == true" @click="resetData()" class="btn btn-danger">Cancel</button></center>
            </form>
            <hr>
        </div>        

        <div id="products" class="mt-2">             
            <div class="row"> 
                <div v-for="product in products" v-bind:key="product.id" class="col-md-4 col-sm-6">
                    <div class="panel">
                        <img class="product-image" :src="product.image" v-b-popover.click="product.description" :title="product.name">
                        <div class="product-name-wrapper mt-2">
                            <h3>{{product.name}}</h3>
                        </div>
                        <p class="price">Price: {{product.price}}$</p>
                        <div v-if="loggedIn">
                            <button @click="editProduct(product)" class="btn btn-sm btn-outline-primary">Edit</button>

                            <button @click="deleteProduct(product.id)" class="btn  btn-sm btn-outline-danger">Delete</button>
                        </div>
                        <hr>
                     </div>                        
                  </div>
            </div>
      </div>
    </div>
</template>
<script>
import LoginForm from './LoginForm.vue'
import RegisterForm from './RegisterForm.vue'
import { required, minLength, decimal, url, between } from 'vuelidate/lib/validators'

export default {
      components: {
        LoginForm,
        RegisterForm
      },
    data(){
        return{
            products: [],
            product: {
                id: '',
                name: '',
                description: '',
                price: '',
                image: ''
            },
            user: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            edit: false,
            register: false,
            action: '',
            actionDefault: 'Add product',
            showErrors: false
        }
    },
    validations: {
        product: {
            name:{
                required: required
            },
            description:{
                required: required
            },
            price:{
                required: required,
                decimal: decimal,
                between: between(0.1, 100000000)
            },
            image:{
                required: required,
                url: url
            }
        },
        user: {
            email:{
                required: required
            },
            password:{
                required: required
            }
        }
    },
    methods: {
        addProduct(){
            this.showErrors = true

            if(!this.$v.product.$invalid){
               if (this.edit === false){
                    //Add token to product
                    this.product.api_token = localStorage.api_token;

                    //Add new product
                    axios.post('api/products', this.product)
                    .then(data => {
                        this.product.name = '';
                        this.product.description = '';
                        this.product.price = '';
                        this.product.image = '';
                        alert('Product Added');
                        this.getProducts();
                    })
                    .catch(err => console.log(err));                
                }
                else{//Update product                    
                    //Add token to product
                    this.product.api_token = localStorage.api_token;

                    axios.put('api/products/' + this.product.id, this.product)
                    .then(data => {
                        this.resetData();

                        alert('Product Updated');
                        this.getProducts();
                    })
                    .catch(err => console.log(err));  
                }
            }
        },
        editProduct(product){
            this.showErrors = false;
            this.edit = true;
            this.action = 'Edit product';
            this.product.id = product.id;
            this.product.name = product.name;
            this.product.description = product.description;
            this.product.price = product.price;
            this.product.image = product.image;
        },
        deleteProduct(id){
            if (confirm('Are you sure?')) {
                axios.delete('api/products/' + id,  { data: { api_token: localStorage.api_token } })
                .then(data => {
                    alert('Product Removed');
                    this.getProducts();
                })
                .catch(err => console.log(err));
            }
        },
        getProducts(){
            axios.get('api/products')
            .then(res => this.products = res.data)
            .catch(err => console.log(err));            
        },
        resetData(){
            this.showErrors = false;
            this.action = this.actionDefault;
            this.edit = false;
            this.product.id = '';
            this.product.name = '';
            this.product.description = '';
            this.product.price = '';
            this.product.image = '';           
        },
    },
    computed:{
        //Behövs?
        groupedProducts() {
            return _.chunk(this.products, 3)
        },
        loggedIn(){
            return this.$store.getters.loggedIn
        }
    },
    created(){
        this.action = this.actionDefault;
        this.getProducts();
    },
    mounted(){
        if(localStorage.api_token){
            this.$store.commit('login')                       
        }
    }
  }
</script>
<style scoped>
    #products-content{
        max-width: 800px;
        text-align: left;
        display: inline-block;
    }

    #products{
        text-align: left;
    }

    .panel{
        height: 350px;
    }
    .product-name-wrapper{
        height: 3.8rem;
    }

    .price{
        margin-bottom: 0px;
    }
</style>

