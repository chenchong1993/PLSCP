<!DOCTYPE html>
<html style="height: 100%">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>大众位置服务云平台</title>

    {{--引入jquery--}}
    <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
    <!-- 提示框开始 -->
    <link href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/notify/font-awesome.min.css" rel="stylesheet">
    <script src="/js/notify/bootstrap.min.js"></script>
    <script src="/js/notify/hullabaloo.js"></script>
    <!-- 提示框结束 -->
    <!-- 声音 -->
    <script type="text/javascript" src="/js/jquery.notify.js"></script>
    <!-- 声音 -->
    {{--引入地图依赖的库--}}
    <link rel="stylesheet" type="text/css" href="{{ asset('static/Ips_api_javascript/dijit/themes/tundra/tundra.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('static/Ips_api_javascript/esri/css/esri.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('static/Ips_api_javascript/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('static/Ips_api_javascript/Ips/css/widget.css') }}" />
    <script type="text/javascript" src="{{ asset('static/Ips_api_javascript/init.js') }}"></script>

    {{--修改三张地图尺寸--}}
    <style type="text/css">
        /*.user-msg{position:absolute;left:810px;top:10px;z-index:auto;width:500px;background-color:#f6f6f6}*/
        .map1-col{position:absolute;left:100px;top:10px;width:1200px;background-color:#f6f6f6}
        .map2-col{position:absolute;left:100px;top:350px;width:1200px;background-color:#f6f6f6}
        .map3-col{position:absolute;left:100px;top:740px;width:600px;background-color:#f6f6f6}
    </style>
    {{--拖动框--}}
    <link rel="stylesheet" type="text/css" href="/css/box.css">
    <script type="text/javascript" src="js/box.js"></script>
    {{--拖动框--}}
    <!-- 提示框开始 -->
    {{--<link href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">--}}
    {{--<link href="/css/notify/font-awesome.min.css" rel="stylesheet">--}}
    {{--<script src="/js/notify/bootstrap.min.js"></script>--}}
    {{--<script src="/js/notify/hullabaloo.js"></script>--}}
    <!-- 提示框结束 -->
</head>
<body>
<style>
    .menu-btn {
        position: fixed;top:30px;left:10px;font-size: 18px;
    }
    #showIndex{
        top:70px;
    }
    #showC7{
        top:30px;
    }
    #showClean{
        top:110px;
    }
    #showGetCoo{
        top:150px;
    }
    #selectUsername{
        top:190px;
    }
</style>
<style>
    html, body, #map1,map2,map3{
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
    }
</style>

{{--拖动框--}}
<div class="box">
    <div class="title">定位信息展示</div>
    <div class="con">
        <p>用户信息:<span id="user-msg"></span> </p>
        <p>用户坐标:<span id="current-location"></span> </p>
        <p>参考坐标:<span id="refer-location"></span> </p>
        <p>定位偏差:<span id="offset-location"></span> </p>
    </div>

</div>
{{--拖动框--}}

<div class="row">
    <div class="map1-col">
        <div id="map1"></div>
    </div>
    <div class="map2-col">
        <div id="map2"></div>
        <h2 class="menu-btn" style="left: 43%;font-size: 35px;color: #0c0c0c;top: 10px">331用户位置分布</h2>
            <button class="menu-btn" id="showC7" onclick=showC7()>切换到C7</button>
            <button class="menu-btn" id="showIndex" onclick=showIndex()>返回首页</button>
            <button class="menu-btn" id="showClean">清除轨迹</button>
            <button class="menu-btn" id="showGetCoo">获取坐标</button>
            <select class="menu-btn" id="selectUsername" onchange="selectUsername()">
                <option value="5">手机终端01</option>
                <option value="6">手机终端02</option>
                <option value="7">手机终端03</option>
                <option value="8">手机终端04</option>
                <option value="9">手机终端05</option>
            </select>');
    </div>
    <div class="map3-col">
        <div id="map3"></div>
    </div>
</div>
<script>
    /**
     * 定义全局变量
     **/
    var INTERVAL_TIME = 0.5; //数据刷新间隔时间
    var POINTSIZE = 18;    //默认图片大小为24*24
    var HELLO_STR = "系统初始化成功！"; //初始化欢迎语句
    var USER_ID = 5;//危险区域发送的信息

    // 提示框初始化
    $.hulla = new hullabaloo();
    //初始化声音
    $.notifySetup({sound: '/audio/notify.wav'});
    /**
     * [声音提醒和提示框显示]
     * @param  {[type]} text [提示信息]
     * @param  {[type]} type [提示类型]
     * @return {[type]}      [description]
     */
    function notify(text, type) {
        var level;
        var index_str;
        if (type == 'sys') {
            index_str = "[ 系统消息 ]<br/>";
            level = 'info';
        } else if (type == 'tips') {
            index_str = "[ 提示 ]<br/>";
            level = 'info';
        }

        $('<div/>').notify();
        setTimeout(function () {
            $.hulla.send(index_str + text, level);
        }, 100);
    }

    /**
     * 跳转到用户轨迹页面
     * */
    function catUserTrail(uid) {
        console.log("2:",uid+" ");
        var startTime = $('#startTime').val();
        var endTime = $('#endTime').val();
        window.location.href = '/userTrail?uid=' + uid + '&startTime=' + startTime +'&endTime=' + endTime;
    }
    function exportFlie(uid) {

        console.log("2:",uid+" ");
        var startTime = $('#startTime').val();
        var endTime = $('#endTime').val();

        window.location.href = '/api/fileExport?uid=' + uid + '&startTime=' + startTime +'&endTime=' + endTime;

    }
    function showC7() {
        window.location.href = '/normalMapC7';
    }
    function showIndex() {
        window.location.href = '/index';
    }

    function selectUsername(){
        var select = document.getElementById("selectUsername");
        USER_ID = select.value
        // alert(USER_ID);
    }

    /**
     * 拖动框
     */
    $(document).ready(function () {
        $(".box").bg_move({
            move: '.title',
            closed: '.close',
            size: 6
        });
    });

    /**
     * 地图需求文件
     */
    require([
        "Ips/map",
        "Ips/widget/IpsMeasure",
        "Ips/layers/DynamicMapServiceLayer",
        "Ips/layers/FeatureLayer",
        "Ips/layers/GraphicsLayer",
        "esri/graphic",
        "esri/geometry/Point",
        "esri/geometry/Polyline",
        "esri/geometry/Polygon",
        "esri/InfoTemplate",
        "esri/symbols/SimpleMarkerSymbol",
        "esri/symbols/SimpleLineSymbol",
        "esri/symbols/SimpleFillSymbol",
        "esri/symbols/PictureMarkerSymbol",
        "esri/symbols/TextSymbol",
        "dojo/colors",
        "dojo/on",
        "dojo/dom",
        "dojo/domReady!"
    ], function (Map, IpsMeasure,DynamicMapServiceLayer,FeatureLayer, GraphicsLayer, Graphic, Point, Polyline, Polygon, InfoTemplate, SimpleMarkerSymbol, SimpleLineSymbol,
                 SimpleFillSymbol, PictureMarkerSymbol, TextSymbol, Color, on, dom) {


        /**
         * 定义三张地图，并设定必要参数
         */
        var map1 = new Map("map1", {
            logo:false,
            center: [114.3489254,38.24772],
        });
        var map2 = new Map("map2", {
            logo:false,
            center: [114.3489254,38.24777],
        });
        var map3 = new Map("map3", {
            logo:false,
            center: [114.3486414,38.247770],
        });
        /**
         * 初始化楼层平面图
         */
        var f1 = new DynamicMapServiceLayer("http://121.28.103.199:5567/arcgis/rest/services/331/floorone/MapServer");
        var f2 = new DynamicMapServiceLayer("http://121.28.103.199:5567/arcgis/rest/services/331/floortwo/MapServer");
        var f3 = new DynamicMapServiceLayer("http://121.28.103.199:5567/arcgis/rest/services/331/floorthree/MapServer");
        map1.addLayer(f1);
        map2.addLayer(f2);
        map3.addLayer(f3);
        /**
         * 定义点图层
         */
        var pointLayerF1 = new GraphicsLayer();
        var pointLayerF2 = new GraphicsLayer();
        var pointLayerF3= new GraphicsLayer();

        /**
         * 更新位置信息小部件
         */
        function updateLocationBox(username,curr_lng,curr_lat,refer_lng,refer_lat,floor,location_method){
            if (location_method==0){location_method='指纹定位'}
            if (location_method==1){location_method='混合定位'}
            if (location_method==2){location_method='视觉定位'}
            $('#user-msg').html(username+", "+floor+"楼, "+location_method);
            $('#current-location').html("("+curr_lat+","+curr_lng+")");
            $('#refer-location').html("("+refer_lat+","+refer_lng+")");
            $('#offset-location').html("("+(curr_lat-refer_lat)/0.00001141+","+(curr_lng-refer_lng)/0.00000899+")m");
        }



        on(dom.byId("showClean"),"click",function(){
            pointLayerF1.clear();
            pointLayerF2.clear();
            pointLayerF3.clear();
        })

        /**
         * 添加点图标
         * */
        function addUserPoint(id,uid, lng, lat, name, phone,floor,status) {
            //定义点的几何体
            //38.2477770 114.3489115
            var picpoint = new Point(lng,lat);
            // //定义点的图片符号
            var picSymbol;
            var img_uri;
            switch (status) {
                case 5:
                    img_uri = "{{ asset('static/Ips_api_javascript/Ips/image/marker0.png') }}";
                    break;
                case 6:
                    img_uri = "{{ asset('static/Ips_api_javascript/Ips/image/marker1.png') }}";
                    break;
                case 7:
                    img_uri = "{{ asset('static/Ips_api_javascript/Ips/image/marker2.png') }}";
                    break;
                case 8:
                    img_uri = "{{ asset('static/Ips_api_javascript/Ips/image/marker3.png') }}";
                    break;
                case 9:
                    img_uri = "{{ asset('static/Ips_api_javascript/Ips/image/marker4.png') }}";
                    break;
            }

            picSymbol = new PictureMarkerSymbol(img_uri,POINTSIZE,POINTSIZE);
            //定义点的图片符号
            var attr = {"name": name, "phone": phone};
            //信息模板
            var infoTemplate = new InfoTemplate();
            infoTemplate.setTitle('用户');
            infoTemplate.setContent(
                "<b>名称:</b><span>${name}</span><br>"
                + "<b>手机号:</b><span>${phone}</span><br>"
                + "<b>起始时间：</b><input type='text' name='startTime'class='' id='startTime' placeholder='2018-01-01 00:00:00'><br>"
                + "<b>终止时间：</b><input type='text' name='endTime'class='' id='endTime' placeholder='2018-01-01 23:59:59'><br>"
                + "<button class='' onclick=catUserTrail(" + "'" + uid + "'" + ") > 查看该用户轨迹</button>"
                + "<button class='' onclick=exportFlie(" + "'" + uid + "'" + ") > 导出该时段数据</button>"
            );
            var picgr = new Graphic(picpoint, picSymbol, attr, infoTemplate);
            if (floor == 1){
                pointLayerF1.add(picgr);
                map1.addLayer(pointLayerF1);
            }
            if (floor == 2){
                pointLayerF2.add(picgr);
                map2.addLayer(pointLayerF2);
            }
            if (floor == 3){
                pointLayerF3.add(picgr);
                map3.addLayer(pointLayerF3);
            }
        }
        /**
         * 从数据库读取用户列表和用户最新坐标并更新界面
         */
        function getDataAndRefresh() {
            // 从云端读取数据
            $.get("/api/apiGetAllUserNewLocationList",
                {},
                function (dat, status) {
                    if (dat.status == 0) {
                        // 删除数据
                        // pointLayerF1.clear();
                        // pointLayerF2.clear();
                        // pointLayerF3.clear();
                        //重绘
                        pointLayerF1.redraw();
                        pointLayerF2.redraw();
                        pointLayerF3.redraw();
                        // 添加人
                        //注销掉因为先单用户测试
                        // for (var i in dat.data) {
                        console.log(dat);
                        for (var i=5; i<10; i++) {
                            console.log(dat.data[i].username);
                            if (dat.data[i].location.floor==3){
                                if ((38.24766<dat.data[i].location.lat)&&(dat.data[i].location.lat<38.2478) &&(114.3485<dat.data[i].location.lng)&&(dat.data[i].location.lng<114.34871))
                                {
                                    addUserPoint(
                                        dat.data[i].id,
                                        dat.data[i].uid,
                                        dat.data[i].location.lng,
                                        dat.data[i].location.lat,
                                        dat.data[i].username,
                                        dat.data[i].tel_number,
                                        dat.data[i].location.floor,
                                        i
                                    );
                                }
                            }
                            else {
                                if ((38.24766<dat.data[i].location.lat)&&(dat.data[i].location.lat<38.2478) &&(114.3485<dat.data[i].location.lng)&&(dat.data[i].location.lng<114.349238))
                                {
                                    addUserPoint(
                                        dat.data[i].id,
                                        dat.data[i].uid,
                                        dat.data[i].location.lng,
                                        dat.data[i].location.lat,
                                        dat.data[i].username,
                                        dat.data[i].tel_number,
                                        dat.data[i].location.floor,
                                        i
                                    );
                                }
                                if ((38.24773539<dat.data[i].location.lat)&&(dat.data[i].location.lat<38.24774129) &&(114.34871745<dat.data[i].location.lng)&&(dat.data[i].location.lng<114.34873097))
                                {
                                    notify("欢迎来到203&205房间", "tips");
                                }
                                if ((38.24773258<dat.data[i].location.lat)&&(dat.data[i].location.lat<38.24773957) &&(114.34887475<dat.data[i].location.lng)&&(dat.data[i].location.lng<114.34889554))
                                {
                                    notify("欢迎来到207房间", "tips");
                                }
                            }
                        }
                        updateLocationBox(
                            dat.data[USER_ID].username,
                            dat.data[USER_ID].location.lng,
                            dat.data[USER_ID].location.lat,
                            0,
                            0,
                            dat.data[USER_ID].location.floor,
                            dat.data[USER_ID].location.location_method
                        );
                    } else {
                        console.log('ajax error!');
                    }
                }
            );
        }
        /**
         * 刷新频率
         */
        setInterval(getDataAndRefresh, (INTERVAL_TIME * 1000));
        //显示初始化成功
        notify(HELLO_STR, "sys");
    });
</script>

</body>
</html>