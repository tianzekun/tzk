<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;

use app\admin\model\AdminUser;
use app\admin\service\AdminUserService;
use think\Request;
use think\response\Json;
use think\response\View;

class AuthController extends Controller
{


    protected $loginExcept = [
        'admin/auth/login',
        'admin/auth/logout'
    ];

    /**
     * @param Request $request
     * @return Json|View
     */
    public function login(Request $request, AdminUserService $adminUserService)
    {

        if ($request->isPost()) {
            $param = $request->param();

            try {
                $user = $adminUserService->login($param);
                $result = $this->authLogin($user);
                $msg    = '登录成功';
            } catch (\Exception $exception) {
                $result = false;
                $msg    = $exception->getMessage();
            }

            return $result ? success('', $msg) : error($msg);
        }

        return view();
    }


    public function logoutRes()
    {

        echo 'wewe';
    }
}