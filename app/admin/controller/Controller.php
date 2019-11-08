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



    public function __construct()
    {
        $this->init();
    }



    public function init(){

        //判断登录
        if(!$this->isLogin()){
            throw new  HttpResponseException(unauthorized());
        }


    }


}