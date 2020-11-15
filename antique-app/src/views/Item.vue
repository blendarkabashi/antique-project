<template>
    <div>
        <div class="container">
            <header-component></header-component>

            <div class="item-box mt-5">
                <b-spinner v-if="!loadingFetchDone" label="Loading..."></b-spinner>
                <template v-else>
                    <!--<div class="row mx-0 align-center w-100">-->
                    <b-row class="mx-0 justify-content-center mb-5">
                        <h2>{{ item.name }}</h2>
                    </b-row>
                    <b-row class="mx-0 align-center">
                        <b-col md="6">
                            <div class="content text-left">
                                <p class="font-weight-bold mb-0">Description: </p>
                                <p>{{item.description}}</p>
                                <p class="font-weight-bold mb-0">Price: </p>
                                <p>${{item.price}}</p>
                            </div>
                        </b-col>
                        <b-col md="6">
                            <b-spinner v-if="!loadingDone" label="Loading..."></b-spinner>
                            <div v-else>
                                <div v-if="timerDone">
                                    <template>
                                        <div class="alert" v-if="this.item.highestBid != 0">
                                            <p style="font-size: 40px;">
                                                <b-icon icon="award" aria-hidden="true"></b-icon>
                                            </p>
                                            <h5>The winner of the auction is: <span
                                                    class="font-weight-bold">{{winningUser.name}}</span></h5>
                                            <h5>Winning bid: <span class="font-weight-bold">${{winningBid.bid}}</span>
                                            </h5>
                                            <p>This auction is already awarded. Please go to another item to continue
                                                bidding.</p>
                                        </div>
                                        <div class="alert red" v-else>
                                            <h5>We are sorry, the deadline for this item has passed.</h5>
                                            <p class="mb-0">No bid was made.</p>
                                        </div>

                                    </template>
                                </div>
                                <div class="countdown" v-else>
                                    <h5 class="mb-3">Bidding closed in:</h5>
                                    <ul class="d-flex align-center justify-content-center">
                                        <li>{{this.days}} days</li>
                                        <li>{{this.hours}} hours</li>
                                        <li>{{this.mins}} minutes</li>
                                        <li>{{this.secs}} seconds</li>
                                    </ul>
                                    <div class="d-block mt-5">
                                        <template v-if="minBid > 0">
                                            <p class="text-left mb-0 font-weight-bold">Minimum bid: ${{ minBid }} </p>
                                            <p class="text-left font-italic pl-0 ml-0 mb-1 ">NOTE: The bid should be
                                                higher than the minimum bid!</p>
                                        </template>
                                        <p v-else class="text-left mb-1 font-weight-bold">No one has bid on this item
                                            yet. Be the first! </p>
                                        <input @input="validateBid" type="number"
                                            class="input-custom mr-3 mb-4 h-100 w-100" placeholder="Enter your bid"
                                            v-model="currentBid">
                                        <a href="javascript:void(0)" :class="{'removeDisabled' : isBidValidated}"
                                            class="btn-custom-bid search h-100 w-100 d-block" @click="makeBid">Bid
                                            Now</a>
                                    </div>
                                </div>
                            </div>
                        </b-col>
                    </b-row>
                    <b-row class="button-holder mt-4">
                        <b-button size="sm" class="mb-2" style="margin:0 auto"
                            @click="toggleBiddingHistoryDialog = true">
                            <b-icon icon="clock-history" aria-hidden="true"></b-icon> View Bid History
                        </b-button>
                    </b-row>
                </template>
                <!--</div>-->
            </div>

        </div>
        <div class="bidding-history overlay" :class="{ 'active': toggleBiddingHistoryDialog }">
            <div class="form-box">
                <h5 class="mb-4">Bidding history</h5>
                <p v-if="biddingHistory < 1">No bidding history for this item.</p>
                <ul style="padding:0" v-else>
                    <li v-for="(log,key) in biddingHistory" :key="key">User with id {{log.userId}} made a bid of:
                        ${{log.bid}} at: {{log.created_at}}</li>
                </ul>
                <div class="close-button">
                    <b-icon @click="toggleBiddingHistoryDialog = false" icon="x-circle-fill" variant="danger"></b-icon>
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
                winningUser: '',
                winningBid: '',
                biddingHistory: '',
                loadingFetchDone: false,
                item: {},
                isBidValidated: false,
                currentBid: '',
                toggleBiddingHistoryDialog: false,
                loadingDone: false,
                minBid: 160,
                timerDone: false,
                days: '',
                hours: '',
                mins: '',
                secs: '',
                winnerSet: false,
            }
        },
        methods: {
            checkForWinner() {
                this.timerDone = true
                if (this.item.highestBid != 0) {
                    this.winningBid = this.item.highestBid
                    this.axios.get('/getUser/' + this.item.highestBid.userId).then(response => {
                        this.winningUser = response.data
                        this.sendWinningEmail()
                    }).catch(error => {
                        console.log('User not found.')
                    })
                }
            },
            sendWinningEmail() {
                if (!self.winnerSet) {

                    var data = {
                        email: this.winningUser.email
                    }
                    this.axios.post('/auctionWonNotification', data).then(response => {
                        console.log("Email sent to: " + data.email)
                        this.winnerSet = true
                    }).catch(error => {
                        console.log(error)
                    })
                }
                else{
                    return
                }
            },
            getBiddingHistory() {
                this.axios.get('/getBidsByItem/' + this.$route.params.itemId).then(response => {
                    this.biddingHistory = response.data
                }).catch(error => {
                    console.log(error)
                });
            },
            getUsers() {
                let users;
                let userId = this.$store.state.user.id
                this.axios.get('/getBids').then(response => {
                    users = response.data.filter(c => c.userId != userId && c.itemId == this.$route.params
                        .itemId)
                    if (users.length != 0) {
                        users = users.map(c => c.userId)
                        let usersUnique = [...new Set(users)];
                        this.getEmails(usersUnique)
                    }
                }).catch(error => {
                    console.log(error)
                })
            },
            getEmails(users) {
                let userEmails;
                this.axios.get('/getUsers').then(response => {
                    userEmails = response.data.filter(c => users.includes(c.id))
                    userEmails = userEmails.map(c => c.email)
                    this.sendEmails(userEmails)
                }).catch(error => {
                    console.log(error)
                })

            },
            sendEmails(userEmails) {

                let data = {
                    emails: JSON.stringify(userEmails)
                }

                this.axios.post('/bidMadeNotification', data).then(response => {
                    console.log('Email sent to ' + data.emails)
                }).catch(error => {
                    console.log(error)
                })
            },
            makeBid() {
                var data = {
                    bid: parseInt(this.currentBid),
                    userId: this.$store.state.user.id,
                }
                this.axios.post('/makeBid/' + this.$route.params.itemId, data).then(response => {
                    this.$bvToast.toast('Bid made successfully!', {
                        title: `Success`,
                        variant: 'success',
                        solid: true,
                        toaster: 'b-toaster-bottom-center'
                    })
                    this.currentBid = ''
                    this.validateBid()
                    this.getBiddingHistory()
                    this.getItem()
                    this.getUsers()
                }).catch(error => {
                    this.$bvToast.toast('Bidding failed. An error occurred.', {
                        title: `Error`,
                        variant: 'danger',
                        solid: true,
                        toaster: 'b-toaster-bottom-center'
                    })
                });
            },
            getItem() {
                this.axios.get('/getItem/' + this.$route.params.itemId).then(response => {
                    this.item = response.data[0]
                    if (this.item.highestBid != 0) {
                        this.minBid = this.item.highestBid.bid
                    } else {
                        this.minBid = this.item.price
                    }
                    this.loadingFetchDone = true
                    this.setEndOfTimer()
                    this.getBiddingHistory()
                }).catch(error => {
                    console.log(error)
                });
            },
            validateBid() {
                if (this.minBid == 0) {
                    if (parseInt(this.currentBid) > this.item.price) {
                        this.isBidValidated = true
                    } else {
                        this.isBidValidated = false
                    }
                } else {
                    if (parseInt(this.currentBid) > this.minBid) {
                        this.isBidValidated = true
                    } else {
                        this.isBidValidated = false
                    }
                }
            },
            setEndOfTimer() {
                var countDownDate = new Date(this.item.auctionDeadline).getTime();
                const self = this

                var myfunc = setInterval(function () {
                    var now = new Date().getTime();
                    var timeleft = countDownDate - now;
                    self.days = Math.floor(timeleft / (1000 * 60 * 60 * 24));
                    self.hours = Math.floor((timeleft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    self.mins = Math.floor((timeleft % (1000 * 60 * 60)) / (1000 * 60));
                    self.secs = Math.floor((timeleft % (1000 * 60)) / 1000);
                    self.loadingDone = true
                    if (timeleft < 0) {
                        clearInterval(myfunc);
                        self.checkForWinner()
                        self.loadingDone = true
                    }
                }, 1000);
            },
        },
        mounted() {
            this.getItem()
        },
    }
</script>

<style lang="scss" scoped>
    .minimum-bid {
        font-size: 14px;
        font-style: italic;
    }

    .item-box {
        padding: 5%;
        height: 100%;
        min-height: 500px;
        border-radius: 5px;
        background: rgb(226, 226, 226);
    }

    .alert {
        color: white;
        padding: 10px;
        background: #4caf50;

        &.red {
            background: rgb(255, 65, 65);
        }
    }

    .countdown {
        background: white;
        padding: 40px;
        border-radius: 5px;

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
            background: rgb(245, 245, 245);
            border: 1px solid rgb(172, 172, 172);
            border-radius: 5px;

            li {
                padding: 10px;
                min-width: 50px;
                margin-right: 20px;
                font-size: 18px;

                &:last-child {
                    margin-right: 0;
                }
            }
        }
    }

    @media (max-width: 1000px) {
        .countdown {
            ul {
                flex-direction: column;

                li {
                    margin-right: 0;
                }
            }
        }
    }

    @media (max-width: 550px) {
        .countdown {
            ul {
                flex-direction: row;
                flex-wrap: wrap;
                background: none;
                border: none;

                li {
                    margin-right: 0;
                    font-size: 16px;
                }
            }
        }
    }

    .button-holder {}

    .bidding-history {
        .form-box {
            overflow-y: auto;
            display: unset;
        }
    }

    .btn-custom-bid {
        cursor: not-allowed;
        background: #76af77;
        padding: 10px;
        color: white;
        width: 100%;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
        transition: all 0.3s ease;

        &:hover {
            text-decoration: none;
        }

        &.removeDisabled {
            cursor: pointer;
            background: #4caf50;

            &:hover {
                transition: all 0.3s ease;
                background: #47a54a;
            }
        }
    }
</style>