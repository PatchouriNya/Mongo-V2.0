<?php

namespace app\admin\controller;

use think\Session;

class ManagerController extends BaseController
{

    public function index()
    {
        $table = connMongodb()->users;
        $cursor = $table->find(
            [],
            [
                # 指定显示的字段
                'projection' => [
                    '_id'      => 1,
                    'username' => 1,
                    'nickname' => 1,
                    'email'    => 1,
                    'img'      => 1
                ],
                'skip' => 0,
            ]
        );

        $data = [];
        foreach($cursor as $info) {
            $data[] = $info;
        };
        return view('',compact('data'));
    }

    public function log(){

        $table = connMongodb()->users;
        $cursor = $table->find(
            [],
            [
                # 指定显示的字段
                'projection' => [
                    '_id'      => 0,
                    'username' => 1,
                    'ip' => 1,
                    'login_time'    => 1,
                    'logout_time'      => 1
                ],
                'skip' => 0,
            ]
        );

        $data = [];
        foreach($cursor as $info) {
            $data[] = $info;
        };
        return view('',compact('data'));
    }

    public function create(){

        return view('');
    }

    public function save(){
        $data = input();
        $data['img'] = $this->upload_logo($data['username']);
        $rule = [
            'username|用户名' => 'require|min:3',
            'nickname|昵称' => 'require',
            'password|密码' => 'require|min:3',
            'email|邮箱' => 'require|email',
        ];

        $res = $this->validate($data, $rule);
        if ($res !== true) {
            $this->error($res);
            exit;
        }
        $table = connMongodb()->users;
        $result = $table->insertOne($data);
        if($result){
            return $this->success('添加成功','manager/index');
        }else{
            return $this->error('因未知原因添加失败');
        }

    }

    public function pwd($id){
        $table = connMongodb()->users;
        $info = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        $username = $info['username'];
        return view('',compact('username','id'));
    }

    public function cpwd(){
        $data = input('post.');
        $rule = [
            'username|用户名' => 'require',
            'pwd|密码' => 'require',
            'password|密码' => 'require',
        ];

        $result = $this->validate($data, $rule);
        if ($result !== true) {
            $this->error($result);
            exit;
        }
        $table = connMongodb()->users;
        $id = Session::get('admin_user');
        $id = (array)$id['_id'];
        $id = $id['oid'];
        $info = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($data['id'])]);
        $true = $info['password'];
        if($id == $info['_id']){
            if($true == $data['pwd']){
                $res = $table->updateOne(
                    ['_id' => new \MongoDB\BSON\ObjectId($data['id'])],
                    ['$set'=>['password'=>$data['password']]],
                    ['upsert'=>false]
                );
                if($res){
                    return $this->success('修改成功','login/logout');
                }
            }else{
                return $this->error('原始密码不正确');
            }
        }elseif ($id == '5eaa766ec8cf560e8d20b70a'){
            $res = $table->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($data['id'])],
                ['$set'=>['password'=>$data['password']]],
                ['upsert'=>false]
            );
            if($res){
                return $this->success('修改成功','login/logout');
            }
        }else{
            return $this->error('您不是超管，请不要动他人的密码');
        }


    }

    public function delete($id){
        $uid = Session::get('admin_user');
        $uid = (array)$uid['_id'];
        $uid = $uid['oid'];

        if($uid == '5eaa766ec8cf560e8d20b70a' && $id != $uid){
            $table = connMongodb()->users;
            $img = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
            $img = $img['img'];
            $res = $table->deleteMany(['_id' => new \MongoDB\BSON\ObjectId($id)]);

            if ($res){
                @unlink('.' . $img);
                return $this->success('删除成功','manager/index');
            }else{
                return  $this->error('删除失败');
            }
        }elseif ($id == $uid){
            return $this->error('请不要删自己');
        }else{
            return $this->error('您不是超级管理员,不配删除此用户');
        }


    }

    // 封装:图片上传方法
    public function upload_logo($username)
    {

        // 1.获取文件
        $file = request()->file('img');
        if (empty($file)) {
            $this->error('请上传头像');
        }
        // 2.移动文件
        $info = $file->validate(['size' => 10240000, 'ext' => 'jpg,jpeg,png,bmp,webp', 'type' => 'image/jpeg,image/png'])
            ->move('uploads/user_img',time() . $username);
        // 3.判断结果
        if ($info) {
            $path = DS . 'uploads' . DS .'user_img' . DS . $info->getSaveName();

            return $path;
        } else {
            $this->error($file->getError());
        }
    }

}
