<!DOCTYPE html>
<html style="height: 100%">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>大众位置服务云平台</title>

    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('static/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="{{ asset('static/vendor/metisMenu/metisMenu.min.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('static/dist/css/sb-admin-2.css') }}" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="{{ asset('static/vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
<!--    {{--HUI的图标库--}}-->
    <link rel="stylesheet" type="text/css" href="{{ asset('static/Hui-iconfont/1.0.8/iconfont.css') }}" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="{{ asset('static/Ips_api_javascript/dijit/themes/tundra/tundra.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('static/Ips_api_javascript/esri/css/esri.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('static/Ips_api_javascript/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('static/Ips_api_javascript/Ips/css/widget.css') }}" />
    <script type="text/javascript" src="{{ asset('static/Ips_api_javascript/init.js') }}"></script>
    <script src="{{ asset('static/vendor/jquery/jquery.min.js') }}"></script>
    <style type="text/css">
        /*.user-msg{position:absolute;left:810px;top:10px;z-index:auto;width:500px;background-color:#f6f6f6}*/
        .map-col{position:absolute;left:10px;top:10px;z-index:0;width:1200px;height:800px;background-color:#f6f6f6}
    </style>
</head>
<body>
<style>
    .menu-btn {
        position: fixed;top:30px;left:1050px;font-size: 18px;
    }
    #showIndex{
        top:70px;
    }
    #showC7{
        top:30px;
    }
</style>
<style>
    html, body, #map{
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
    }
</style>
<div class="row">
    <div class="map-col">
        <div id="map"></div>
        <h2 class="menu-btn" style="left: 43%;font-size: 35px;color: #0c0c0c;top: 10px">C7用户实时轨迹</h2>
        <button class="menu-btn" id="showIndex" onclick=showIndex()>返回首页</button>
        <button class="menu-btn" id="showC7" onclick=showC7()>查看C7用户分布</button>

    </div>
</div>
<script>
    /**
     * 定义全局变量
     **/
    var INTERVAL_TIME = 0.5; //数据刷新间隔时间
    var POINTSIZE = 16;    //默认图片大小为24*24
    /**
     * 跳转到用户轨迹页面
     * */
    function catUserTrail(uid) {
        console.log("2:",uid+" ");
        var startTime = $('#startTime').val();
        var endTime = $('#endTime').val();
        window.location.href = '/userTrailC7?uid=' + uid + '&startTime=' + startTime +'&endTime=' + endTime;
    }
    function showIndex() {
        window.location.href = '/index';
    }
    function showC7() {
        window.location.href = '/normalMapC7';
    }
    /**
     * 地图需求文件
     */
    require([
        "esri/map",
        "esri/layers/ArcGISDynamicMapServiceLayer",
        "esri/layers/GraphicsLayer",
        "esri/graphic",
        "esri/SpatialReference",
        "esri/InfoTemplate",
        "esri/geometry/Point",
        "esri/symbols/PictureMarkerSymbol",
        "esri/symbols/SimpleMarkerSymbol",
        "esri/symbols/SimpleLineSymbol",
        "dojo/colors",
        "dojo/on",
        "dojo/domReady!"
    ], function (Map, ArcGISDynamicMapServiceLayer,GraphicsLayer,Graphic,SpatialReference,InfoTemplate,Point,PictureMarkerSymbol,
                 SimpleMarkerSymbol,SimpleLineSymbol,Color,on) {
        /**
         * 定义三张地图，并设定必要参数
         */
        map = new Map("map", {
            center: new Point(538260.1180806961,4212780.5132764, new SpatialReference({ wkid: 4509})),
            logo:false

        });
        /**
         * 初始化楼层平面图
         */
        var C7 = new ArcGISDynamicMapServiceLayer("http://121.28.103.199:5567/arcgis/rest/services/C7/C7/MapServer");
        map.addLayer(C7);
        /**
         * 定义点图层
         */
        var pointLayerC7 = new GraphicsLayer();
        /**
         * 添加点图标
         * */
        /**
         var point=new Point(538265.1569497379,4212838.030891536,new SpatialReference({ wkid: 4509}));
         var symbol= new PictureMarkerSymbol("{{ asset('static/Ips_api_javascript/Ips/image/marker1.png') }}",24,24);
         var infoTemplate = new InfoTemplate();
         infoTemplate.setTitle("测试点");
         infoTemplate.setContent("<b>名称:</b><span>number1</span><br>");
         var graphic= new Graphic(point,symbol,null,infoTemplate);
         pointLayerC7.add(graphic);
         map.addLayer(pointLayerC7);
         */

        function addUserPoint(id,uid, lng, lat, name, phone,floor,status) {
            //定义点的几何体
            //38.2477770 114.3489115
            console.log(lat);
            console.log(lng);
            console.log(status);
            var picpoint = new Point(lng,lat, new SpatialReference({ wkid: 4509}));
            // //定义点的图片符号
            var img_uri;
            switch (status) {
                case 0:
                    img_uri = "{{ asset('static/Ips_api_javascript/Ips/image/marker0.png') }}";
                    break;
                case 1:
                    img_uri = "{{ asset('static/Ips_api_javascript/Ips/image/marker1.png') }}";
                    break;
                case 2:
                    img_uri = "{{ asset('static/Ips_api_javascript/Ips/image/marker2.png') }}";
                    break;
                case 3:
                    img_uri = "{{ asset('static/Ips_api_javascript/Ips/image/marker3.png') }}";
                    break;
                case 4:
                    img_uri = "{{ asset('static/Ips_api_javascript/Ips/image/marker4.png') }}";
                    break;
            }

            var picSymbol = new PictureMarkerSymbol(img_uri,24,24);
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
                + "<button class='' onclick=catUserTrail(" + "'" + uid + "'" + ") > 查看该用户历史轨迹</button>"
            );
            var picgr = new Graphic(picpoint, picSymbol, attr, infoTemplate);
            pointLayerC7.add(picgr);
            map.addLayer(pointLayerC7);
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
                        pointLayerC7.redraw();
                        // 添加人
                        //注销掉因为先单用户测试
                        // for (var i in dat.data) {
                        for (var i=0; i<5; i++) {
                            // console.log(dat.data[i]);
                            addUserPoint(
                                dat.data[i].id,
                                dat.data[i].uid,
                                dat.data[i].location.y,
                                dat.data[i].location.x,
                                dat.data[i].username,
                                dat.data[i].tel_number,
                                dat.data[i].location.floor,
                                i
                            );
                        }
                    } else {
                        console.log('ajax error!');
                    }
                }
            );
        }

        /**
         * 刷新频率
         */
        setInterval(getDataAndRefresh, (INTERVAL_TIME * 1000))
    });
</script>

</body>
</html>