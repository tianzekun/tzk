<?php
/**
 * 程序公共函数（相当于助手函数）
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types = 1);

use think\response\Json;
use tools\AppException;

if (!function_exists('success')) {
    /**
     * @param array $data 返回的数据主体
     * @param string $msg 返回消息
     * @param int $code 状态码
     * @param array $header 响应头
     * @param array $options 附加参数
     * @return Json
     */
    function success($data = [], $msg = 'SUCCESS', $code = 200, $header = [], $options = []): Json
    {
        return result($data, $msg, $code, $header, $options);
    }
}

if (!function_exists('error')) {
    /**
     * @param string $msg 返回消息
     * @param array $data 返回的数据主体
     * @param int $code 状态码
     * @param array $header 响应头
     * @param array $options 附加参数
     * @return Json
     */
    function error($msg = 'FAIL', $data = [], $code = 500, $header = [], $options = []): Json
    {
        return result($data, $msg, $code, $header, $options);
    }
}

if (!function_exists('unauthorized')) {
    /**
     * 未授权，客户端需重新发起登录流程
     * @param string $msg 返回消息
     * @param array $data 返回的数据主体
     * @param int $code 状态码
     * @param array $header 响应头
     * @param array $options 附加参数
     * @return Json
     */
    function unauthorized($msg = 'Unauthorized', $data = [], $code = 401, $header = [], $options = []): Json
    {
        return result($data, $msg, $code, $header, $options);
    }
}

if (!function_exists('forbidden')) {
    /**
     * 当前操作的用户权限不足
     * @param string $msg 返回消息
     * @param array $data 返回的数据主体
     * @param int $code 状态码
     * @param array $header 响应头
     * @param array $options 附加参数
     * @return Json
     */
    function forbidden($msg = 'Forbidden', $data = [], $code = 403, $header = [], $options = []): Json
    {
        return result($data, $msg, $code, $header, $options);
    }
}

if (!function_exists('error_404')) {
    /**
     * 资源或接口不存在
     * @param string $msg 返回消息
     * @param array $data 返回的数据主体
     * @param int $code 状态码
     * @param array $header 响应头
     * @param array $options 附加参数
     * @return Json
     */
    function error_404($msg = 'Not Found', $data = [], $code = 404, $header = [], $options = []): Json
    {
        return result($data, $msg, $code, $header, $options);
    }
}


if (!function_exists('result')) {
    /**
     * 返回json结果
     * @param array $data 返回的数据主体
     * @param string $msg 返回消息
     * @param int $code 状态码
     * @param array $header 响应头
     * @param array $options 附加参数
     * @return Json
     */
    function result($data = [], $msg = 'FAIL', $code = 500, $header = [], $options = []): Json
    {
        $body = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        return json($body, $code, $header, $options);
    }
}

if(!function_exists('exception')){
    /**
     * 抛出异常
     * @param string $msg 错误信息
     * @throws AppException
     */
    function exception($msg='error')
    {
        throw new AppException($msg);
    }
}