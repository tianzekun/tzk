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
    public function login($username, $password, $remember = 0)
    {
        try {
            $user = $this->adminUser->where('user_name', $username)->find();
            if (!$user) {
                exception('用户不存在');
            }

            $verify = password_verify($password, base64_decode($user->password));
            if ($verify) {
                exception('密码错误');
            }

            $uid_key = config('auth.uid_key') ?? 'admin_uid';
            session($uid_key, $user->id);
            if ($remember) {
                $sign     = $this->getSign($user);
                $sign_key = config('auth.sign_key') ?? 'admin_sign';
                cookie($uid_key, $user->id);
                cookie($sign_key, $sign);
            }

            return true;

        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }

    }

    /**
     * @param int $user_id
     * @param string $sign
     */
    public function isLogin()
    {
        try {

            $uid_key = config('auth.uid_key') ?? 'admin_uid';
            $user_id = session($uid_key);
            //如果有session，返回true即可
            if ($user_id) {
                return true;
            }

            $sign_key = config('auth.sign_key') ?? 'admin_sign';
            $user_id  = cookie($uid_key);
            $sign     = cookie($sign_key);
            //如果连cookie也没有，直接返回false
            if (!$user_id || $sign) {
                return false;
            }

            $user      = $this->adminUser->find($user_id);
            $user_sign = $this->getSign($user);
            return $user_sign === $sign;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * 获取sign
     * @param AdminUser $user
     * @return string
     */
    protected function getSign($user)
    {
        $ua = request()->header('user-agent');
        return sha1($user->id . $user->username . $ua);
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