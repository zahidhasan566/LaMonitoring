import Vue from 'vue'
import VueRouter from 'vue-router';
import Main from '../components/layouts/Main';
import Dashboard from '../views/dashboard/Index.vue';
import Login from '../views/auth/Login.vue';
import {baseurl} from '../base_url'
import NotFound from '../views/404/Index';

import Users from '../views/users/Index';
import FarmList from "../views/admin/farms/farmList.vue";
import BreedingList from "../views/admin/breeding/breedingList.vue";

import BullTypeList from "../views/admin/BullTypes/BullTypeList.vue";
import BullList from "../views/admin/Bulls/BullList.vue";

Vue.use(VueRouter);

const config = () => {
    let token = localStorage.getItem('token');
    return {
        headers: {Authorization: `Bearer ${token}`}
    };
}
const checkToken = (to, from, next) => {
    let token = localStorage.getItem('token');
    if (token === undefined || token === null || token === '') {
        next(baseurl + 'login');
    } else {
        next();
    }
};

const activeToken = (to, from, next) => {
    let token = localStorage.getItem('token');
    if (token === 'undefined' || token === null || token === '') {
        next();
    } else {
        next(baseurl);
    }
};

const routes = [
    {
        path: baseurl,
        component: Main,
        redirect: {name: 'Dashboard'},
        children: [
            //COMMON ROUTE | SHOW DASHBOARD DATA
            {
                path: baseurl + 'dashboard',
                name: 'Dashboard',
                component: Dashboard
            },
            //ADMIN ROUTE | SHOW USER LIST
            {
                path: baseurl + 'users',
                name: 'Users',
                component: Users
            },
            //ADMIN ROUTE | FARM LIST
            {
                path: baseurl + 'Farm/Farmlist',
                name: 'FarmList',
                component: FarmList
            },
            //ADMIN ROUTE | Breeding LIST
            {
                path: baseurl + 'Breeding/BreedingList',
                name: 'BreedingList',
                component: BreedingList
            },
            //ADMIN ROUTE | Bull Type LIST
            {
                path: baseurl + 'Breeding/BullTypeList',
                name: 'BullTypeList',
                component: BullTypeList
            },
            //ADMIN ROUTE | Bull Type LIST
            {
                path: baseurl + 'Breeding/BullListID',
                name: 'BullList',
                component: BullList
            },

        ],
        beforeEnter(to, from, next) {
            checkToken(to, from, next);
        }
    },
    {
        path: baseurl + 'login',
        name: 'Login',
        component: Login,
        beforeEnter(to, from, next) {
            activeToken(to, from, next);
        }
    },
    {
        path: baseurl + '*',
        name: 'NotFound',
        component: NotFound,
    },
]
const router = new VueRouter({
    mode: 'history',
    base: process.env.baseurl,
    routes
});

router.afterEach(() => {
    $('#preloader').hide();
});

export default router
