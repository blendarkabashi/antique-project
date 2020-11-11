<template>
    <div class="login-background">
        <div class="form-box-holder">
            <h2 class="mb-3">Antique</h2>
            <transition name="slide-from-up">
                <div class="error-banner mb-3" v-if="loginFailed">
                    <b-icon icon="exclamation-triangle-fill" scale="2" variant="danger" class="mr-3"></b-icon>
                    Username or Password incorrect!
                </div>
            </transition>
            <form action="" class="form-holder" @keyup.enter="login">
                <div class="username mb-3">
                    <label>Username:</label>
                    <input @input="validateUsername()" class="input-custom" type="text" v-model="user.username"
                        name="user">
                    <transition name="slide-from-up">
                        <p class="error-message" v-if="!usernameValid && usernameValidationStarted">The username is
                            required</p>
                    </transition>
                </div>
                <div class="password">
                    <label>Password:</label>
                    <input @input="validatePassword()" class="input-custom" v-model="user.password" type="password"
                        name="password">
                    <transition name="slide-from-up">
                        <p class="error-message" v-if="!passwordValid && passwordValidationStarted">The password is
                            required</p>
                    </transition>
                </div>
                <a @click="login" href="javascript:void(0)" :class="{ 'removeDisabled': isValidated }"
                    class="btn-custom mt-5">LOGIN</a>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                loginFailed: false,
                usernameValidationStarted: false,
                passwordValidationStarted: false,
                usernameValid: false,
                passwordValid: false,
                user: {
                    username: '',
                    password: '',
                }
            }
        },
        methods: {
            validateUsername() {
                this.usernameValidationStarted = true
                if (this.user.username.length < 1) {
                    this.usernameValid = false
                } else {
                    this.usernameValid = true
                }
            },
            validatePassword() {
                this.passwordValidationStarted = true
                if (this.user.password.length < 1) {
                    this.passwordValid = false
                } else {
                    this.passwordValid = true
                }
            },
            login() {
                if (this.user.username == 'user' && this.user.password == 'user') {
                    var user = {
                        username: this.user.username,
                        password: this.user.password,
                        userType: 2
                    }
                    this.loginFailed = false
                    this.$store.dispatch('storeUser', user)
                    this.$router.push({name: 'Home'})
                } else if (this.user.username == 'admin' && this.user.password == 'admin') {
                    var user = {
                        username: this.user.username,
                        password: this.user.password,
                        userType: 1
                    }
                    this.loginFailed = false
                    this.$store.dispatch('storeUser', user)
                    this.$router.push({name: 'Home'})
                } else {
                    this.loginFailed = true
                }
            }
        },
        computed: {
            isValidated: function () {
                if (this.usernameValid && this.passwordValid) {
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
        height: 100vh;
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
            .password {
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