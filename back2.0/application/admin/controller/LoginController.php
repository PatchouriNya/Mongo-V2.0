<?php
// 后台登录

namespace app\admin\controller;

use think\Controller;
use think\Session;

class LoginController extends Controller {

    public function index() {
        return view();
    }

    // 用户登录处理
    public function login() {
        // 接受表单数据
        $data = input('post.');

        // 得到集合
        $rule = [
            'username|用户名' => 'require|min:3',
            'password|密码' => 'require|min:3',
            'code|验证码' => 'require|captcha:login'
        ];
        $table = connMongodb()->users;
        $checkInfo = $this->validate($data, $rule);
        if ($checkInfo === true){
            unset($data['code']);
            $ret = $table->findOne($data);
            $key = 'cs'.$data['username'];
            $redis = redis_connect();
            if(!$ret){
                if(!$redis->exists($key)){
                    $redis->set($key,0,86400);
                }
                $times = $redis->incr($key);
                if($times > 10){
                    return $this->error('尝试次数太多,请一天后再试');
                }
                return $this->error('账号或密码错误');
            }
            $times = $redis->get($key);
//            dump($times);
            if($times > 10){
                return $this->error('尝试次数太多,请一天后再试');
            }
//        写入session
            session('admin_user',$ret);
            $redis -> del($key);



            $id = Session::get('admin_user');
            $id = (array)$id['_id'];
            $id = $id['oid'];

            $table->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set'=>['ip'=>real_ip(),'login_time'=>time()]],
                ['upsert'=>false]
            );

            return $this->redirect(url('index/index'));
        }else{
            $this->error($checkInfo);
        }

    }

    public function logout(){
        if(!session('?admin_user')){
            $this->redirect(url('login/index'));
        }
          $id = Session::get('admin_user');
          $id = (array)$id['_id'];
          $id = $id['oid'];
          $table = connMongodb()->users;
          $table->updateOne(
              ['_id' => new \MongoDB\BSON\ObjectId($id)],
              ['$set'=>['logout_time'=>time()]],
              ['upsert'=>false]
          );
        session(null);
        $this->success('退出成功', 'admin/login/index');
    }
}
