<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class VipsController extends Controller
{

    public function index()
    {

    }

    public function per($id=''){

        try {
            new \MongoDB\BSON\ObjectID($id);
            $table = connMongodb()->vips;
            $data = $table->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)],[
                'projection' => [
                    'password'=>0
                ],
            ]);
            $data = (array)$data;
            return api($data);
        } catch (\Exception $e) {
            return api_error('请提供正确的ID');
        }

    }


}
