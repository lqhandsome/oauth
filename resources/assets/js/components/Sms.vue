<template>
    <div class="app" id="SMS">
        <form action="">
            <div class="inputMobile" v-show="hidden">
                <input type="text" v-model="mobile"  >手机号码
                <span>{{validations.mobile.text}}</span>
            </div>

            <div id="getCode" v-show="hidden">
                <input type="button" value="获取验证码" v-on:click="action()" >
            </div>

            <div id="inputCode">
                <input type="text" v-if="!hidden" placeholder="请输入验证码 5分钟内有效！">
            </div>
            <div v-show="!hidden">
                <button >注册</button>
            </div>

            <p>没有注册会自动为您注册</p>
        </form>
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
                    }

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
            }
        }

    }
</script>
