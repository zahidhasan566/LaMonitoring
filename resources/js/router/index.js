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

import BullTypeList from "../views/admin/bullTypes/BullTypeList.vue";
import BullList from "../views/admin/bulls/BullList.vue";
import EventList from "../views/admin/setting/events/EventList.vue";
import NoticeList from "../views/admin/setting/notices/NoticeList.vue";
import BreedingReportList from "../views/admin/reports/breedingReportList.vue";
import ReBreedingReportList from "../views/admin/reports/reBreedingReportList.vue";


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
                path: baseurl + 'farm/farmlist',
                name: 'FarmList',
                component: FarmList
            },
            //ADMIN ROUTE | Breeding LIST
            {
                path: baseurl + 'breeding/breedingList',
                name: 'BreedingList',
                component: BreedingList
            },
            //ADMIN ROUTE | Bull Type LIST
            {
                path: baseurl + 'breeding/bullTypeList',
                name: 'BullTypeList',
                component: BullTypeList
            },
            //ADMIN ROUTE | Bull LIST
            {
                path: baseurl + 'breeding/bullListID',
                name: 'BullList',
                component: BullList
            },
            //ADMIN ROUTE | Event
            {
                path: baseurl + 'setting/event',
                name: 'EventList',
                component: EventList
            },
            //ADMIN ROUTE | Notice LIST
            {
                path: baseurl + 'setting/notice',
                name: 'NoticeList',
                component: NoticeList
            },
            //ADMIN ROUTE | Breeding Report LIST
            {
                path: baseurl + 'report/breeding',
                name: 'BreedingReportList',
                component: BreedingReportList
            },
            //ADMIN ROUTE | Re Breeding Report LIST
            {
                path: baseurl + 'report/reBreeding',
                name: 'ReBreedingReportList',
                component: ReBreedingReportList
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
