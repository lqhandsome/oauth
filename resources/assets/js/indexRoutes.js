import VueRouter from 'vue-router';
import Vue from 'vue';
Vue.use(VueRouter);
//引入vue组件
import SMS from  './components/Sms';
import loginLog from './components/index/LoginLog'
export default new VueRouter({
    routes:[
        {
            path: '/loginlog',
            name:'loginLog',
            component:loginLog
        }
    ]
})