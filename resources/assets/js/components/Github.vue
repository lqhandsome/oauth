<style>

</style>

<template>
    <div>

        <a v-bind:href=GithubLogin><img v-bind:src=GithubImg alt="使用github登录" height="60px" width="60px"></a>
        <p>github登录</p>
    </div>
</template>

<script>
    export default {
    name:'Github',
        data(){
            return {
                GithubImg:'http://oauth.lqlovehai.com/image/github.jpg',
                GithubLogin:'https://github.com/login/oauth/authorize?client_id=e0dcea40a90ff58b7ce3'
            }
        },
        methods:{
        beforeS(){
            alert(2)
        }
        }

    }
</script>




