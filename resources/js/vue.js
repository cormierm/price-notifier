import Buefy from 'buefy';
import 'buefy/dist/buefy.css';

window.Vue = require('vue');

Vue.component('watcher-form', require('./components/watcher/Form.vue').default);
Vue.component('watcher-logs', require('./components/watcher/Logs.vue').default);
Vue.component('home', require('./components/views/Home.vue').default);
Vue.component('nav-bar', require('./components/NavBar.vue').default);

Vue.use(Buefy);

const app = new Vue({
    el: '#app',
});
