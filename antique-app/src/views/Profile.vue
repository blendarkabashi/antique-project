<template>
    <div>
        <div class="container">
            <header-component></header-component>
            <div class="mt-4">
                <div class="text-left">
                    <h4>Your profile</h4>
                    <p>Logged in with user: <span class="font-weight-bold">{{$store.state.user.name}}</span></p>
                </div>
                <div class="table-wrapper mb-4">
                    <div class="table">
                        <div class="row header">
                            <div class="cell">
                                Item Id
                            </div>
                            <div class="cell">
                                Date of bid
                            </div>
                            <div class="cell">
                                Status
                            </div>
                        </div>
                        <div class="row" v-for="(bid,key) in bidsMade" :key="key">
                            <div class="cell" data-title="Item Id">
                                {{bid.itemId}}
                            </div>
                            <div class="cell" data-title="Date">
                                {{bid.created_at}}
                            </div>
                            <div class="cell" data-title="Status">
                                <template v-if="inProgressBidsIds.includes(bid.id)">                                        
                                    <p class="mb-0">
                                        In Progress
                                    </p>
                                </template>
                                <template v-else-if="winningBidsIds.includes(bid.id)">                                        
                                    <p class="mb-0 text-success">
                                        Won
                                    </p>
                                </template>
                                <template v-else>                                        
                                    <p class="mb-0 text-danger">
                                        Lost
                                    </p>
                                </template>
                            </div>
                        </div>
                    </div>
					<template v-if="bidsMade.length <1">
						<div class="w-100 text-center">
							<p>No items to show</p>
						</div>
					</template>
                </div>
                <hr class="my-5">
                <div class="awards">
                    <h4 class="text-left mb-4">Auctions won:</h4>
                        <template v-if="awardedBids.length > 0">
                    <div class="row">
                            <div class="col-md-6 col-lg-4 award-box" v-for="(awarded,key) in awardedBids" :key="key">
                                <div class="award-content">
                                    <p style="font-size:40px; color:#DAA520;">
                                        <b-icon icon="trophy-fill"></b-icon>
                                    </p>
                                    <h5 style="">{{awarded.name}}</h5>
                                    <h5>${{awarded.highestBid.bid}}</h5>
                                    <p style="">{{awarded.description}}</p>
                                </div>
                            </div>
                    </div>
                        </template>
                        <template v-else>
                            You have not won an auction yet
                        </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import headerComponent from '@/components/Header.vue'

    export default {
        components: {
            headerComponent
        },
        data() {
            return {
                winningBidsIds: [],
                inProgressBidsIds: [],
                awardedBids: [],
                bidsMade: [],
                bidsIds: [],
            }
        },
        methods: {
            getAwards() {
                this.axios.get('/getItems').then(response => {
                    let userId = this.$store.state.user.id
                    const self = this

                    this.inProgressBidsIds = response.data.filter(c => !self.isDeadlinePast(c))
                    this.inProgressBidsIds = this.inProgressBidsIds.map(c => c.highestBid.bidId)

                    this.awardedBids = response.data.filter(c => c.highestBid.userId == userId)
                    this.awardedBids = this.awardedBids.filter(c => self.isDeadlinePast(c))

                    this.winningBidsIds = this.awardedBids.map(c => c.highestBid.bidId)

                }).catch(error => {
                    console.log(error)
                })
            },
            isDeadlinePast(el){
                let now = new Date()
                let deadline = new Date(el.auctionDeadline)
                return deadline < now
            },
            getBids() {
                this.axios.get('/getBidsByUser/'+this.$store.state.user.id).then(response => {
                    this.bidsMade = response.data
                    this.bidsIds = this.bidsMade.map(c => c.id)
                }).catch(error => {
                    console.log(error)
                })
            }
        },
        mounted() {
            this.getBids();
            this.getAwards();
        },
    }
</script>

<style lang="scss" scoped>
    .awards {
        .award-box {
            .award-content {
                border-radius: 5px;
                height: 300px;
                background: #f1f1f1;
                margin-bottom: 30px;
                padding: 5%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
            }
        }
    }
</style>