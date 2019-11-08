<?php

declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\model\AdminUser;
use app\admin\traits\AdminAuth;
use think\exception\HttpResponseException;

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

    public function __construct()
    {
        //初始化
        $this->init();
    }






}