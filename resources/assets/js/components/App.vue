<template>
    <div class="app" id="app">
        <div class="panel-body">
            <Github></Github>
            <Weibo></Weibo>
            <router-view></router-view>
            <router-link to="/sms">手机验证码登录</router-link>

        </div>
    </div>
</template>

<script>
    import Github from './Github.vue'
    import Weibo from  './Weibo'
    export default {
        name:'app',
        mounted() {
            console.log('app Component mounted')
        },
        data(){
            return{
                'title':'hello world'
            }
        },
        components:{
            'Github':Github,
            'Weibo':Weibo,
        }
    }
</script>
