require('./bootstrap');
require('./validation/index');

import Vue from 'vue'
import App from './views/App'
import router from './router/index';
import store from './store/index'
import {baseurl} from './base_url'

// main origin
Vue.prototype.mainOrigin = baseurl


//Vue Multiselect
import Multiselect from 'vue-multiselect'
Vue.component('multiselect', Multiselect)
//Bus to transfer data
export const bus = new Vue();


//Vue Datepicker
import { Datepicker } from '@livelybone/vue-datepicker';
Vue.component('datepicker', Datepicker);
import '@livelybone/vue-datepicker/lib/css/index.css'


//Toaster
import Toaster from 'v-toaster'
import 'v-toaster/dist/v-toaster.css'
Vue.use(Toaster, {timeout: 5000})

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
Vue.use(BootstrapVue)

//moment
import moment from 'moment'


//Component
Vue.component('skeleton-loader', require('./components/loaders/Straight').default);
Vue.component('submit-form', require('./components/buttons/Submit').default);
Vue.component('advanced-datatable', require('./components/datatable/Advanced').default);
Vue.component('breadcrumb', require('./components/layouts/Breadcrumb').default);
Vue.component('data-export', require('./components/datatable/Export').default);

Vue.component('add-edit-user',require('./components/users/AddEditModal').default);
Vue.component('add-edit-farmListData',require('./components/farms/AddEditModal').default);
Vue.component('add-edit-breedingListData',require('./components/breeding/AddEditModal').default);
Vue.component('add-edit-bullTypeListData',require('./components/bullTypes/AddEditModal').default);
Vue.component('add-edit-bullListData',require('./components/bulls/AddEditModal').default);
Vue.component('add-edit-eventData',require('./components/setting/events/AddEditModal').default);
Vue.component('add-edit-noticeData',require('./components/setting/notices/AddEditModal').default);


Vue.component('reset-password',require('./components/users/Editpassword').default);
Vue.component('submit-form', require('./components/buttons/Submit').default);



const app = new Vue({
    el: '#app',
    store: store,
    components: {App},
    router,
});
