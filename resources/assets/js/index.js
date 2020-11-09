window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    console.log('token OK');
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

import Vue from 'vue'
import App from './components/index/App'
import indexRoutes from './indexRoutes'
import VueResource from 'vue-resource'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import { Button } from 'element-ui';

Vue.use(Button)
Vue.use(VueResource)
Vue.use(ElementUI);
const app = new Vue({
    el: '#app',
    router:indexRoutes,
    components: { App },
});

var node4 = {left: null, right: null, val: 4 };
var node5 = {left: null, right: null, val: 5 };
var node6 = {left: null, right: null, val: 6 };
var node7 = {left: null, right: null, val: 7 };
var node3 = {left: node6, right: node7, val: 3 };
var node2 = {left: node4, right: node5, val: 2 };
var node1 = {left: node2, right: node3, val: 1 };
