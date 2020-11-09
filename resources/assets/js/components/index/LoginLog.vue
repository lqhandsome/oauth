<template>
<div id="loginLog">
    <div id="log">
        <el-table
                element-loading-text="拼命加载中"
                element-loading-spinner="el-icon-loading"
                element-loading-background="rgba(0, 0, 0, 0.8)"
                :data="logs.data"
                border
                style="width: 40%">
            <el-table-column
                    prop="login_time"
                    label="登录日期"
                    width="180"
                    :formatter="formatter">
            </el-table-column>
            <el-table-column
                    prop="name"
                    label="登录名"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="type"
                    label="登录方式">
            </el-table-column>
            <el-table-column
                    prop="ip"
                    label="登录地址">
            </el-table-column>
        </el-table>
    </div>
    <div class="block">
        <el-pagination
                layout="total, sizes, prev, pager, next, jumper"
                :total="logs.total"
                :page-sizes="[5,10, 20, 50, 100]"
                :page-size="pageSize"
                @size-change="changePageSize"
                @current-change="currentPage"
        >
        </el-pagination>
    </div>
</div>

</template>
<script>
    import test from './test'
    const dateFormat = require('../../formDate.js');
    export default {
        name:'loginLog',
        data(){
            return{
                index:'11',
                logs:{},
                count:1,
                currentPage1:1,
                pageSize:10,
                loading:true,
            }
        },
        mounted() {
            var _this = this;
            axios.get('http://oauth.lqlovehai.com/loginlog?perPage='+this.pageSize).then(
                function (response) {
                    _this.logs = (response.data.logs);
                    _this.count = response.data.count
                }
            ).catch()
        },
        filters:{
            formDate:function(date) {
                var date = new Date(date * 1000);
                var YY = date.getFullYear() + '-';
                var MM = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
                var DD = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate());
                var hh = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
                var mm = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
                var ss = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
                return YY + MM + DD +" "+hh + mm + ss;
            }
        },
        methods:{
            formatter:function (row, column, cellValue, index) {
                var date = new Date(row.login_time * 1000);
                var YY = date.getFullYear() + '-';
                var MM = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
                var DD = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate());
                var hh = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
                var mm = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
                var ss = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
                return YY + MM + DD +" "+hh + mm + ss;
            },
            add:function () {
                this.count = this.count + 1
            },
            changePageSize(val) {
                console.log(val)
                this.pageSize = val
                var _this = this;
                axios.get('http://oauth.lqlovehai.com/loginlog?', {
                    params: {
                        perPage:this.pageSize,
                    }
                }).then(
                    function (response) {
                        console.log(response)
                        _this.logs = (response.data.logs);
                        _this.count = response.data.count
                    }
                ).catch()
            },
            currentPage(val) {
                var _this = this;
                _this.currentPage1 =
                axios.get('http://oauth.lqlovehai.com/loginlog?',{
                    params: {
                        perPage:this.pageSize,
                        page:this.currentPage1
                    }
                }).then(
                    function (response) {
                        console.log(response)
                        _this.logs = (response.data.logs);
                        _this.count = response.data.count
                    }
                ).catch()
            }
        },
        components:{
            'test':test,
        },
    }
</script>
<style>

</style>