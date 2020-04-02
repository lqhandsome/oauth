<style>

</style>

<template>
    <div id="Weibo">
        <!--<a v-bind:href="href"><img v-bind:src=weiboImg></a>-->
        <a href="https://api.weibo.com/oauth2/authorize?client_id=406317703&response_type=code&redirect_uri=http://oauth.lqlovehai.com/weibo/callback"><img v-bind:src=weiboImg></a>
    </div>
</template>

<script>
    export default {
        name:'Weibo',
        data(){
            return{
                weiboImg:'http://timg.sjs.sinajs.cn/t4/appstyle/widget/images/loginButton/loginButton_24.png',
                href:'https://api.weibo.com/oauth2/authorize?client_id=406317703&response_type=code&redirect_uri=http://oauth.lqlovehai.com/weibo/callback',
            }
        }
    }
</script>




