<template>
    <div class="app" id="SMS">
        <form action="" v-show="!aaa">
            <div class="inputMobile" v-show="hidden">
                <label for="mobile" >手机号码:</label>
                <input type="text" v-model="mobile"  id="mobile">
                <span>{{validations.mobile.text}}</span>
            </div>

            <div id="getCode" v-show="hidden">
                <input type="button" value="获取验证码" v-on:click="action()" >
            </div>

            <div id="inputCode">
                <input type="text" v-model="code" v-if="!hidden" placeholder="请输入验证码 5分钟内有效！">
            </div>
            <div v-show="!hidden">
                <button v-on:click="login()">登录</button>
                <p>没有注册会自动为您注册</p>
            </div>
        </form>
        <div v-show="aaa">
            <a href="/">进入首页</a>
        </div>

    </div>
</template>

<script>

    export default {
        name:'SMS',
        mounted() {
            console.log('Component mounted.')
        },
        data(){
            return{
                'title':'hello world',
                'hidden':true,
                'mobile':'',
                'code':'',
                'aaa':'',
                validations:{
                    mobile:{
                        is_valid: true,
                        text:''
                    }
                }
            }
        },
        methods:{
            action:function () {
                    if(this.vailMobile()) {
                        this.hidden = false;
                        axios
                            .get('http://oauth.lqlovehai.com/mobile?mobile='+this.mobile)
                            .then(
                            )
                            .catch(function (error) { // 请求失败处理
                                console.log(error);
                            });
                    }
            },
            login:function () {
                    axios.get('http://oauth.lqlovehai.com/codeLogin?code='+this.code+'&mobile='+this.mobile).then(
                    response=>(this.aaa = response.data.code =='success' ? true:false ,console.log(1))
                        ).catch(function (error) {
                        console.log(error);
                    })
            },
            vailMobile:function () {
                let validFormMobile = true;
                // 验证手机号字段不为空且格式正确
                if( this.mobile.trim() === '' || !this.mobile.match(/^1[3456789]\d{9}$/) ){
                    validFormMobile = false;
                    this.validations.mobile.is_valid = false;
                    this.validations.mobile.text = '请输入正确的手机号码!';
                }else{
                    this.validations.mobile.is_valid = true;
                    this.validations.mobile.text = '';
                }
                return validFormMobile;
            }
        }

    }
</script>
