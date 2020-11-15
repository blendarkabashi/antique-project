<template>
	<div class="toolbar d-flex justify-content-between align-center mt-4">
		<div class="font-weight-bold main-title"><a href="javascript:void(0)"
				@click="$router.push({name: 'Home'})">Antique App</a></div>
		<div>
			<a href="javascript:void(0)" class="text-white mr-4" @click="$router.push({name: 'Profile'})">Profile</a>
			<a href="javascript:void(0)" class="text-white" @click="logout">Logout</a>
		</div>
	</div>
</template>

<script>
	export default {
		methods: {
			logout() {
				this.axios.post('/logout').then(response => {
					this.$store.dispatch('logout');
					this.$router.push({
						name: 'Login'
					})
				}).catch(error => {
					console.log(error)
				})


			},
		},
	}
</script>

<style lang="scss" scoped>
	.toolbar {
		padding: 10px 20px;
		background: #4caf50;
		border-radius: 5px;
		color: white;
	}

	.main-title {
		a {
			color: inherit;
			text-decoration: none;
		}
	}
</style>