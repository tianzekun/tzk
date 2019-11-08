<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;



use app\admin\service\AdminUserService;
use think\Request;

class AuthController
{


    /**
     * @param Request $request
     */
    public function login(Request $request,AdminUserService $service)
    {
        $param = $request->param();
        $service->create($param);


    }


    public function logout()
    {

    }
}