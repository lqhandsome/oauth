<template>
    <div class="app" id="app">
        <el-row :gutter="0">
            <el-col :span="1" ><div class="grid-content bg-purple"><router-link  to="/"><el-link type="primary">回首页</el-link></router-link></div></el-col>
            <el-col :span="2" ><div class="grid-content bg-purple">{{name}}</div></el-col>
            <el-col :span="2" ><div class="grid-content bg-purple-light">{{title}}</div></el-col>
            <el-col :span="3" ><div class="grid-content bg-purple">{{date | formData}}</div></el-col>
            <el-col :span="2" ><div class="grid-content bg-purple"> <router-link type="primary"  to="/loginlog"><el-link type="success">登录日志</el-link></router-link></div></el-col>
            <el-col :span="1" ><div class="grid-content bg-purple-light"><el-link type="warning" href="/outlogin">退出登录</el-link></div></el-col>
        </el-row>
        <router-view></router-view>
    </div>
</template>

<script>
    // import loginLog from './LoginLog'
    export default {
        name:'app',
        mounted() {
            var _this=this;
               axios.get('http://oauth.lqlovehai.com//userinfo')
                .then(
                    function (response) {
                        if(response.data.success == true) {
                            _this.name = response.data.userName
                        }
                    }
                ).catch(function (error) { // 请求失败处理
                    _this.name = '未设置'
                });
            this.timer = setInterval(() => {
                _this.date = new Date(); // 修改数据date
            }, 1000);
        },
        data(){
            return{
                'title':'欢迎进入首页',
                'name':'',
                'date': new Date(),
                hu:'胡海品请把鼠标移入',
                dialogVisible:false

            }
        },
        components:{
        },
        methods:{
          hus:function (message,event) {
                this.hu = message
              this.dialogVisible =true
              console.log(event)
          }
        },
        filters:{
            //格式化时间
            formData:function (date) {
                var date = new Date(date);
                var YY = date.getFullYear() + '-';
                var MM = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
                var DD = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate());
                var hh = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
                var mm = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
                var ss = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
                return YY + MM + DD +" "+hh + mm + ss;
            }
        },
    }
</script>
<style scoped>
#hu{
    width: 200px;
    height: 200px;
    border: 1px solid #056;
    text-align: center;
}

</style>
