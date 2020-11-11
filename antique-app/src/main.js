import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from '@/store'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import './assets/style/global.scss'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import axios from 'axios'
import VueAxios from 'vue-axios'

Vue.use(VueAxios, axios)
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

Vue.config.productionTip = false

Vue.axios.defaults.baseURL = 'http://127.0.0.1:8000/api/';

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
