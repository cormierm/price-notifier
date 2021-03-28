import Buefy from 'buefy';
import 'buefy/dist/buefy.css';

import {MazPhoneNumberInput} from 'maz-ui';

window.Vue = require('vue');

Vue.component('template-form', require('./components/template/Form.vue').default);
Vue.component('watcher-form', require('./components/watcher/Form.vue').default);
Vue.component('watcher-logs', require('./components/watcher/Logs.vue').default);
Vue.component('dashboard-index', require('./components/views/DashboardIndex.vue').default);
Vue.component('profile-index', require('./components/views/ProfileIndex.vue').default);
Vue.component('template-index', require('./components/views/TemplateIndex.vue').default);
Vue.component('watcher-index', require('./components/views/WatcherIndex.vue').default);
Vue.component('watcher-details', require('./components/views/WatcherDetails.vue').default);
Vue.component('nav-bar', require('./components/NavBar.vue').default);
Vue.use(MazPhoneNumberInput);

Vue.use(Buefy);

const app = new Vue({
    el: '#app',
});
