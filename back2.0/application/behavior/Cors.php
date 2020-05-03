<?php
// 跨域所用

namespace app\behavior;


class Cors {
    // 调用行为就执行
    public function run(&$params) {
        // 记日志
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:*');
        header('Access-Control-Allow-Headers:Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With');


    }
}