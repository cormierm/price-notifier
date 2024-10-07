import _ from 'lodash';
import axios from 'axios';
import { library, dom } from '@fortawesome/fontawesome-svg-core';
import { faCircleHalfStroke, faSun, faMoon, faDesktop, faUser, faGear, faRightFromBracket } from '@fortawesome/free-solid-svg-icons';

library.add(faCircleHalfStroke, faSun, faMoon, faDesktop, faUser, faGear, faRightFromBracket);

dom.watch();

window._ = _;
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
