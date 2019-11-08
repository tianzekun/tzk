<?php
/**
 * 后台用户
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace app\admin\service;


use app\admin\model\AdminUser;

class AdminUserService extends Service
{

    protected $adminUser;

    public function __construct(AdminUser $adminUser)
    {
        $this->adminUser = $adminUser;
    }


    /**
     * 创建用户
     */
    public function create($user_data,$role_data = null)
    {
        $user = $this->adminUser::create($user_data);
    }

    /**
     * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return bool
     */
    public function login($param, $remember = 0)
    {

    }





    public function createLog()
    {

    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

}