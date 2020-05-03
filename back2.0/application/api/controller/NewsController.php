<?php

namespace app\api\controller;

use app\org\Page;
use think\Controller;

class NewsController extends Controller
{
//    全部数据接口
    public function index(){
        $table = connMongodb()->news;

//        搜索
        $keyword = input('get.keyword');


        $total = $table->count();
        $pagesize = (int)input('get.pagesize') ?: 5;


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
                    'skip' => $page->offset,
                    'limit'=> $pagesize
                ]
            );
        }else{
            $page = new Page($total,$pagesize);
            $cursor = $table->find(
                [],
                [
                    'skip' => $page->offset,
                    'limit'=> $pagesize
                ]
            );
        }




        $data = [];
        $data['total'] = $total;
        $data['pagesize'] = $pagesize;
        $data['current_page'] = (int)$page->page;
        $data['last_page'] = ceil($total/$pagesize);
        foreach($cursor as $info) {
            $info['ctime'] = date('Y-m-d H:i:s',$info['ctime']);
            $data['data'][] = $info;

        };

        if(!array_key_exists('data',$data)){
            return api_error('数据为空，获取失败');
        }

        return api($data);
    }

//    详情界面接口
    public function detail($id){
        try {
            new \MongoDB\BSON\ObjectID($id);
            $table = connMongodb()->news;
            $data = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
            $data = (array)$data;
            $data['body'] = htmlspecialchars_decode($data['body']);
            $data['ctime'] = date('Y-m-d H:i:s',$data['ctime']);
            return api($data);
        } catch (\Exception $e) {
            return api_error('请提供正确的ID');
        }

    }

    public function qwq(){
        // 接受表单数据
//       $data = input('post.');
        $data[] = 1;
        $data[] = 2;
        return api($data);
    }


}

