<?php
use MongoDB\Client;

function connMongodb(){
    $client = new Client("mongodb://qwq:qwq@39.107.245.179:27017/php");
//    获取数据库
    return $client->php;
}

function redis_connect(){
    $redis = new \Redis();
//            读取配置文件中的
    $config_redis = config('redis');
    $redis->connect($config_redis['host'],$config_redis['port'],$config_redis['port']);
    $redis->auth($config_redis['auth']);

    return $redis;
}



/**
 * 获取客户端IP地址
 */
function real_ip()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] AS $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    return $ip;
}

//接口返回信息
function api(array $data){
    return json([
        'code' => 200,
        'msg' => '获取成功',
        'data' => $data
    ]);
}

function api_error($msg){
    return json([
        'code' => 100,
        'msg' => $msg,
        'data' => null
    ]);

function create_token($id,$out_time){
    return substr(md5($id.$out_time),5,26);
    }
}