<style>

</style>

<template>
    <div>
        <p>个人信息</p>
    </div>
</template>

<script>
    export default {
        name:'UserInfo',
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




