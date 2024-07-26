<?php
// 封装接口返回函数
function api($data,int $code,string $msg)
{
    return response([
        'code'  =>  $code,
        'msg'   =>  $msg,
        'data'  =>  $data
    ],200,['Access-Control-Allow-Origin' => '*']);
}


/**
 * 制作密码
 */
function makePassword(string $password){
    return md5(sha1($password).substr(sha1($password), 10, 20));
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

/**
 * 若表单验证失败
 */
function formError($validator)
{
    if ($validator->fails()){
        $error = $validator->errors()->toArrary();
        return api($error,400,'表单验证未通过');
    }
    return true;
}
