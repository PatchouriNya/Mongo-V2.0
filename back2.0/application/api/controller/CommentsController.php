<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class CommentsController extends Controller
{
   public function index($newsid){

        $table = connMongodb()->comments;
        $cursor = $table->find(['newsid'=>$newsid])->toArray();
//        dump($cursor);
        return api($cursor);

   }

   public function create($userid,$newsid,$content,$to = ''){
       try {
           new \MongoDB\BSON\ObjectID($userid);
           new \MongoDB\BSON\ObjectID($newsid);
           $data = [];
           $data['userid'] = $userid;
           $data['newsid'] = $newsid;
           $data['content'] = $content;
           $data['to'] = $to;
           $table = connMongodb()->vips;
           $arr = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($userid)],
               [
                   'projection' => [
                       '_id'=>0,
                       'nickname'=>1
                   ],
               ]);
           $data['nickname'] = $arr['nickname'];
           $data['ctime'] = date('Y-m-d H:i:s',time());
//           $data['to'] = '';
           $table = connMongodb()->comments;
           if($data['to'] != ''){
               $cursor = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($data['to'])],[
                   'projection' => [
                       '_id'=>0,
                       'userid'=>1,
                       'nickname'=>1
                   ],
               ]);
               $cursor = (array)$cursor;
               $data['toid'] = $cursor['userid'];
               $data['tonick'] = $cursor['nickname'];
           }else{
               $data['toid'] = '';
               $data['tonick'] = '';

               $redis = redis_connect();
               $redis->select(1);
               $key = $userid . $newsid;
               if(!$redis->exists($key)){
                   $redis->set($key,1);
                   $table->insertOne($data);
                   return api($data);
               }else{
                   return api_error('300');
               }


           }



           $table->insertOne($data);


           return api($data);
       } catch (\Exception $e) {
           return api_error('请提供正确的ID');
       }


   }
}
