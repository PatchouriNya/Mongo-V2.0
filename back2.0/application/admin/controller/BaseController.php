<?php

namespace app\admin\controller;

use think\Controller;
use think\Session;

class BaseController extends Controller
{
   public function _initialize(){

       if(!session('?admin_user')){
           $this->redirect(url('login/index'));
       }
   }
}
