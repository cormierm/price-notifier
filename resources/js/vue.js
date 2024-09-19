import Vue from 'vue';
import Buefy from 'buefy';
import 'buefy/dist/buefy.css';

import TemplateForm from './components/template/Form.vue';
import WatcherForm from './components/watcher/Form.vue';
import WatcherLogs from './components/watcher/Logs.vue';
import DashboardIndex from './components/views/DashboardIndex.vue';
import ProfileIndex from './components/views/ProfileIndex.vue';
import TemplateIndex from './components/views/TemplateIndex.vue';
import WatcherIndex from './components/views/WatcherIndex.vue';
import WatcherDetails from './components/views/WatcherDetails.vue';
import NavBar from './components/NavBar.vue';

Vue.component('template-form', TemplateForm);
Vue.component('watcher-form', WatcherForm);
Vue.component('watcher-logs', WatcherLogs);
Vue.component('dashboard-index', DashboardIndex);
Vue.component('profile-index', ProfileIndex);
Vue.component('template-index', TemplateIndex);
Vue.component('watcher-index', WatcherIndex);
Vue.component('watcher-details', WatcherDetails);
Vue.component('nav-bar', NavBar);

Vue.use(Buefy);

const app = new Vue({
    el: '#app',
});
