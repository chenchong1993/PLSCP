<?php

function rq($key = null)
{
    return ($key == null) ? \Illuminate\Support\Facades\Request::all() : \Illuminate\Support\Facades\Request::get($key);
}
/**
 * @param null $data
 * @return array 成功返回0
 */
function suc($data = null)
{
    $ram = ['status' => 0];
    if ($data) {
        $ram['data'] = $data;
        return $ram;
    }
    return $ram;
}
/**
 * @param $code
 * @param null $data
 * @return array 失败返回错误码和信息
 */
function err($code, $data = null)
{
    if ($data)
        return ['status' => $code, 'data' => $data];
    return ['status' => $code];
}

/*
|--------------------------------------------------------------------------
| Web Routes
|----------------------------------------userTrail----------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => 'web'], function () {
    Route::get('index','PageController@index');//首页
    //用户管理
    Route::get('userList','PageController@userList'); //用户列表
    Route::get('userOnlineList','PageController@userOnlineList'); //在线用户列表
    Route::get('userAdd','PageController@userAdd'); //增加用户
    Route::any('userUpdate/{uid}','PageController@userUpdate'); //修改用户资料
    Route::get('userDetail/{uid}','PageController@userDetail'); //显示用户资料
    //地图
    Route::any('normalMap','PageController@normalMap'); //普通地图331
    Route::any('normalMapC7','PageController@normalMapC7'); //普通地图C7
    Route::any('poiMap','PageController@poiMap'); //兴趣点图
    Route::any('routeMap','PageController@routeMap'); //路网图
    Route::any('heatMap','PageController@heatMap'); //热力图
    Route::any('userTrail/','PageController@userTrail'); //331 历史轨迹
    Route::any('userTrailC7/','PageController@userTrailC7'); //C7 历史轨迹
    Route::any('userRtTrailC7/','PageController@userRtTrailC7'); //C7 实时轨迹
    //普通查询
    Route::any('nameSearch','PageController@nameSearch'); //名称查询
    Route::any('extentSearch','PageController@extentSearch'); //扩展查询
    //消息推送
    Route::any('pushToOne','PageController@pushToOne'); //私信
    Route::any('pushToMore','PageController@pushToMore'); //群发
    //热力图
    Route::any('wifiSignalHeatMap','PageController@wifiSignalHeatMap'); //wifi信号强度热力图
    Route::any('bluSignalHeatMap','PageController@bluSignalHeatMap'); //蓝牙信号强度热力图
    Route::any('hdopHeatMap','PageController@hdopHeatMap'); //hdop热力图
    Route::any('vdopHeatMap','PageController@vdopHeatMap'); //vdop热力图
    Route::any('pdopHeatMap','PageController@pdopHeatMap'); //pdop热力图
    Route::any('gdopHeatMap','PageController@gdopHeatMap'); //gdop热力图
    Route::any('rssHeatMap','PageController@rssHeatMap'); //rss热力图
    Route::any('peopleHeatMap','PageController@peopleHeatMap'); //桥西区人口分布热力图
    Route::any('peopleIn331','PageController@peopleIn331'); //331人口分布热力图


    Route::group(['prefix' => 'api'], function () {
        Route::post('apiTest', 'ApiController@apiTest');//测试路由
        Route::post('apiUserAdd', 'ApiController@apiUserAdd');//添加用户
        Route::post('apiSearchResult', 'ApiController@apiSearchResult');//添加用户
        Route::any('apiUserDelete/{uid}','ApiController@apiUserDelete'); //删除用户
        Route::any('apiUserUpdate/{uid}','ApiController@apiUserUpdate'); //修改用户资料
        Route::post('apiGetUid', 'ApiController@apiGetUid');//为终端获取用户UID
        Route::post('apiAddUserLocation', 'ApiController@apiAddUserLocation');//终端上传用户坐标
        Route::get('apiGetAllUserNewLocationList', 'ApiController@apiGetAllUserNewLocationList');//从数据库中获取用户位置信息
        Route::post('apiAddObs', 'ApiController@apiAddObs');//获取观测数据
        Route::any('heatMapData', 'ApiController@heatMapData');//读热力图数据
        Route::any('fileExport/','ApiController@fileExport'); //Excel导出

});
});
