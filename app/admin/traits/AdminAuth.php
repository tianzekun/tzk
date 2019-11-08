<?php
/**
 * 登录、退出、记录日志相关
 * @author yupoxiong<i@yupoxiong.com>
 */

namespace app\admin\traits;

use app\admin\model\AdminLog;
use app\admin\model\AdminUser;
use app\admin\service\AdminUserService;
use think\facade\Session;
use think\facade\Cookie;

trait AdminAuth
{
    /**
     * @var string 用户sign的cookie和session的key
     */
    protected $cookie_id = 'admin_uid';
    /**
     * @var string  用户ID的cookie和session的key
     */
    protected $cookie_sign = 'admin_sign';

    /**
     * @var string 用户签名
     */
    protected $user_sign = '';


    //是否登录
    protected function isLogin()
    {
        //这里要写个判断数据库的才行

        $user_id = session($this->cookie_id)??cookie($this->cookie_id);
        if ($user_id) {
            $user = AdminUser::find($user_id);

            $service = new AdminUserService($user);

        }


        $user       = false;
        $this->user = &$user;
        if (empty($user_id)) {
            if (Cookie::has(self::$user_id) && Cookie::has(self::$user_id_sign)) {
                $user_id = Cookie::get(self::$user_id);
                $sign    = Cookie::get(self::$user_id_sign);
                $user    = AdminUser::get($user_id);
                if ($user && $user->sign_str === $sign) {
                    Session::set(self::$user_id, $user_id);
                    Session::set(self::$user_id_sign, $sign);
                    return true;
                }
            }
            return false;
        }

        $user = AdminUser::get($user_id);
        if (!$user) {
            return false;
        }
        $this->uid = $user->id;

        return Session::get(self::$user_id_sign) === $user->sign_str;
    }

    /**
     * session 与cookie登录
     * @param $user AdminUser
     * @param bool $remember
     * @return bool
     */
    protected function authLogin($user, $remember = false)
    {
        Session::set(self::$user_id, $user->id);
        Session::set(self::$user_id_sign, $user->sign_str);

        //记住登录
        if ($remember === true) {
            Cookie::set(self::$user_id, $user->id);
            Cookie::set(self::$user_id_sign, $user->sign_str);
        } else if (Cookie::has(self::$user_id) || Cookie::has(self::$user_id_sign)) {
            Cookie::delete(self::$user_id);
            Cookie::delete(self::$user_id_sign);
        }
        //记录登录日志
        self::loginLog($user);
        return true;
    }

    //退出
    protected function authLogout()
    {
        Session::delete(self::$user_id);
        Session::delete(self::$user_id_sign);
        if (Cookie::has(self::$user_id) || Cookie::has(self::$user_id_sign)) {
            Cookie::delete(self::$user_id);
            Cookie::delete(self::$user_id_sign);
        }
        return true;
    }

    /**
     * 权限检查
     * @param $user AdminUser
     * @return bool
     */
    public function authCheck($user)
    {
        return in_array($this->url, $this->authExcept, true) || in_array($this->url, $user->auth_url, true);
    }

    //登录记录
    protected function loginLog($user)
    {
        $data = AdminLog::create([
            'user_id'    => $user->id,
            'name'       => '登录',
            'url'        => 'admin/auth/login',
            'log_method' => 'POST',
            'log_ip'     => request()->ip()
        ]);

        $crypt_data = Crypt::encrypt(json_encode(request()->param()), config('app.app_key'));
        $log_data   = [
            'data' => $crypt_data
        ];
        $data->adminLogData()->save($log_data);
    }

    //创建操作日志
    public function createAdminLog($user, $menu)
    {
        $data = [
            'user_id'    => $user->id,
            'name'       => $menu->name,
            'log_method' => $menu->log_method,
            'url'        => request()->pathinfo(),
            'log_ip'     => request()->ip()
        ];
        $log  = AdminLog::create($data);

        //加密数据，防脱库
        $crypt_data = Crypt::encrypt(json_encode(request()->param()), config('app.app_key'));
        $log_data   = [
            'data' => $crypt_data
        ];
        $log->adminLogData()->save($log_data);
    }
}