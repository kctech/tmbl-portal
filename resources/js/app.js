
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Alpine from 'alpinejs';
//import 'bootstrap-datepicker';
//require('bootstrap-datepicker');

/*fontawesome6
//import { library, dom } from '@fortawesome/fontawesome-svg-core';
//import { far, fal, fas, fab } from '@awesome.me/kit-2762277c30/icons';
//library.add(far, fas, fal, fab);
*/

/*fontawesome5*/
import { library, dom } from '@fortawesome/fontawesome-svg-core';
import { far } from '@fortawesome/pro-regular-svg-icons';
import { fas } from '@fortawesome/pro-solid-svg-icons';
import { fal } from '@fortawesome/pro-light-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';
import { faFacebookF } from '@fortawesome/free-brands-svg-icons';
library.add(far, fas, fal, fab, faFacebookF);

/* Kicks off the process of finding <i> tags and replacing with <svg>*/
dom.watch();

window.Alpine = Alpine;
//window.$ = window.jQuery = require('jquery'); //<-- added via bootstrap.js
window.Select2 = window.select2 = require('select2');
window.LazyLoad = require('vanilla-lazyload');
window.datepicker = require('bootstrap-datepicker');
window.Swal = window.swal = require('sweetalert2');
window.accounting = require('accounting-js');

Alpine.start();
