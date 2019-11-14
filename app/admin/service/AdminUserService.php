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
     * @param $param
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \utils\AppException
     */
    public function login($param)
    {
        $username = $param['username'];
        $password = $param['password'];
        $user     = $this->adminUser->where('user_name', $username)->find();
        if (!$user) {
            exception('用户不存在');
        }
        $verify = password_verify($password, base64_decode($user->password));
        if ($verify) {
            exception('密码错误');
        }
        return $user;
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