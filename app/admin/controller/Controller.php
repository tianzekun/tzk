<?php

declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\model\AdminUser;
use app\admin\traits\AdminAuth;
use think\exception\HttpResponseException;
use think\Http;

class Controller
{

    use AdminAuth;

    /**
     * @var AdminUser 当前登录的用户
     */
    protected $user;
    /**
     * @var int 当前用户ID
     */
    protected $user_id;
    /**
     * @var array 无需登录即可访问的URL，请在子类中配置
     */
    protected $loginExcept = [];
    /**
     * @var array 无需验证权限即可访问的URL，请在子类中配置
     */
    protected $authExcept = [];

    /**
     * @var string 用户ID session/cookie的key值
     */
    protected $uid_key;
    /**
     * @var string 用户登录签名 session/cookie的key值
     */
    protected $sign_key;
    /**
     * @var string 当前URL
     */
    protected $url;
    /**
     * @var string 错误信息
     */
    protected $error;

    public function __construct()
    {
        $this->uid_key  = config('auth.uid_key') ?? 'admin_uid';
        $this->sign_key = config('auth.sign_key') ?? 'admin_sign';
        $this->url      =app('http')->getName()  . '/' . parse_name(request()->controller()) . '/' . parse_name(request()->action());

        //初始化
        $this->init();
    }






}