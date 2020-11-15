<template>
    <div class="login-background">
        <div class="form-box-holder">
            <h2 class="mb-3">Antique</h2>
            <template v-if="loginPage">
                <transition name="slide-from-up">
                    <div class="error-banner mb-3" v-if="loginFailed">
                        <b-icon icon="exclamation-triangle-fill" scale="2" variant="danger" class="mr-3"></b-icon>
                        {{loginFailedMessage}}
                    </div>
                </transition>
                <form action="" class="form-holder" @keyup.enter="login">
                    <div class="username mb-3">
                        <label>Username:</label>
                        <input class="input-custom" type="text" v-model="loginUser.username" name="user">
                        <transition name="slide-from-up">
                            <p class="error-message" v-if="!loginUsername">The username is
                                required</p>
                        </transition>
                    </div>
                    <div class="password">
                        <label>Password:</label>
                        <input class="input-custom" v-model="loginUser.password" type="password" name="password">
                        <transition name="slide-from-up">
                            <p class="error-message" v-if="!loginPassword">The password is
                                required</p>
                        </transition>
                    </div>
                    <a @click="login" href="javascript:void(0)" :class="{ 'removeDisabled': isLoginValidated }"
                        class="btn-custom mt-5">LOGIN</a>

                    <p class="auth-switcher text-center mt-3">Not a user? <a href="javascript:void(0)"
                            @click="loginPage=false">Click here</a> to register</p>

                </form>
            </template>
            <template v-else>
                <transition name="slide-from-up">
                    <div class="error-banner mb-3" v-if="registrationFailed">
                        <b-icon icon="exclamation-triangle-fill" scale="2" variant="danger" class="mr-3"></b-icon>
                        Registration Failed!
                    </div>
                </transition>
                <form action="" class="form-holder" @keyup.enter="register">
                    <div class="username mb-3">
                        <label>Username:</label>
                        <input class="input-custom" type="text" v-model="registerUser.username" name="user">
                        <transition name="slide-from-up">
                            <p class="error-message" v-if="!registerUsername">The username is
                                required</p>
                        </transition>
                    </div>
                    <div class="email mb-3">
                        <label>Email:</label>
                        <input class="input-custom" type="text" v-model="registerUser.email" name="user">
                        <transition name="slide-from-up">
                            <p class="error-message" v-if="!registerEmail">The username is
                                required</p>
                        </transition>
                    </div>
                    <div class="password mb-3">
                        <label>Password:</label>
                        <input class="input-custom" v-model="registerUser.password" type="password" name="password">
                        <transition name="slide-from-up">
                            <p class="error-message" v-if="!registerPassword">The password is
                                required</p>
                        </transition>
                    </div>
                    <div class="confirm-password mb-3">
                        <label>Confirm Password:</label>
                        <input class="input-custom" v-model="registerUser.confirmPassword" type="password"
                            name="password">
                        <transition name="slide-from-up">
                            <p class="error-message" v-if="!registerPasswordMatch">Passwords should match</p>
                        </transition>
                    </div>
                    <div class="checkbox">
                        <b-form-checkbox id="checkbox-1" v-model="registerUser.isAdmin" name="checkbox-1">
                            Check this if this user is an admin
                        </b-form-checkbox>
                    </div>
                    <a @click="register" href="javascript:void(0)" :class="{ 'removeDisabled': isRegisterValidated }"
                        class="btn-custom mt-5">REGISTER</a>

                    <p class="auth-switcher text-center mt-3">Already registered? <a href="javascript:void(0)"
                            @click="loginPage=true">Sign in</a> instead</p>
                </form>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                registrationFailed: false,
                loginFailedMessage:'',
                loginPage: true,
                loginFailed: false,
                registrationFailed: false,
                loginUser: {
                    username: '',
                    password: '',
                },
                registerUser: {
                    username: '',
                    email: '',
                    password: '',
                    confirmPassword: '',
                    isAdmin: false
                }

            }
        },
        methods: {
            login() {
                const data = {
                    username: this.loginUser.username,
                    password: this.loginUser.password,
                }
                this.axios.post('/login', data).then(response => {
                    this.loginFailed = false
                    this.$store.dispatch('storeUser', response.data)
                    this.$router.push({
                        name: 'Home'
                    })
                }).catch(error => {
                    this.loginFailedMessage = error.response.data.error
                    this.loginFailed = true
                })
            },
            register() {
                const data = {
                    username: this.registerUser.username,
                    password: this.registerUser.password,
                    email: this.registerUser.email,
                    userType: this.registerUser.isAdmin == true ? 1 : 2,
                }

                this.axios.post('/register', data).then(response => {
                    this.loginFailed = false
                    this.$store.dispatch('storeUser', response.data)
                    this.$router.push({
                        name: 'Home'
                    })
                }).catch(error => {
                    this.loginFailed = true
                })
            }
        },
        computed: {
            isLoginValidated: function () {
                if (this.loginUsername && this.loginPassword) {
                    return true
                } else {
                    return false
                }
            },
            isRegisterValidated: function () {
                if (this.registerUsername && this.registerPassword && this.registerEmail && this
                    .registerPasswordMatch) {
                    return true
                } else {
                    return false
                }
            },
            loginUsername: function () {
                if (this.loginUser.username) {
                    this.loginUser.showUsernameValidation = false
                    return true
                } else {
                    this.loginUser.showUsernameValidation = true
                    return false
                }
            },
            loginPassword: function () {
                if (this.loginUser.password) {
                    return true
                } else {
                    return false
                }
            },
            registerUsername: function () {
                if (this.registerUser.username) {
                    return true
                } else {
                    return false
                }
            },
            registerEmail: function () {
                if (this.registerUser.email) {
                    return true
                } else {
                    return false
                }
            },
            registerPassword: function () {
                if (this.registerUser.password) {
                    return true
                } else {
                    return false
                }
            },
            registerPasswordMatch: function () {
                if (this.registerUser.password == this.registerUser.confirmPassword && this.registerUser
                    .confirmPassword) {
                    return true
                } else {
                    return false
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
    .error-banner {
        padding: 10px;
        border: 1px solid rgb(255, 65, 65);
        border-radius: 5px;
    }

    .login-background {
        position: relative;
        background: #4caf50;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        .form-box-holder {
            border-radius: 5px;
            min-width: 500px;
            background: white;
            padding: 80px;
            -webkit-box-shadow: 0px 0px 25px 0px rgba(50, 50, 50, 0.5);
            -moz-box-shadow: 0px 0px 25px 0px rgba(50, 50, 50, 0.5);
            box-shadow: 0px 0px 25px 0px rgba(50, 50, 50, 0.5);

            .form-holder,
            .username,
            .password,
            .confirm-password,
            .email {
                display: grid;
                text-align: left;

                input,
                a {
                    display: inline-block;
                }

                .error-message {
                    color: rgb(255, 49, 49);
                    font-size: 14px;
                    margin: 0;
                    padding: 0;
                }
            }

            .btn-custom {
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
        }
    }

    .slide-from-up-enter-active {
        transition: all .3s ease;
        max-height: 100px;
    }

    .slide-from-up-leave-active {
        transition: all .3s ease;
        max-height: 100px;
    }

    .slide-from-up-enter,
    .slide-from-up-leave-to {
        transform: translateY(-20px);
        opacity: 0;
        max-height: 0px;
    }

    @media (max-width: 550px) {
        .form-box-holder {
            h2 {
                font-size: 24px;
            }
        }

        .login-background {
            .form-box-holder {
                min-width: 385px;
                padding: 55px;
            }
        }
    }

    @media (max-width: 425px) {
        .login-background {
            .form-box-holder {
                width: -webkit-fill-available;
                min-width: auto;
                padding: 20px;
                margin: 20px;
            }
        }
    }
</style>