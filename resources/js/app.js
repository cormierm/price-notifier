import _ from 'lodash';
import axios from 'axios';

window._ = _;
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
