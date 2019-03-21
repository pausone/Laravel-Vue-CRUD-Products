<template>
    <div id="products-content" class="mt-2">
        <button type="button" class="btn btn-default" aria-label="Left Align">
  <span class="glyphicon glyphicon-align-left" aria-hidden="true"></span>
</button>
        <h1>Products</h1>
        <hr>
        <div v-if="!loggedIn">
            <form class="login" @submit.prevent="login">
                 <h2>Log in</h2>
                 <div class="form-group">
                 <input  class="form-control" required v-model="user.email" type="text" placeholder="Email"/>
                  </div>
                  <div class="form-group">
                 <input  class="form-control" required v-model="user.password" type="password" placeholder="Password"/>
                  </div>
                 <button class="btn btn-light btn-block" type="submit">Login</button>
            </form>
            <hr>
        </div>

        
        <div v-if="loggedIn">
            <h3>{{action}}</h3>
            <form @submit.prevent="addProduct">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name: Max length 20 characters" maxlength="20" v-model="product.name">
                </div>
                <div class="form-group">
                    <textarea type="text" class="form-control" placeholder="Description" v-model="product.description"></textarea>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Price" v-model="product.price">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Image URL: 150x150px" alt="product image" v-model="product.image">
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
export default {
      components: {
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
            edit: false,
            action: '',
            actionDefault: 'Add product',
            user: {
                email: '',
                password: ''
            },
        }
    },
    methods: {
        login(){
                axios.post('api/login', this.user)
                .then(res => {
                    localStorage.api_token = res.data.data.api_token;
                    this.$store.commit('login');
                })
                .catch(err => console.log(err)); 
        },
        addProduct(){
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
            else{
                //Update product
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
        },
        editProduct(product){
            this.edit = true;
            this.action = 'Edit product';
            this.product.id = product.id;
            //this.product.product_id = product.id;
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

