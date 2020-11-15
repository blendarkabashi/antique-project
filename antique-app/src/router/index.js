import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '../views/Login.vue'
import store from '../store/index'

Vue.use(VueRouter)

const routes = [{
    path: '/login',
    name: 'Login',
    component: Login,
    meta:{
      requiresAuthorization: false
    }
  },
  {
    path: '/about',
    name: 'About',
    meta:{
      requiresAuthorization: true
    },
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import( /* webpackChunkName: "about" */ '../views/About.vue')
  },
  {
    path: '',
    name: 'Home',
    meta:{
      requiresAuthorization: true
    },
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import( /* webpackChunkName: "about" */ '../views/Home.vue')
  },
  {
    path: '/profile',
    name: 'Profile',
    meta:{
      requiresAuthorization: true
    },
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import( /* webpackChunkName: "about" */ '../views/Profile.vue')
  },
  {
    path: '/:itemId',
    name: 'Item',
    meta:{
      requiresAuthorization: true
    },
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import( /* webpackChunkName: "about" */ '../views/Item.vue')
  }
]
const router = new VueRouter({
  mode: 'history',
  routes
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuthorization) {
    if (!store.state.isAuthenticated) {
      next({
        name: 'Login'
      })
    } else {
      next()
    }
  }
  else{
    next()
  }
});

export default router