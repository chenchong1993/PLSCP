<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/23
 * Time: 10:42
 */

namespace App\Http\Controllers;
use App\Bluetooth;
use App\Coo;
use App\HeatMapData;
use App\Obs;
use App\RtCoo;
use App\Sensor;
use App\User;
use App\Wifi;
use Couchbase\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
//use Maatwebsite\Excel\Excel;
use Excel;
use function PHPSTORM_META\type;


class ApiController extends Controller
{
    /**
     * 测试路由，用来看隧道是否畅通
     */
    public function apiTest()
    {
        return 0;

    }

    /**
     * 用户注册接口
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function apiUserAdd(Request $request)
    {

        //控制器验证，如果通过继续往下执行，如果没通过抛出异常返回当前视图。
        if ($request->isMethod('POST')){
            $this->validate($request,[
                'username'=>'required',
                'password'=>'required',
                'email'   =>'required',
                'sex'     =>'required',
                'mobile'     =>'required',
            ],[
                'required'=>':attribute 为必填项'
            ],[
                'username'=>'用户名',
                'password'=>'密码',
                'email'   =>'email',
                'sex'     =>'性别',
                'mobile'     =>'手机号',
            ]);
        }
//从表单视图传过来的输入信息
        $username = $request->input('username');
        $password = $request->input('password');
        $email    = $request->input('email');
        $sex      = $request->input('sex');
        $mobile      = $request->input('mobile');
        if ($sex==10) {
            $sex="保密";
        }
        elseif ($sex==20) {
            $sex="男";
        }
        else{
            $sex="女";
        }

//获取uid的过程如下。

//如果接口返回的数据为json，这里需要先定义数据类型为json
        $url = "http://121.28.103.199:5583/service/user/v1/signup?appid=10";
        $data = array('username'=>$username,'password'=>$password,'admin'=>false);
//调用封装的json请求方法
        $response = $this->apiPostJson($url,$data);
//将返回的字符串进行分析
        //1. 验证用户名是否重复
        //2. 截取出uid存入用户数据库
//两种返回结果
        //1. { "result": true, "error": "", "data": 30152179566247939 }
        //2. { "result": false, "error": "用户名已被占用", "data": null }
//判断是否注册成功
        if(strpos($response,"true")==false){
//            return redirect('PLSCP/USERcreate')->with('error','添加失败');
            if (strpos($response,"用户名已被占用")==false){
                return redirect('PLSCP/USER/add')->with('error','未知错误');
            }else{
                return redirect('PLSCP/USER/add')->with('error','用户名已被占用');
            }

        }else{
            $uid = substr($response, -19, 17);
            $users = new User();
            $users->uid = $uid;
            $users->username = $username;
            $users->password = $password;
            $users->sex = $sex;
            $users->email = $email;
            $users->tel_number = $mobile;
            if ($users->save()){
                return redirect('userList')->with('success','添加成功');
            }else{
                return redirect('userAdd')->with('error','数据库存储错误');
            }

        }

    }

    /**
     * 删除用户接口
     */
    public function apiUserDelete($uid)
    {
        echo $uid;
        $users = User::find($uid);
        if ($users->delete()){
            return redirect('userList')->with('success','删除成功-'.$uid);
        }else{
            return redirect('userList')->with('error','删除失败-'.$uid);
        }

    }

    /**
     * 查询结果接口
     */
    public function apiSearchResult(Request $request)
    {
        if ($request->isMethod('POST')){
            $this->validate($request,[
                'content'=>'required',
                'type'=>'required',
            ],[
                'required'=>':attribute 为必填项'
            ],[
                'content'=>'查询内容',
                'type'=>'查询类型',
            ]);
        }

        $content = $request->input('content');
        $type = $request->input('type');
        if ($type==10) {
            $type="username";
        }
        elseif ($type==20) {
            $type="address";
        }
        else{
            $type="物名";
        }

        $user = User::where($type ,'like', '%'.$content.'%')->get();
        if ($user->isEmpty()){
            return redirect('nameSearch')->with('error','查询结果不存在');
        }
        else{
            return view('search.Result',['users' => $user]);
        }

    }

    /**
     * 修改用户资料接口 没用上
     */
    public function apiUserUpdate(Request $request,$uid)
    {
        $users = User::find($uid);
        //控制器验证，如果通过继续往下执行，如果没通过抛出异常返回当前视图。
        if ($request->isMethod('POST')){
            $this->validate($request,[
                'username'=>'required',
                'password'=>'required',
                'email'   =>'required',
                'sex'     =>'required',
                'mobile'  =>'required',
            ],[
                'required'=>':attribute 为必填项'
            ],[
                'username'=>'用户名',
                'password'=>'密码',
                'email'   =>'email',
                'sex'     =>'性别',
                'mobile'  =>'手机号',
            ]);
        }
        if ($request->isMethod('POST')){
            $username = $request->input('username');
            $password = $request->input('password');
            $email    = $request->input('email');
            $sex      = $request->input('sex');
            $group_id = $request->input('group_id');
            $tel_number = $request->input('mobile');
            $address = $request->input('address');

            $users->username = $username;
            $users->password = $password;
            $users->sex = $sex;
            $users->email = $email;
            $users->group_id = $group_id;
            $users->tel_number = $tel_number;
            $users->address = $address;
            if ($users->save()){
                return redirect('userList')->with('success','修改成功-'.$uid);
            }
        }

        return view('user.update',[
            'users'=>$users
        ]);

    }

    /**
     * 添加用户的位置信息 也就是终端用户实时位置上报
     * 1.添加到位置数据库
     * 2.添加或者更新实时位置数据库
     */
    public function apiAddUserLocation()
    {
        $validator = Validator::make(rq(), [
            'uid' => 'required',
            'x' => '',
            'y' => '',
            'lng' => 'required',
            'lat' => 'required',
            'floor' => 'required',
            'orien' => '',
            'location_method' => 'required'
//            location_method = 0 指纹
//            location_method = 1 混合
//            location_method = 2 视觉
        ]);

        if ($validator->fails())
            return err(1, $validator->messages());

        $uid = rq('uid');
        $x = rq('x');
        $y = rq('y');
        $lng = rq('lng');
        $lat = rq('lat');
        $floor = rq('floor');
        $orien = rq('orien');
        $location_method = rq('location_method');

        $users =RtCoo::where('uid',$uid)->first();
        if ($users){
//            更新
            $users->x = $x;
            $users->y = $y;
            $users->lng = $lng;
            $users->lat = $lat;
            $users->floor = $floor;
            $users->orien = $orien;
            $users->location_method = $location_method;
            $users->save();

    }
    else{
            //插入
            $users = new RtCoo();
            $users->uid = $uid;
            $users->x = $x;
            $users->y = $y;
            $users->lng = $lng;
            $users->lat = $lat;
            $users->floor = $floor;
            $users->orien = $orien;
            $users->location_method = $location_method;
            $users->save();

        }

        $userLocation = new Coo();
        $userLocation->uid = $uid;
        $userLocation->x = $x;
        $userLocation->y = $y;
        $userLocation->lng = $lng;
        $userLocation->lat = $lat;
        $userLocation->floor = $floor;
        $userLocation->orien = $orien;
        $users->location_method = $location_method;
        $userLocation->save();
        return suc();
    }

    /**
     * 终端根据用户名获取UID接口
     */
    public function apiGetUid()
    {
        $validator = Validator::make(rq(), [
        'username' => 'required|string',
    ]);

        if ($validator->fails())
            return err(1, $validator->messages());

        $user_name = rq('username');

        $user = User::where("username" ,'like', '%'.$user_name.'%')->get();
        if ($user->isEmpty()){
            return  err(1, $validator->messages());
        }
        else{
            return $user[0]->uid;
        }

    }

    /**
     * 返回所有用户的最新位置信息列表
     */
    public function apiGetAllUserNewLocationList()
    {
        $users = User::get();
//        $users = RtCoo::get();
        foreach ($users as $user) {
            $user_location =RtCoo::where('uid', $user->uid)->first();
            $user['location'] = $user_location;
        }
        return suc($users);
    }

    /**
     * 观测数据上传接口
     * 上传wifi,蓝牙，传感器，分别存储到不同的表里
     */
    public function apiAddObs()
    {
        $validator = Validator::make(rq(), [
            'uid' => 'required',
            'lng' => 'required',
            'lat' => 'required',
            'x' => '',
            'y' => '',
            'floor' => 'required',
            'orien' => 'required',
            'wifi' => '',
            'bluetooth'=>'',
            'sensor'=>''
        ]);

        if ($validator->fails())
            return err(1, $validator->messages());

        $uid=rq('uid');
        $lng=rq('lng');
        $lat=rq('lat');
        $x = rq('x');
        $y = rq('y');
        $floor=rq('floor');
        $orien=rq('orien');
        $wifi=rq('wifi');
        $blue_tooth=rq('bluetooth');
        $sensor=rq('sensor');

        $wifiData = new Wifi();
        $bluData = new  Bluetooth();
        $sensorData = new Sensor();
//wifi表上传
        $wifiData->uid=$uid;
        $wifiData->lng=$lng;
        $wifiData->lat=$lat;
        $wifiData->x=$x;
        $wifiData->y=$y;
        $wifiData->floor=$floor;
        $wifiData->orien=$orien;
        $wifiData->wifi = $wifi;
//蓝牙表上传
        $bluData->uid=$uid;
        $bluData->lng=$lng;
        $bluData->lat=$lat;
        $bluData->x=$x;
        $bluData->y=$y;
        $bluData->floor=$floor;
        $bluData->orien=$orien;
        $bluData->blue_tooth = $blue_tooth;

//传感器表上传

        $sensorData->uid=$uid;
        $sensorData->lng=$lng;
        $sensorData->lat=$lat;
        $sensorData->x=$x;
        $sensorData->y=$y;
        $sensorData->floor=$floor;
        $sensorData->orien=$orien;
        $sensorData->sensor = $sensor;

        $wifiData->save();
        $bluData->save();
        $sensorData->save();
        return suc();

    }

    /**
     * 读热力图数据
     */
    public function heatMapData(){

        $validator = Validator::make(rq(), [
            'type' => 'required',
            'floor'=>'required'
        ]);

        if ($validator->fails())
            return err(1, $validator->messages());

        $type = rq('type');
        $floor = rq('floor');
        $heatMapData = HeatMapData::where("type" ,'=', $type)->where("floor" ,'=', $floor)->get();
//        $data = json_decode($heatMapData[0]->data, true);
        $data = $heatMapData[0]->data;
        $dataobj = json_decode($data);

//        return $dataobj;
        return $data;
        var_dump($dataobj);

    }

    /**
     * 保存文件,csv格式
     * 检索一段时间内的位置数据并导出为文件
     */
    public function fileExport(){
        ini_set('memory_limit','500M');
        set_time_limit(0);//设置超时限制为0分钟
//        $cellData = User::select('uid','uid','uid')->limit(5)->get()->toArray();

        $uid = rq('uid');
        $startTime = rq('startTime');//"2018-10-22 11:36:07";//rq('startTime');
        $endTime = rq('endTime');//"2018-10-22 11:38:19";//rq('endTime');
        if ($startTime== '' or $endTime == ''){
            return '输入时间段为空';
        }
        $userPositionList = Coo::where('uid' ,'=', $uid)->where('created_at', '>=', $startTime)->where('created_at', '<=', $endTime)->get();
        if ($userPositionList->isEmpty()){
            return '输入有误或该时间段内没有数据';
        }
        $userPositionList->toArray();
        $cellData[0] = array('参与评估方简称','用户识别码','时间','楼层','X','Y','Z');
        for($i=1;$i<count($userPositionList);$i++){
            $cellData[$i][0] = 'BLH';
            $cellData[$i][1] = $userPositionList[$i]->uid;
            $cellData[$i][2] = $userPositionList[$i]->created_at;
            $cellData[$i][3] = $userPositionList[$i]->floor;
            $cellData[$i][4] = $userPositionList[$i]->lat;
            $cellData[$i][5] = $userPositionList[$i]->lng;
            $cellData[$i][6] = '0';
        }
        Excel::create('位置信息',function($excel) use ($cellData){
            $excel->sheet('location', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('csv');
        die;

    }
}