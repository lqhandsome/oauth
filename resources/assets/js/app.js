
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
import App from './components/App'
import routes from './routes'
import VueResource from 'vue-resource'
Vue.use(VueResource)
const app = new Vue({
    el: '#app',
    router:routes,
    components: { App },
});
