<?php

declare (strict_types=1);

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
    protected $uid;
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
        //初始化
        $this->init();
    }

    /**
     * 初始化方法
     */
    protected function init(): void
    {
        $app_name   = app('http')->getName();
        $controller = parse_name(request()->controller());
        $action     = parse_name(request()->action());
        $this->url  = $app_name . '/' . $controller . '/' . $action;
        if (!in_array($this->url, $this->loginExcept, true)) {
            if (!$this->isLogin()) {
                unauthorized();
            }
        }

        if(!in_array($this->url, $this->authExcept, true)){
            $check  = $this->authCheck($this->user,$this->url);
            if(!$check){
                forbidden();
            }
        }



    }


}