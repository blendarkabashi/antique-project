<template>
	<div class="pos-rel">
		<div class="container">
			<header-component></header-component>
			<div class="table-wrapper mt-4">
				<h4 class="text-left heading-text mb-4">Browse through our antique items... </h4>
				<template v-if="isAdmin">
				<div class="tools-wrapper mb-4 text-left d-flex justify-content-between" style="height:35px">
					<div class="search-box d-flex w-100">
						<input type="text" class="input-custom mr-3 h-100 w-80" v-model="searchTerm" placeholder="Search...">
						<a href="javascript:void(0)" class="btn-custom search h-100 w-20" @click="searchByTerm">Search</a>
					</div>
					<div class="add-new">
						<a href="javascript:void(0)" class="btn-custom search h-100" @click="openAddDialog">Add New
							Item</a>
					</div>
				</div>
				</template>
                <b-spinner v-if="!loadingDone" label="Loading..."></b-spinner>
				<div class="table" v-else>
					<div class="row header">
						<div class="cell">
							Name
						</div>
						<div class="cell">
							Description
						</div>
						<div class="cell">
							<a class="remove-link-defaults" href="javascript:void(0)" @click="sortByPrice">
								Price
							</a>
						</div>
						<div class="cell">
							<a class="remove-link-defaults" href="javascript:void(0)" @click="sortByPrice">
								Actions
							</a>
						</div>
					</div>
					<template  v-if="paginatedList.length >0">							
					<div class="row" v-for="(item,index) in paginatedList" :key="index">
						<div class="cell" data-title="Product">
							{{ item.name }}
						</div>
						<div class="cell" data-title="Description">
							{{item.description}}
						</div>
						<div class="cell" data-title="Price">
							${{ item.price }}
						</div>
						<div class="cell" data-title="Actions">
							<template v-if="isAdmin">									
								<a href="javascript:void(0)" class="remove-link-defaults mx-1" style="font-size:32px" @click="openEditDialog(item.id)">
									<b-icon icon="pencil-square" class="p-2 rounded-circle bg-dark" variant="white"></b-icon>
								</a>
								<a href="javascript:void(0)" class="remove-link-defaults mx-1" style="font-size:32px" @click="openDeleteDialog(item.id)">
									<b-icon icon="trash" class="p-2 rounded-circle bg-danger" variant="white"></b-icon>
								</a>
							</template>
							<a href="javascript:void(0)" class="btn-custom open-btn d-block" @click="openItem(item.id)">Bid Now</a>
						</div>
					</div>
					</template>
				</div>
					<template v-if="paginatedList.length <1">
						<div class="w-100 text-center">
							<p>No items to show</p>
						</div>
					</template>
				<div class="pagination pb-5 pt-3" v-if="items.length>10">
					<ul>
						<li v-for="(page,key) in pagesArray" :key="key"><a href="javascript:void(0)"
								:class="{ 'active': page == currentPage }" @click="paginate(page,items)">{{page}}</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="add-item overlay" :class="{ 'active': toggleCreateDialog }">
			<div class="form-box">
				<h5 class="mb-4"> Add new antique item </h5>
				<form class="form">
					<p class="mb-0">Item name: </p>
					<input type="text" class="input-custom mb-3" v-model="item.name">
					<p class="mb-0">Item description: </p>
					<textarea type="text" class="input-custom mb-3" v-model="item.description"></textarea>
					<p class="mb-0">Price: </p>
					<input type="number" class="input-custom mb-3" v-model="item.price">
					<p class="mb-0">Auction Deadline: </p>
					<input type="text" class="input-custom mb-5" v-model="item.auctionDeadline" placeholder="The date must be in format: YYYY/MM/DD HH:MM:SS">
					<a href="javascript:void(0)" class="btn-custom" @click="addItem">Create Item</a>
				</form>
				<div class="close-button">
					<b-icon @click="closeAddDialog" icon="x-circle-fill" variant="danger"></b-icon>
				</div>
			</div>
		</div>
		<div class="edit-item overlay" :class="{ 'active': toggleEditDialog }">
			<div class="form-box">
				<h5 class="mb-4"> Edit antique item </h5>
				<form class="form">
					<p class="mb-0">Item name: </p>
					<input type="text" class="input-custom mb-3" v-model="editItemObject.name">
					<p class="mb-0">Item description: </p>
					<textarea type="text" class="input-custom mb-3" v-model="editItemObject.description"></textarea>
					<p class="mb-0">Price: </p>
					<input type="number" class="input-custom mb-3" v-model="editItemObject.price">
					<p class="mb-0">Auction Deadline: </p>
					<input type="text" class="input-custom mb-5" v-model="editItemObject.auctionDeadline" placeholder="The date must be in format: YYYY/MM/DD HH:MM:SS">
					<a href="javascript:void(0)" class="btn-custom" @click="updateItem">Edit Item</a>
				</form>
				<div class="close-button">
					<b-icon @click="closeEditDialog" icon="x-circle-fill" variant="danger"></b-icon>
				</div>
			</div>
		</div>
		<div class="delete-item overlay" :class="{ 'active': toggleDeleteDialog }">
			<div class="form-box">
				<h5 class="mb-4">Delete antique item</h5>
				<p class="mb-4">Are you sure you want to delete this item? If you delete it, all data regarding this item will be lost</p>
				<div class="d-flex align-center justify-content-center w-100">
					<a href="javascript:void(0)" class="btn-custom red mr-2 w-100" @click="deleteItem">Yes</a>
					<a href="javascript:void(0)" class="btn-custom secondary w-100" @click="closeDeleteDialog">No</a>
				</div>
				<div class="close-button">
					<b-icon @click="closeDeleteDialog" icon="x-circle-fill" variant="danger"></b-icon>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	// @ is an alias to /src
	import HelloWorld from '@/components/HelloWorld.vue'
	import headerComponent from '@/components/Header.vue'

	export default {
		name: 'Home',
		components: {
			HelloWorld,
			headerComponent
		},
		data() {
			return {
				loadingDone: false,
				isAdmin: '',
				item:{
					name: '',
					description: '',
					price: '',
					auctionDeadline: '',
				},
				editItemObject:{
					name: '',
					description: '',
					price: '',
					auctionDeadline: '',
				},
				itemToBeDeleted: '',
				itemToBeEdited: '',
				toggleDeleteDialog:false,
				toggleCreateDialog:false,
				toggleEditDialog: false,
				sortFlag: false,
				pages: 1,
				currentPage: 1,
				itemsPerPage: 10,
				pagesArray: [],
				searchTerm: '',
				items: [],
				paginatedList: [],
			}
		},
		methods: {
			validateAdd(){

			},
			validateEdit(){

			},
			getItems(){
                this.axios.get('/getItems').then(response => {
					this.items = response.data
					this.numberOfPages(this.items)
					this.paginate(this.currentPage, this.items)
					this.loadingDone = true
                }).catch(error => {
					console.log(error)
                });
			},
			addItem(){
				// add item with params item.name, item.description, item.price, item.auctionDeadline
				var data = {
					name: this.item.name,
					description: this.item.description,
					price: this.item.price,
					auctionDeadline: this.item.auctionDeadline
				}
                this.axios.post('/createItem', data).then(response => {
					this.closeAddDialog()
					console.log('response')
					console.log(response)
        			this.$bvToast.toast(response.message, {
        			  title: `Success`,
        			  variant: 'success',
        			  solid: true,
	  				  toaster: 'b-toaster-bottom-center'
					})
					this.getItems()
                }).catch(error => {
        			this.$bvToast.toast(error.response.data.message, {
        			  title: `Error`,
        			  variant: 'danger',
        			  solid: true,
	  				  toaster: 'b-toaster-bottom-center'
					})
                });
			},
			updateItem(){
                this.axios.put('/updateItem/'+this.itemToBeEdited,
				{
					name: this.editItemObject.name,
					description: this.editItemObject.description,
					price: this.editItemObject.price,
					auctionDeadline: this.editItemObject.auctionDeadline
				}).then(response => {
        			this.$bvToast.toast('The item was edited successfully', {
        			  title: `Success`,
        			  variant: 'success',
        			  solid: true,
	  				  toaster: 'b-toaster-bottom-center'
					})
					this.closeEditDialog()
					this.getItems()
                }).catch(error => {
        			this.$bvToast.toast(error.response.data.message, {
        			  title: `Error`,
        			  variant: 'danger',
        			  solid: true,
	  				  toaster: 'b-toaster-bottom-center'
					})
                });

			},
			deleteItem(){
				// delete item with id == this.itemToBeEdited
                this.axios.delete('/deleteItem/'+this.itemToBeDeleted).then(response => {
        			this.$bvToast.toast('The item was deleted successfully', {
        			  title: `Success`,
        			  variant: 'success',
        			  solid: true,
	  				  toaster: 'b-toaster-bottom-center'
					})
					this.closeDeleteDialog()
					this.getItems()
                }).catch(error => {
        			this.$bvToast.toast('The item was not deleted. An error occurred.', {
        			  title: `Error`,
        			  variant: 'danger',
        			  solid: true,
	  				  toaster: 'b-toaster-bottom-center'
					})
                });
			},
			openEditDialog(itemId){
				var element = this.items.find(a => a.id == itemId)
				this.editItemObject.name = element.name
				this.editItemObject.description = element.description
				this.editItemObject.price = element.price
				this.editItemObject.auctionDeadline = element.auctionDeadline
				this.itemToBeEdited = itemId
				this.toggleEditDialog = true
				// Replace item with id == itemId with elements on editItem.name, editItem.description, editItem.price
				var element = document.getElementsByTagName('body')
				element[0].classList.add('modal-open')
			},
			closeEditDialog(){
				this.editItemObject.name = ''
				this.editItemObject.description = ''
				this.editItemObject.price = ''
				this.editItemObject.auctionDeadline = ''
				this.itemToBeEdited = ''
				this.toggleEditDialog = false
				var element = document.getElementsByTagName('body')
				element[0].classList.remove('modal-open')
			},
			openDeleteDialog(itemId){
				this.itemToBeDeleted = itemId
				this.toggleDeleteDialog = true
				// Delete item with id itemId
				var element = document.getElementsByTagName('body')
				element[0].classList.add('modal-open')
			},
			closeDeleteDialog(){
				this.itemToBeDeleted = ''
				this.toggleDeleteDialog = false
				var element = document.getElementsByTagName('body')
				element[0].classList.remove('modal-open')
			},	
			openAddDialog(){
				this.toggleCreateDialog = true;
				var element = document.getElementsByTagName('body');
				element[0].classList.add('modal-open');
			},
			closeAddDialog(){
				this.item.name = '',
				this.item.description = '',
				this.item.price = '',
				this.item.auctionDeadline = '',
				this.toggleCreateDialog = false;
				var element = document.getElementsByTagName('body');
				element[0].classList.remove('modal-open');
			},
			numberOfPages(arrayList) {
				this.pages = Math.ceil(arrayList.length / this.itemsPerPage);
				this.populatePagesArray()
			},
			populatePagesArray() {
				this.pagesArray = []
				for (let i = 1; i <= this.pages; i++) {
					this.pagesArray.push(i);
				}
			},
			paginate(currentPage, itemsList) {
				//Set current page number
				this.currentPage = currentPage
				let offset;

				// Eliminate the possibility of index out of bounds
				if (this.currentPage == 1) {
					offset = 0;
				} else {
					offset = (this.currentPage - 1) * this.itemsPerPage
				}

				// Put 10 items in a new paginated list 
				this.paginatedList = itemsList.slice(offset, offset + this.itemsPerPage)
			},
			searchByTerm() {
				var searchedArray = this.items.filter(el => el.name.toLowerCase().includes(this.searchTerm
				.toLowerCase()) || el.description.toLowerCase().includes(this.searchTerm.toLowerCase()))
				this.paginate(1, searchedArray)
				this.numberOfPages(searchedArray)
			},
			sortByPrice() {
				var array = (this.items.sort((a, b) => {
						if(this.sortFlag){
							return b.price - a.price;
						}
						else{
							return  a.price - b.price;
						}
					}
				));
				this.paginatedList = array
				this.numberOfPages(this.paginatedList)
				this.paginate(1, this.paginatedList)
				this.sortFlag = !this.sortFlag

			},
			checkUserCredentials(){
				if(this.$store.state.user.userType == 1){
					this.isAdmin = true
				}
				else{
					this.isAdmin = false
				}
			},
			openItem(id){
				this.$router.push({name: 'Item', params: { itemId: id }})
			}
		},
		mounted() {
			this.getItems()
			this.checkUserCredentials()
		},
	}
</script>
<style lang="scss">

	.pagination {
		ul {
			width: 100%;
			display: flex;
			justify-content: center;
			list-style: none;
			padding: 0;
			margin: 0;

			li {
				margin-right: 10px;

				&:last-child {
					margin-right: 0;
				}

				a {
					text-decoration: none;
					padding: 5px 10px;
					border-radius: 5px;
					border: 1px solid #4caf50;
					color: #4caf50;
					transition: all 0.3s ease;

					&.active {
						color: white;
						background: #4caf50;
					}

					&:hover {
						background: #4caf50;
						color: white;
					}
				}
			}
		}
	}

	.search-box{

	}
	.add-new{
		a{
		display: block;
    	min-width: 150px;
		}
	}
</style>