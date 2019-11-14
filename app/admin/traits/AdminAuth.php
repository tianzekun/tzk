<?php
/**
 * 登录、退出、记录日志相关
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace app\admin\traits;

use app\admin\model\AdminLog;
use app\admin\model\AdminLogData;
use app\admin\model\AdminMenu;
use app\admin\model\AdminRole;
use app\admin\model\AdminUser;

use think\exception\HttpResponseException;
use think\facade\Db;

trait AdminAuth
{

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


    /**
     * 初始化方法
     */
    protected function authInit(): void
    {
        //初始化uid和sign的key
        $this->uid_key  = config('admin.auth.uid_key') ?? 'admin_uid';
        $this->sign_key = config('admin.auth.sign_key') ?? 'admin_sign';

        //获取当前的url
        $app_name   = strtolower(app('http')->getName());
        $controller = parse_name(request()->controller());
        $action     = strtolower(request()->action());
        $this->url  = $app_name . '/' . $controller . '/' . $action;

        //登录排除URL处理

        //检查登录
        if (!in_array($this->url, $this->parseUrl($this->loginExcept), true)) {
            if (!$this->isLogin()) {
                throw new HttpResponseException(redirect('/admin/auth/login'));
            }

            //检查权限
            if (!in_array($this->url, $this->parseUrl($this->authExcept), true)) {
                $check = $this->authCheck($this->user, $this->url);
                if (!$check) {
                    throw new HttpResponseException(forbidden());
                }
            }
        }

    }

    /**
     * 转换url为统一格式，防止开发人员手误填错导致无法验证
     * @param string|array $url
     * @return mixed
     */
    protected function parseUrl($url)
    {
        if (is_string($url)) {
            $tmp_arr = explode('/', $url);
            $url     = strtolower($tmp_arr[0] ?? '') . '/' . parse_name($tmp_arr[1] ?? '') . '/' . strtolower($tmp_arr[2] ?? '');
        } else if (count($url) > 0) {
            foreach ($url as $key => $value) {
                $tmp_arr   = explode('/', $value);
                $url[$key] = strtolower($tmp_arr[0] ?? '') . '/' . parse_name($tmp_arr[1] ?? '') . '/' . strtolower($tmp_arr[2] ?? '');
            }
        }
        return $url;
    }


    /**
     * 是否登录
     * @return bool
     */
    protected function isLogin(): ?bool
    {
        try {
            $user_id = session($this->uid_key);
            //如果有session，返回true即可
            if ($user_id) {
                $this->user = AdminUser::find($user_id);
                return true;
            }

            $user_id = cookie($this->uid_key);
            $sign    = cookie($this->sign_key);
            //如果连cookie也没有，直接返回false
            if (!$user_id || $sign) {
                return false;
            }

            $this->user = AdminUser::find($user_id);
            $user_sign  = $this->getSign($this->user);

            return $user_sign === $sign;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * 获取sign
     * @param AdminUser|mixed $user
     * @return string
     */
    protected function getSign($user): string
    {
        $ua = request()->header('user-agent');
        return sha1($user->id . $user->username . $ua);
    }

    /**
     * session 与cookie登录
     * @param $user AdminUser
     * @param bool $remember
     * @return bool
     */
    protected function authLogin($user, $remember = false): ?bool
    {
        session($this->uid_key, $user->id);
        if ($remember) {
            $sign = $this->getSign($user);
            cookie($this->uid_key, $user->id, 1314520);
            cookie($this->sign_key, $sign, 1314520);
        }

        $this->createLog($user->id, '登录');
        return true;
    }

    /**
     * 退出登录
     * @return bool
     */
    protected function authLogout(): bool
    {
        session($this->uid_key, null);
        cookie($this->uid_key, null);
        cookie($this->sign_key, null);
        return true;
    }

    /**
     * 权限检查
     * @param AdminUser $user
     * @param string $url
     * @return bool
     */
    public function authCheck($user, $url): bool
    {
        return in_array($url, $this->getUserAuthUrl($user), true);
    }

    /**
     * @param int $user_id 用户ID
     * @param string $name 操作名称
     * @return bool
     */
    protected function createLog($user_id, $name): bool
    {
        Db::startTrans();
        try {
            $adminLog              = new AdminLog;
            $adminLog->user_id     = $user_id;
            $adminLog->name        = $name;
            $adminLog->method      = request()->method();
            $adminLog->path        = request()->pathinfo();
            $adminLog->ip          = request()->ip();
            $adminLogData          = new AdminLogData;
            $adminLogData->content = json_encode(request()->param());
            $adminLog->together(['adminLogData'])->save();

            Db::commit();
            $result = true;
        } catch (\Exception $exception) {
            Db::rollback();
            $result      = false;
            $this->error = $exception->getMessage();
        }

        return $result;
    }

    /**
     * 获取用户已授权的URL
     * @param AdminUser $user
     * @return array
     */
    protected function getUserAuthUrl($user): array
    {
        $role_urls = (new AdminRole)
            ->whereIn('id', $user->role)
            ->where('status', 1)
            ->column('url');

        $url_id_str = '';
        foreach ($role_urls as $key => $val) {
            $url_id_str .= $key === 0 ? $val : ',' . $val;
        }
        $url_id   = array_unique(explode(',', $url_id_str));
        $auth_url = [];
        if (count($url_id) > 0) {
            $auth_url = (new AdminMenu)->whereIn('id', $url_id)->column('url');
        }
        return $this->parseUrl($auth_url);
    }
}