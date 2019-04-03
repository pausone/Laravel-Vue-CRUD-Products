<template>
	<nav class="navbar navbar-expand-sm navbar-dark bg-info mb2-2">
		<div class="container">
			<a href="/" class="navbar-brand">Products</a>

            <form v-if="this.$store.getters.loggedIn" class="logout" @submit.prevent="logout">
                 <button class="btn btn-light" type="submit">Logout</button>
            </form>
            <a v-else-if="!this.$store.getters.loggedIn && !this.$store.getters.register" id="register-btn"class="btn btn-light" @click="register">Register</a>
            <a v-else-if="!this.$store.getters.loggedIn && this.$store.getters.register" id="register-btn"class="btn btn-light" @click="loginForm">Login</a>
		</div>
	</nav>
</template>
<script>
export default {
    methods: {	
        logout(){
            axios.post('api/logout', { data: { api_token: localStorage.api_token} })
            .then(data => {
                localStorage.removeItem('api_token');
                this.$store.commit('logout');
            })
            .catch(err => {
                localStorage.removeItem('api_token');
                console.log(err);
            }); 
        },
        register(){
            this.$store.commit('register')
        },
        loginForm(){
            this.$store.commit('registerComplete')
        }
    }
}
</script>