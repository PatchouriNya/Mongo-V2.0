<?php

namespace app\admin\controller;

use app\org\Page;
use think\Controller;

class CommentsController extends BaseController
{
    public function index(){
        $table = connMongodb()->comments;
        $keyword = input('get.keyword');
        $total = $table->count();
        $pagesize = 5;

        if($keyword) {
            $arr = $table->find(
                ['userid' => new \MongoDB\BSON\Regex($keyword, 'i'),],
                []
            );
            $arr = $arr->toArray();
            $total = count($arr);
            $page = new Page($total, $pagesize);
            $cursor = $table->find(
                ['userid' => new \MongoDB\BSON\Regex($keyword, 'i'),],
                [
                    'skip' => $page->offset,
                    'limit' => $pagesize
                ]
            );
        }else{
            $page = new Page($total,$pagesize);
            $cursor = $table->find(
                [],
                [
                    # 指定显示的字段
                    'skip' => $page->offset,
                    'limit'=> $pagesize
                ]
            );
        }
        $data = [];
        foreach($cursor as $info) {
            $data[] = $info;
        };

//        $data = $table -> find([])->toArray();
        $title=[];
        foreach ($data as $val){
            $title = connMongodb()->news->findOne(['_id' => new \MongoDB\BSON\ObjectId($val['newsid'])],[
                'projection' => [
                    '_id'      => 0,
                    'title' => 1
                ],
            ]);
            $title = (array)$title['title'];
            $val['title'] = $title[0];
        }
        $fpage = $page->fpage();
        return view('',compact('data','title','fpage'));
    }

    public function read($id){
        $table = connMongodb()->comments;
        $data = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        return view('',compact('data'));
    }

    public function delete($id){
        $table = connMongodb()->comments;
        $data = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        $data = (array)$data;
        $res = $table->deleteMany(['_id' => new \MongoDB\BSON\ObjectId($id)]);

        if ($res){
            if ($data['to'] == ''){
                $redis = redis_connect();
                $redis->select(1);
                $key = $data['userid'].$data['newsid'];
                if($redis->exists($key)){
                    $redis->del($key);
                }
            }

            return $this->success('删除成功','comments/index');
        }else{
            return  $this->error('删除失败');
        }
    }

    public function delall(){
        $data = input('post.');
        if ($data == null){
            return $this->error('请至少选择一条新闻');
        }
        $ids = $data['id'];

        $table = connMongodb()->comments;
        foreach ($ids as $id){
            $data = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
            $data = (array)$data;
            if ($data['to'] == ''){
                $redis = redis_connect();
                $redis->select(1);
                $key = $data['userid'].$data['newsid'];
                if($redis->exists($key)){
                    $redis->del($key);
                }
            }

            $res = $table->deleteMany(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        }
        if ($res){
            return $this->success('删除成功','comments/index');
        }else{
            return  $this->error('删除失败');
        }
    }
}
