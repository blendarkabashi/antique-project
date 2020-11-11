<template>
    <div class="toolbar d-flex justify-content-between align-center mt-4">
        <div class="font-weight-bold main-title"><a href="javascript:void(0)" @click="redirectHome">Antique App</a></div>
        <a href="javascript:void(0)" class="text-white" @click="logout">Logout</a>
    </div>
</template>

<script>
    export default {
        methods: {
			logout() {
				this.$store.dispatch('logout');
			},
			redirectHome(){
				this.$router.push({name: 'Home'})
			}
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
	.main-title{
		a{
			color: inherit;
			text-decoration: none;
		}
	}
</style>