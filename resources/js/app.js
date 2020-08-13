import Buefy from 'buefy';
import 'buefy/dist/buefy.css';

require('./bootstrap');

window.Vue = require('vue');

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('watcher-form', require('./components/watcher/Form.vue').default);
Vue.component('home', require('./components/views/Home.vue').default);

Vue.use(Buefy);

const app = new Vue({
    el: '#app',
});
