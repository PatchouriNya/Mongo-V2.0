<?php

namespace app\api\controller;

use think\Controller;

class IndexController extends Controller
{
    public function index(){
        $table = connMongodb()->api;
        $data = $table->find([])->toArray();
        return view('',compact('data'));
    }
}
