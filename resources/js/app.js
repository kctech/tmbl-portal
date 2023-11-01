
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/*import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routes';*/

/*fontawesome*/
import { library, dom } from '@fortawesome/fontawesome-svg-core';
import { far } from '@fortawesome/pro-regular-svg-icons';
import { fas } from '@fortawesome/pro-solid-svg-icons';
import { fal } from '@fortawesome/pro-light-svg-icons';
import { faFacebookF } from '@fortawesome/free-brands-svg-icons';
/*import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'*/
library.add(far, fas, fal, faFacebookF);
/* Kicks off the process of finding <i> tags and replacing with <svg>*/
dom.watch();

/*window.$ = window.jQuery = require('jquery');*/
window.Select2 = window.select2 = require('select2');
window.LazyLoad = require('vanilla-lazyload');
window.datepicker = require('bootstrap-datepicker');
window.Swal = window.swal = require('sweetalert2');
window.accounting = require('accounting-js');

/*window.Vue = require('vue');
window.VueRouter = require('vue-router');
Vue.use(VueRouter);*/

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

/*const app = new Vue({
   el: '#app',
    router: new VueRouter(routes)
});*/
