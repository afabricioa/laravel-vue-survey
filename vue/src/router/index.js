import {createRouter, createWebHistory} from "vue-router";
import Dashboard from "../views/Dashboard.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import DefaultLayout from "../components/DefaultLayout.vue";
import AuthLayout from "../components/AuthLayout.vue";
import Surveys from "../views/Surveys.vue";
import SurveyView from "../views/SurveyView.vue";
import {useUserStore} from "../store/index";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            redirect: '/dashboard',
            component: DefaultLayout,
            meta: {requiresAuth: true},
            children: [
                {path: '/dashboard', name: 'Dashboard', component: Dashboard},
                {path: '/surveys', name: 'Surveys', component: Surveys},
                {path: "/surveys/create", name: "SurveyCreate", component: SurveyView},
                {path: "/surveys/:id", name: "SurveyView", component: SurveyView}
            ]
        },
        {
            path: '/auth',
            name: 'Auth',
            redirect: '/login',
            component: AuthLayout,
            meta: {isGuest: true},
            children: [
                {
                    path: '/login',
                    name: 'Login',
                    component: Login
                },
                {
                    path: '/register',
                    name: 'Register',
                    component: Register
                }
            ]
        }
    ]
});

router.beforeEach((to, from, next) => {
    const userStore = useUserStore();
    if(to.meta.requiresAuth && !userStore.user.token){
        next({name: 'Login'})
    } else if(userStore.user.token && to.meta.isGuest){
        next({name: 'Dashboard'})
    } else {
        next()
    }
});

export default router
