<?php

namespace app\admin\controller;

use app\org\Page;
use think\Controller;
use think\Request;

class VipsController extends BaseController
{
    public function index(){
        $table = connMongodb()->vips;
        $keyword = input('get.keyword');
        $total = $table->count();
        $pagesize = 3;

        if($keyword) {
            $arr = $table->find(
                ['username' => new \MongoDB\BSON\Regex($keyword, 'i'),],
                []
            );
            $arr = $arr->toArray();
            $total = count($arr);
            $page = new Page($total, $pagesize);
            $cursor = $table->find(
                ['username' => new \MongoDB\BSON\Regex($keyword, 'i'),],
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


        $fpage = $page->fpage();
//        dump($data);
        return view('',compact('data','fpage'));
    }

    public function delete($id){
        $table = connMongodb()->vips;
        $res = $table->deleteMany(['_id' => new \MongoDB\BSON\ObjectId($id)]);

        if ($res){

            return $this->success('删除成功','vips/index');
        }else{
            return  $this->error('删除失败');
        }
    }

    public function delall(){
        $data = input('post.');
        if ($data == null){
            return $this->error('请至少选择一个用户');
        }
        $ids = $data['id'];

        $table = connMongodb()->vips;
        foreach ($ids as $id){
            $res = $table->deleteMany(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        }
        if ($res){
            return $this->success('删除成功','vips/index');
        }else{
            return  $this->error('删除失败');
        }
    }
}
