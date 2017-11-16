<?php
namespace Home\Controller;

use Think\Controller;
use Think\Gateway;
class IndexController extends Controller {
    //渲染登录的界面
    public function index(){
        $this->display("logins");
    }
    //退出登录
    public function autologin()
    {
        session(null); // 清空当前的session
        //调用登录界面模板
        $this->display("logins");
    }
    //登录界面
    public function login()
    {
        $User = M("Username"); // 实例化User对象
        if (IS_POST) {
            $nameInfo = I("post.");//拿到用户发过来的数据
            $name = $nameInfo['username'];
            $pwd = $nameInfo['password'];
            //根据数据查询用户是否存在
            $list = $User->where("username = '$name' and password = '$pwd'")->find();
            if (!empty($list)){
                $id = $list['id'];//当前用户的id
                session('user_id', $list['id']);//存用户的id
                //查询该好友的列表
                $friend = M("Friend");
                $userInfo = $User->select();//查询用户的所有信息
                $friend_id = $friend->field("friend_id")->where("uid = '$id'")->select();//查询该好友的信息
                //将好友的id放入新的一维数组当中
                $friend_str = array();
                foreach($friend_id as $k=>$v){
                    $friend_str[] = $v['friend_id'];
                }
                //通过循环用户列表判断该用户列表中是否包含我的好友id
                foreach($userInfo as $k=>$v){
                    if(in_array($v['id'],$friend_str)){
                        //判断好友用户是否在线
                        if(Gateway::isUidOnline($v['id']) == 1|| $v['id'] == $list['id']){
                            $new_message[] = array("state" => "1","name"=>$v['username'],"uid"=>$v['id']);
                        }else{
                            $new_message[] = array("state" => "0","name"=>$v['username'],"uid"=>$v['id']);
                        }
                    }else{
                        //判断非好友用户是否在线
                        if(Gateway::isUidOnline($v['id']) == 1|| $v['id'] == $list['id']){
                            $new[] = array("state" => "1","name"=>$v['username'],"uid"=>$v['id']);
                        }else{
                            $new[] = array("state" => "0","name"=>$v['username'],"uid"=>$v['id']);
                        }
                    }
                }
                $this->assign("message",$new_message);//查询好友的列表
                $this->assign("mo_user",$new);//查询到的非好友列表
                $this->assign("name",$list['username']);
                $this->assign("uid",$list['id']);
                $this->display("index");

            }else {
                $this->success("登录失败",'Index/index');
            }
        } else {
            $this->success("请先登录",'Index/index');
        }
    }
    //文件上传
    public function Upload()
    {
        $file=$_FILES;
        $path = "Public/uploads/logo/"; //上传路径
        if (!file_exists($path)) {//检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir ($path,0777,true);
        }
        $tp = array("image/gif", "image/jpeg", "image/jpg", "image/png");
        if (!in_array($file["file"]["type"], $tp)) {//检查图片的格式是否正确
            return  "格式不对";
            exit;
        }
        if ($file["file"]['name']) {
            $file1 = $file["file"]["name"];
            $file2 = $path . time() . $file1;
            $flag = 1;
        }
        if ($flag) $result = move_uploaded_file($file["file"]["tmp_name"], $file2);
        if ($result) {
            $res=array(
                'code' => 0,
                'msg' => '上传成功！',
                'data' => array(
                    'src'=>$file2,
                    'title'=>$file1
                )
            );
            echo json_encode($res);
        }else{
            $res=array(
                'code' => 1,
                'msg' => "失败",
                'data' => array(
                    'src'=>'',
                    'title'=>"失败"
                )
            );
            echo json_encode($res);
        }
    }
    //绑定参数
    public function buid()
    {
        $user_id = session('user_id');//获取登录的用户名
        $cliend = I("post.cliend");
        Gateway::bindUid($cliend,$user_id);
    }
    //加好友入库
    public function applefrined(){
       $firendInfo = I("post.");
       $Friend = M("Friend");
       $data['uid'] = $firendInfo['to_uid'];
       $data['friend_id'] = $firendInfo['from_uid'];
       $res = $Friend->add($data);
       if($res){
           $message = ["code"=>"1","to_uid"=>$firendInfo['to_uid'],"from_uid"=>$firendInfo['from_uid'],"message"=>"添加好友成功"];
       }else{
           $message =["code"=>"0","message"=>"添加好友失败"];
       }
       echo json_encode($message);
    }
    //同意请求后添加入库
    public function friends()
    {
        $firendInfo = I("post.");
        $Friend = M("Friend");
        $data['uid'] = $firendInfo['from_uid'];
        $data['friend_id'] = $firendInfo['to_uid'];
        $res = $Friend->add($data);
        if($res){
            $message = ["code"=>"1","message"=>"添加好友成功"];
        }else{
            $message = ["code"=>"0","message"=>"添加好友失败"];
        }
        echo json_encode($message);
    }
}