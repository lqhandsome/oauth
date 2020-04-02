<template>
    <div class="app" id="app">
        <div class="panel-body">
            {{title}}
            <router-link to="/sms">sms</router-link>
            <Github></Github>
            <Weibo></Weibo>
            <router-view></router-view>

        </div>
    </div>
</template>

<script>
    import Github from './Github.vue'
    import Weibo from  './Weibo'
    export default {
        name:'app',
        mounted() {
            console.log('Component mounted.')
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
