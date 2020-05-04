<?php

namespace app\api\controller;

use think\Controller;

class HotController extends Controller
{
    public function hot($id){

        try {
            $redis = redis_connect();
            $arr = $redis->zRange('click',0,-1);
            if(!in_array($id,$arr)){
                $redis->zAdd('click',0,$id);
            }
            $click = $redis->zIncrBy('click',1,$id);
            $table = connMongodb()->news;
            $table->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                ['$set'=>[
                    'click' => $click,
                ]],
                ['upsert'=>false]
            );
            return $click;
        } catch (\Exception $e) {
            return api_error('请提供正确的ID');
        }



    }

    public function hotList(){
        $redis = redis_connect();
        $arr = $redis->zrevRange('click',0,9);
        $table = connMongodb()->news;
        foreach ($arr as $id){
            $data[] = $table->findOne(
                ['_id' => new \MongoDB\BSON\ObjectId($id)],
                [
                    'projection' => [
                        'title' => 1,
                        'click' => 1,
                        'author'=> 1,
                        'ctime' => 1
                    ],
                ]
            );
        }

        foreach ($data as $vol){
            $vol['ctime'] = date('Y-m-d H:i:s',$vol['ctime']);
        }

        return api($data);
    }
}
