import { createRouter, createWebHistory } from 'vue-router'
// import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import LandingView from '../views/LandingView.vue'
import LocationView from '../views/LocationView.vue'
import axios from 'axios'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'login',
            component: LoginView,
        },
        {
            path: '/landing',
            name: 'landing',
            component: LandingView,
        },
        {
            path: '/about',
            name: 'about',
            // route level code-splitting
            // this generates a separate chunk (About.[hash].js) for this route
            // which is lazy-loaded when the route is visited.
            component: () => import('../views/AboutView.vue'),
        },
        {
            path: '/location',
            name: 'location',
            component: LocationView
        }
    ],
})

router.beforeEach((to, from) => {
    if (to.name === 'login') {
        return true
    }

    if (!localStorage.getItem('token')) {
        return {
            name: 'login',
        }
    }

    checkTokenAuthenticity()
})

const checkTokenAuthenticity = () => {
    axios
        .get('http://localhost:8000/api/user', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
        })
        .then((response) => {})
        .catch((error) => {
            localStorage.removeItem('token')
            router.push({
                name: 'login',
            })
        })
}

export default router
