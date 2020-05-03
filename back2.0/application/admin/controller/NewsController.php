<?php

namespace app\admin\controller;

use app\org\Page;
use think\Controller;

class NewsController extends BaseController
{
    public function index(){
        $table = connMongodb()->news;

//        搜索
        $keyword = input('get.keyword');


        $total = $table->count();
        $pagesize = 5;


        if($keyword){
            $arr = $table->find(
                ['title' => new \MongoDB\BSON\Regex($keyword, 'i'),],
                []
            );
            $arr = $arr->toArray();
            $total = count($arr);
            $page = new Page($total,$pagesize);
            $cursor = $table->find(
                ['title' => new \MongoDB\BSON\Regex($keyword, 'i'),],
                [
                    # 指定显示的字段
                    'projection' => [
                        '_id'      => 1,
                        'title' => 1,
                        'desn' => 1,
                        'author'=>1,
                        'ctime'    => 1,
                        'click'      => 1
                    ],
                    'skip' => $page->offset,
                    'limit'=> $pagesize
                ]
            );
        }else{
            $page = new Page($total,$pagesize);
            $cursor = $table->find(
                [],
                [
                    # 指定显示的字段
                    'projection' => [
                        '_id'      => 1,
                        'title' => 1,
                        'desn' => 1,
                        'author'=>1,
                        'ctime'    => 1,
                        'click'      => 1
                    ],
                    'skip' => $page->offset,
                    'limit'=> $pagesize
                ]
            );
        }




        $data = [];
        foreach($cursor as $info) {
            $data[] = $info;
        };
        $fpage = $page->fpage();
        return view('',compact('data','fpage'));
    }

    public function create(){
        return view('');
    }

    public function save(){
        $data = input('post.');
        $rule = [
            'title|标题' => 'require|max:15',
            'desn|描述' => 'require|max:15',
            'author|作者' => 'require|max:15',
            'body|内容' => 'require',
        ];

        $res = $this->validate($data, $rule);
        if ($res !== true) {
            $this->error($res);
            exit;
        }
        $data['ctime'] = time();
        $data['click'] = 0;
        $table = connMongodb()->news;
        $table -> insertOne($data);
        return $this->success('添加新闻成功','news/index');
    }

    public function delete($id){
        $table = connMongodb()->news;
        $res = $table->deleteMany(['_id' => new \MongoDB\BSON\ObjectId($id)]);

        if ($res){
            return $this->success('删除成功','news/index');
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

        $table = connMongodb()->news;
        foreach ($ids as $id){
            $res = $table->deleteMany(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        }
        if ($res){
            return $this->success('删除成功','news/index');
        }else{
            return  $this->error('删除失败');
        }
    }

    public function edit($id){
        $table = connMongodb()->news;
        $info = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

        return view('',compact('info'));
    }

    public function update(){
        $data = input('post.');
        $rule = [
            'title|标题' => 'require|max:15',
            'desn|描述' => 'require|max:15',
            'author|作者' => 'require|max:15',
            'body|内容' => 'require',
        ];

        $res = $this->validate($data, $rule);
        if ($res !== true) {
            $this->error($res);
            exit;
        }

        $table = connMongodb()->news;
        $res = $table->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($data['id'])],
            ['$set'=>[
                'title'=>$data['title'],
                'desn' =>$data['desn'],
                'author'=>$data['author'],
                'body'=>$data['body'],
                'ctime'=>time()
            ]],
            ['upsert'=>false]
        );
        if($res){
            return $this->success('修改成功','news/index');
        }else{
            return $this->error('修改失败');
        }
    }

    public function hot(){
        $redis = redis_connect();
        $arr = $redis->zrevRange('click',0,9);
        $table = connMongodb()->news;
        foreach ($arr as $id){
            $data[] = $table->findOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                [
                    # 指定显示的字段
                    'projection' => [
                        '_id'      => 1,
                        'title' => 1,
                        'desn' => 1,
                        'author'=>1,
                        'ctime'    => 1,
                        'click'      => 1
                    ],
                ]
            );
        }
        return view('',compact('data'));
    }
}
