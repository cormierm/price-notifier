import _ from 'lodash';
import axios from 'axios';
import 'bootstrap';

window._ = _;
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
