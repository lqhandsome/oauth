import VueRouter from 'vue-router';
import Vue from 'vue';
Vue.use(VueRouter);
//引入vue组件
import SMS from  './components/Sms';
export default new VueRouter({
    routes:[
        {
            path: '/sms',
            name:'SMS',
            component:SMS
        }
    ]
})