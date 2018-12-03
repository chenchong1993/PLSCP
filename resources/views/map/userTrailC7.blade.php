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
    {{--HUI的图标库--}}
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
<style>
    html, body, #map{
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
    }
</style>
{{--/*定义放大缩小按钮风格*/--}}
<style>
    .menu-btn {
        position: fixed;top:30px;left:1050px;font-size: 18px;
    }
    #showbigger{
        top:30px;
    }
    #showsmaller{
        top:70px;
    }
    #showIndex{
        top:110px;
    }
</style>
<div class="row">

    <div class="map-col">
        <div id="map"></div>
        <h2 class="menu-btn" style="left: 43%;font-size: 35px;color: #0c0c0c;top: 10px">C7用户轨迹</h2>
        <button  class="menu-btn" id="showbigger">放大点</button>
        <button  class="menu-btn" id="showsmaller">缩小点</button>
        <button class="menu-btn" id="showIndex" onclick=showIndex()>返回首页</button>

    </div>

</div>
<script>
    /**
     * 定义全局变量
     **/
    var POINTSIZE = 15;    //默认图片大小为15*15
    /**
     * 返回普通地图按钮的方法
     */
    function returnNormalMap() {
        window.location.href = '/normalMapC7';
    }
    function catUserRtTrail() {
        window.location.href = '/userRtTrailC7';
    }
    function showIndex() {
        window.location.href = '/index';
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
            "dojo/dom",
            "dojo/domReady!"
        ], function (Map, ArcGISDynamicMapServiceLayer,GraphicsLayer,Graphic,SpatialReference,InfoTemplate,Point,PictureMarkerSymbol,
                     SimpleMarkerSymbol,SimpleLineSymbol,Color,on,dom,){
        /**
         * 定义三张地图，并设定必要参数
         */
        var map = new Map("map", {
            center: new Point(538264,4212780, new SpatialReference({ wkid: 4547})),
            logo:false

        });
            /**
             * 初始化楼层平面图
             */
            var C7 = new ArcGISDynamicMapServiceLayer("http://121.28.103.199:5567/arcgis/rest/services/C7/NewC7Map/MapServer");
            map.addLayer(C7);
            /**
             * 定义点图层
             */
            var pointLayerC7 = new GraphicsLayer();
        /**
         * 放大缩小点图标按钮的具体实现方法
         */
        on(dom.byId("showbigger"),"click",function () {
            POINTSIZE++;
            pointLayerC7.clear();
            console.log(POINTSIZE);
            addPointToMap();
        });
        on(dom.byId("showsmaller"),"click",function () {
            POINTSIZE--;
            pointLayerC7.clear();
            console.log(POINTSIZE);
            addPointToMap();
        });
        /**
         * 添加用户点方法
         */
        function addUserPoint(id,uid, time,lng, lat,floor,status) {
            var name = '当前用户';
            var picpoint = new Point(lng,lat, new SpatialReference({ wkid: 4509}));
            // //定义点的图片符号
            var picSymbol = new PictureMarkerSymbol("{{ asset('static/Ips_api_javascript/Ips/image/marker.png') }}",POINTSIZE,POINTSIZE);
            //定义点的图片符号
            var attr = {"name": name, "time": time};
            //信息模板
            var infoTemplate = new InfoTemplate();
            infoTemplate.setTitle('用户');
            infoTemplate.setContent(
                "<b>名称:</b><span>${name}</span><br>"
                + "<b>时间:</b><span>${time}</span><br>"
                + "<button class='' onclick='returnNormalMap()' >返回查看用户分布图</button>"
                + "<button class='' onclick=catUserRtTrail() > 返回查看该用户实时轨迹</button>"
            );
            var picgr = new Graphic(picpoint, picSymbol, attr, infoTemplate);
            pointLayerC7.add(picgr);
            map.addLayer(pointLayerC7);
        }
        /**
         * 添加所有用户点到地图方法，分楼层显示并划定了建筑物边界，楼外不显示
         */
        function addPointToMap() {
                    @foreach($userPositionLists as $userPositionList)
                console.log({{$userPositionList->y}});
            console.log("{{$userPositionList->updated_at}}");
            var lng={{$userPositionList->y}};
            var lat={{$userPositionList->x}};
            var floor={{$userPositionList->floor}};
            addUserPoint(
                    {{$userPositionList->id}},
                    {{$userPositionList->uid}},
                '{{$userPositionList->updated_at}}',
                    {{$userPositionList->y}},
                    {{$userPositionList->x}},
                    {{$userPositionList->floor}},
                'normal'
            );
            @endforeach
        }
        addPointToMap();
    });
</script>

</body>
</html>