<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class LoginController extends Controller
{
   public function login($username='',$password=''){
//        $data = input();

        if($username==null || $password==null ){
            return api_error('请正确填入用户名和密码');
        }
        $data['username'] = $username;
        $data['password'] = $password;
        $table = connMongodb()->vips;
        $res = $table->findOne($data,[
            'projection' => [
                'password'=>0
            ],
        ]);
        if(!$res){
            return api_error('账号或密码错误');
        }
        $data = (array)$res;
       return api($data);
   }

   public function register($username='',$password='',$nickname='',$mobile='',$email=''){
        $data = input('post.');
        $rule = [
            'username|用户名' => 'require|min:3',
            'password|密码' => 'require|min:3',
            'nickname|昵称' => 'require',
            'mobile|手机号' => 'require|max:11|number',
            'email|邮箱' => 'require|email',
        ];
       $checkInfo = $this->validate($data, $rule);
       if ($checkInfo === true){
            $data['token'] = sha1($username.time());
            $table = connMongodb()->vips;
            $res = $table->findOne(['username'=>$username]);
            if($res == null){
                $table->insertOne($data);
                return json([
                    'code' => 200,
                    'msg' => '注册成功',
                ]);
            }else{
                return json([
                    'code' => 300,
                    'msg' => '注册失败，用户名已存在',
                ]);
            }
       }else{
           return api_error($checkInfo);
       }
   }
}
