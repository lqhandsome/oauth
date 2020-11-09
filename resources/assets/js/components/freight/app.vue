<template>
    <div class="page">
        <div id="form">
        <form class="form-inline">
            <h5>请选择发货地址</h5>
            <select v-model="stateStoreAddress" class="form-control" id="select">
                <option value="manual">手动输入</option>
                <option v-bind:value="storeAd.address" v-for=" storeAd in storeAddress "> {{storeAd.firstAdress}}</option>
            </select>
            <div v-show="!isManual">
                <h5>请选择收货地址</h5>
                <select v-model="stateUserAddress" class="form-control" id="select">
                    <option v-bind:value="userAd | formUserAd" v-for=" userAd in userAddress.list "> {{userAd | formUserAd}}</option>
                </select>
            </div>

            <div class="grid-container" v-show="isManual">
                <h5>发货地址</h5>
                <div class="grid-x grid-padding-x">
                    <div class="large-1 medium-12 small-12 cell form-group">
                        <label>State
                            <input type="text" placeholder="省份" v-model="state">
                        </label>
                        <span class="validation" v-show="!validations.state.is_valid">{{ validations.state.text }}</span>
                    </div>
                    <div class="large-2 medium-12 small-12 cell form-group">
                        <label>City
                            <input type="text" placeholder="城市" v-model="city">
                        </label>
                        <span class="validation" v-show="!validations.city.is_valid">{{ validations.city.text }}</span>
                    </div>

                    <div class="large-3 medium-12 small-12 cell form-group">
                        <label>Address
                            <input type="text" placeholder="地址" v-model="address">
                        </label>
                        <span class="validation" v-show="!validations.address.is_valid">{{ validations.address.text }}</span>
                    </div>

                </div>
            </div>
            <div class="grid-container"  v-show="isManual">
                <h5>收货地址</h5>
                <div class="grid-x grid-padding-x">
                    <div class="large-1 medium-12 small-12 cell form-group">
                        <label>State
                            <input type="text" placeholder="省份" v-model="stateTwo">
                        </label>
                        <span class="validation" v-show="!validations.stateTwo.is_valid">{{ validations.stateTwo.text }}</span>
                    </div>
                    <div class="large-2 medium-12 small-12 cell form-group">
                        <label>City
                            <input type="text" placeholder="城市" v-model="cityTwo">
                        </label>
                        <span class="validation" v-show="!validations.cityTwo.is_valid">{{ validations.cityTwo.text }}</span>
                    </div>

                    <div class="large-3 medium-12 small-12 cell form-group">
                        <label>Address
                            <input type="text" placeholder="地址" v-model="addressTwo">
                        </label>
                        <span class="validation" v-show="!validations.addressTwo.is_valid">{{ validations.addressTwo.text }}</span>
                    </div>
                </div>
            </div>
            <div id="other" >
                <div class="grid-container" v-for="(item , i) in sites">
                    <p>途经点{{i+1}}</p>
                    <div>
                        <select v-model="autoSites[i]" class="form-control" id="select">
                            <option value="manual">手动输入</option>
                            <option v-bind:value="storeAd.address" v-for=" storeAd in storeAddress "> {{storeAd.firstAdress}}</option>
                        </select>
                    </div>
                    <div class="grid-x grid-padding-x" v-show= "selectSite(autoSites[i],i)" >
                        <div class="large-1 medium-12 small-12 cell form-group">
                            <label>State
                                <input type="text" placeholder="省份" v-model="siteState[i]">
                            </label>
                            <span class="validation" v-show="!validations.stateTwo.is_valid">{{  }}</span>
                        </div>
                        <div class="large-2 medium-12 small-12 cell form-group">
                            <label>City
                                <input type="text" placeholder="城市" v-model="siteCity[i]">
                            </label>
                            <span class="validation" v-show="!validations.cityTwo.is_valid">{{  }}</span>
                        </div>

                        <div class="large-3 medium-12 small-12 cell form-group">
                            <label>Address
                                <input type="text" placeholder="地址" v-model="siteAddress[i]">
                            </label>
                            <span class="validation" v-show="!validations.addressTwo.is_valid">{{  }}</span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="large-12 medium-12 small-12 cell">
                <a v-on:click="submitNewCafe()" href="javascript:void 0;" class="btn  btn-success">提交</a>
            </div>
            <a href="javascript:void 0;" @click="addForm" class="btn btn-info">增加一个途经点</a>
            <a href="javascript:void 0;" @click="deleteForm" class="btn btn-danger">删除一个途经点</a>
            <br/>
            <h3 style="color: #056">{{distanceCount}}</h3>
        </form>
        </div>
        <div id="route-plan"></div>
        <div id="test">
        </div>
    </div>


</template>

<script>
    // import Navigation from "../components/global/Navigation";
    export default {
        components:{
            // Navigation
        },
        data() {
            return {
                sites:[],//控制途经点个数
                address: '',
                city: '',
                state: '',
                addressTwo: '',
                cityTwo: '',
                stateTwo: '',
                siteState:[],
                siteCity:[],
                siteAddress:[],
                waypoints : [],
                distanceCount:'',
                storeAddress:[],
                userAddress:{},
                autoSites:[],
                isManual:false,//是否是手动添加
                siteIsManual:false,//途经点是否手动添加
                store:'',
                stateStoreAddress:'',
                stateUserAddress:'',
                validations:{
                    address: {
                        is_valid: true,
                        text: ''
                    },
                    city: {
                        is_valid: true,
                        text: ''
                    },
                    state: {
                        is_valid: true,
                        text: ''
                    },
                    addressTwo: {
                        is_valid: true,
                        text: ''
                    },
                    cityTwo: {
                        is_valid: true,
                        text: ''
                    },
                    stateTwo: {
                        is_valid: true,
                        text: ''
                    },
                },
            }
        },
        methods:{
            submitNewCafe:function () {
                if (this.validateNewCafe()) {
                    //逻辑代码
                    var _this = this;
                    axios.get('/responseFreight', {
                        params: {
                            address: this.address ? this.address: '',//发货地址
                            city: this.city ? this.city: '',
                            state: this.state ? this.state: this.stateStoreAddress,//是否select的数据
                            addressTwo: this.addressTwo ? this.addressTwo: '',//收货地址
                            cityTwo: this.cityTwo ? this.cityTwo: '',
                            stateTwo: this.stateTwo ? this.stateTwo: this.stateUserAddress,//是否select的数据
                            siteState: this.siteState,
                            siteCity: this.siteCity,
                            siteAddress: this.siteAddress,
                        }
                    }).then(function (response) {
                        if(response.data.success) {
                            _this.waypoints = response.data.sites
                            _this.showMap(response.data.origin,response.data.destination);
                           _this.distanceCount = '距离是'+ response.data.distance/1000 +'公里';
                        } else {
                            alert(response.data.message)
                        }
                        }).catch(function (error) {
                            console.log(error);
                        });
                }
            },
            validateNewCafe: function () {
                let validNewCafeForm = true;
                //判断是手动输入还是select的方式
                if(this.isManual == true) {
                    // 确保 address 字段不为空
                    if( this.address.trim() === '' ){
                        validNewCafeForm = false;
                        this.validations.address.is_valid = false;
                        this.validations.address.text = '请输入发货地址!';
                    }else{
                        this.validations.address.is_valid = true;
                        this.validations.address.text = '';
                    }
                    // 确保 address 字段不为空
                    if( this.addressTwo.trim() === '' ){
                        validNewCafeForm = false;
                        this.validations.addressTwo.is_valid = false;
                        this.validations.addressTwo.text = '请输入收货地址!';
                    }else{
                        this.validations.addressTwo.is_valid = true;
                        this.validations.addressTwo.text = '';
                    }

                    //  确保 city 字段不为空
                    if( this.cityTwo.trim() === '' ){
                        validNewCafeForm = false;
                        this.validations.cityTwo.is_valid = false;
                        this.validations.cityTwo.text = '请输入城市!';
                    }else{
                        this.validations.cityTwo.is_valid = true;
                        this.validations.cityTwo.text = '';
                    }
                    //  确保 city 字段不为空
                    if( this.city.trim() === '' ){
                        validNewCafeForm = false;
                        this.validations.city.is_valid = false;
                        this.validations.city.text = '请输入城市!';
                    }else{
                        this.validations.city.is_valid = true;
                        this.validations.city.text = '';
                    }


                    //  确保 state 字段不为空
                    if( this.state.trim() === '' ){
                        validNewCafeForm = false;
                        this.validations.state.is_valid = false;
                        this.validations.state.text = '请输入发货省份!';
                    }else{
                        this.validations.state.is_valid = true;
                        this.validations.state.text = '';
                    }


                    //  确保 state 字段不为空
                    if( this.stateTwo.trim() === '' ){
                        validNewCafeForm = false;
                        this.validations.stateTwo.is_valid = false;
                        this.validations.stateTwo.text = '请输入收货省份!';
                    }else{
                        this.validations.stateTwo.is_valid = true;
                        this.validations.stateTwo.text = '';
                    }
                }

                return validNewCafeForm;
            },
            drawRoute:function (route) {
                var paths = this.parseRouteToPath(route)

                var startMarker = new AMap.Marker({
                    position: paths[0],
                    icon: 'https://webapi.amap.com/theme/v1.3/markers/n/start.png',
                    map: this.map
                })

                var endMarker = new AMap.Marker({
                    position: paths[paths.length - 1],
                    icon: 'https://webapi.amap.com/theme/v1.3/markers/n/end.png',
                    map: this.map
                })

                var routeLine = new AMap.Polyline({
                    path: paths,
                    isOutline: true,
                    outlineColor: '#ffeeee',
                    borderWeight: 2,
                    strokeWeight: 5,
                    strokeColor: '#0091ff',
                    lineJoin: 'round'
                })

                routeLine.setMap(this.map)

                // 调整视野达到最佳显示区域
                this.map.setFitView([ startMarker, endMarker, routeLine ])
            },
            parseRouteToPath:function (route) {
                let path = []

                for (var i = 0, l = route.steps.length; i < l; i++) {
                    var step = route.steps[i]

                    for (var j = 0, n = step.path.length; j < n; j++) {
                        path.push(step.path[j])
                    }
                }

                return path
            },
            showMap:function ($origin,$destination) {
                this.map = new AMap.Map("route-plan", {
                    center: [116.397559, 39.89621],
                    zoom: 8,
                });

                this.truckDriving = new AMap.TruckDriving({
                    policy: 10, // 规划策略
                    size: 1, // 车型大小
                    width: 2.5, // 宽度
                    height: 2, // 高度
                    load: 1, // 载重
                    weight: 12, // 自重
                    axlesNum: 2, // 轴数
                    province: '京', // 车辆牌照省份
                });
                this.path = [];
                var markers = [];
                // 创建 AMap.Icon 实例：
                var icon = new AMap.Icon({
                    size: new AMap.Size(50, 60),    // 图标尺寸
                    image: 'http://a.amap.com/jsapi_demos/static/demo-center/icons/dir-via-marker.png',  // Icon的图像
                    imageOffset: new AMap.Pixel(0, 0),  // 图像相对展示区域的偏移量，适于雪碧图等
                });
                this.path.push({lnglat:[$origin[0],$origin[1]]});//起点
                 // this.path.push({lnglat:[116.321354, 39.896436]});//途径
                for (var i = 0;i < this.waypoints.length; i++){
                        this.path.push({lnglat:[this.waypoints[i].lat,this.waypoints[i].lng]});
                        var marker = new AMap.Marker({
                            position: new AMap.LngLat(this.waypoints[i].lat,this.waypoints[i].lng),
                            // offset: new AMap.Pixel(-23, -45),
                            // icon: icon,
                            icon: 'https://webapi.amap.com/theme/v1.3/markers/n/mark_bs.png',
                            title: '途经点' + (i +1),
                        })
                    markers.push(marker)
                }
                this.map.add(markers)
                this.path.push({lnglat:[$destination[0],$destination[1]]});//终点
                var _this =this
                this.truckDriving.search(_this.path, function(status, result) {
                    if (status === 'complete') {
                        log.success('绘制货车路线完成')
                        if (result.routes && result.routes.length) {
                            _this.drawRoute(result.routes[0])
                        }
                    } else {
                        log.error('获取货车规划数据失败：' + result)
                    }
                })
            },
            //增加一个途经点
            addForm:function () {
                    this.sites.push(1)
            },
            deleteForm:function () {
                    this.siteState.pop();
                    this.siteCity.pop();
                    this.siteAddress.pop();
                    this.sites.pop()
            },
            //清空手动输入时的自动填充
            clearSite:function (key) {
                    if(this.autoSites[key] == 'manual') {
                        this.siteState[key] = '';
                        this.siteAddress[key] = '';
                        this.siteCity[key] = '';
                    }
            }
        },
        mounted() {
                var _this = this
                        axios.get('http://oauth.lqlovehai.com/getAddress').then(function (response) {
                        if(response.data.success ) {
                            _this.storeAddress = response.data.data.storeAddress
                            _this.userAddress = response.data.data.userAddress
                        }
                }).catch(function (error) {
                                    console.log(error)
                })
        },
        watch: {
            stateStoreAddress: function (newStore, oldStore) {

                if (newStore == 'manual') {
                    this.isManual = true
                } else {
                    //当选择的有地址时清空发货地址和收货地址
                    this.isManual = false;
                    this.address = '';
                    this.city = '';
                    this.state = '';
                    this.addressTwo = '';
                    this.cityTwo = '';
                    this.stateTwo = '';
                }
            },
            autoSites: function (newValue, oldValue) {
                if (newValue == 'manual') {
                    this.siteIsManual = true
                     }
                deep:true;
                },

        },
        filters:{
            formUserAd:function (value) {
                if(!value) {
                    return '';
                }
                let tmpArea = value.area.match(/(\S*):/)[1];
                return  tmpArea + value.addr;
            }
        },
        computed:{
            // selectSite () {
            //     return function (value) {
            //         if(value == 'manual'){
            //             return true;
            //         }
            //         return false;
            //     }
            // },
            selectSite:function () {
                    return function (value,key) {
                        if(value == 'manual'){
                            return true;
                        } else{
                            this.clearSite(key);
                            this.siteState[key] = this.autoSites[key];
                            this.siteAddress[key] = this.autoSites[key];
                            this.siteCity[key] = this.autoSites[key];
                        }

                        return false;
                    }
            }

        }
    }
</script>

<style scoped>
.validation{
    color: #5e5e5e;
}
#route-plan {
    width: 66%;
    height:800px;
    float:left
}
    #form{
        width: 30%;
        float:left;
        margin-left: 10px;
    }
    .amap-copyright{
        display:none;
        visibility:hidden;
    }
    .large-1 {
        width: 100px;
    }
    .large-2{
        width: 80px;
    }
 a {
     display: block;
     float: left;
     margin-right: 10px;
 }
  #select{
      width: 70%;
  }
</style>
