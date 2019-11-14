<?php
/**
 * 后台用户模型
 * @author yupoxiong<i@yufuping.com>
 */

declare (strict_types=1);

namespace app\admin\model;

use Exception;
use think\model\concern\SoftDelete;
use think\model\relation\HasMany;

/**
 * @property int id ID
 * @property string username 用户名
 * @property string password 密码
 * @property string nickname 昵称
 * @property string avatar 头像
 * @property int status 状态：1启用，0禁用
 * @property int create_time 创建时间
 * @property int update_time 更新时间
 * @property int delete_time 删除时间
 * @property mixed role
 */
class AdminUser extends Model
{
    use SoftDelete;

    protected $searchField = [
        'nickname',
        'username'
    ];

    public $noDeletionId = [
        1, 2
    ];

    /**
     * 新增前处理密码
     * @param AdminUser $model
     * @return mixed|void
     */
    public static function onBeforeInsert($model)
    {
        $model->password = base64_encode(password_hash($model->password, 1));
    }

    /**
     * 更新前处理密码
     * @param AdminUser $model
     * @return mixed|void
     */
    public static function onBeforeUpdate($model)
    {
        $old = (new static())->find($model->id);
        if ($model->password !== $old->password) {
            $model->password = base64_encode(password_hash($model->password, 1));
        }
    }

    /**
     * @return HasMany
     */
    public function adminLog(): HasMany
    {
        return $this->hasMany(AdminLog::class);
    }

    /**
     * 角色获取器
     * @param $value
     * @return array
     */
    protected function getRoleAttr($value): array
    {
        return explode(',', $value);
    }

    /**
     * 角色修改器
     * @param $value
     * @return string
     */
    protected function setRoleAttr($value): string
    {
        return implode(',', $value);
    }

    /**
     * 用户角色名称
     * @param $value
     * @param $data
     * @return array
     */
    protected function getRoleTextAttr($value, $data): array
    {
        return (new AdminRole)->whereIn('id', $data['role'])->column('id,name', 'id');
    }


    /**
     * 获取已授权url
     * @param $value
     * @param $data
     * @return array
     */
    protected function getAuthUrlAttr($value, $data): array
    {
        $role_urls  = (new AdminRole)->whereIn('id', $data['role'])->where('status', 1)->column('url');
        $url_id_str = '';
        foreach ($role_urls as $key => $val) {
            $url_id_str .= $key === 0 ? $val : ',' . $val;
        }
        $url_id   = array_unique(explode(',', $url_id_str));
        $auth_url = [];
        if (count($url_id) > 0) {
            $auth_url = (new AdminMenu)->whereIn('id', $url_id)->column('url');
        }
        return $auth_url;
    }

    /**
     * 加密字符串，用在登录的时候加密处理
     * @param $value
     * @param $data
     * @return string
     */
    protected function getSignStrAttr($value, $data): string
    {
        $ua = request()->header('user-agent');
        return sha1($data['id'] . $data['username'] . $ua);
    }

    /**
     * 获取当前用户已授权的显示菜单
     * @return array
     */
    public function getShowMenu(): array
    {
        if ($this->id === 1) {
            return (new AdminMenu)->where('is_show', 1)
                ->order('sort_id', 'asc')
                ->order('id', 'asc')
                ->column('id,parent_id,name,url,icon,sort_id', 'id');
        }

        $role_urls = (new AdminRole)->whereIn('id', $this->role)
            ->where('status', 1)
            ->column('url');

        $url_id_str = '';
        foreach ($role_urls as $key => $val) {
            $url_id_str .= $key === 0 ? $val : ',' . $val;
        }

        $url_id = array_unique(explode(',', $url_id_str));
        return (new AdminMenu)->whereIn('id', $url_id)->where('is_show', 1)
            ->order('sort_id', 'asc')
            ->order('id', 'asc')
            ->column('id,parent_id,name,url,icon,sort_id', 'id');
    }

    /**
     * 用户登录
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function login($param)
    {
        $username = $param['username'];
        $password = $param['password'];
        $user     = $this->where('username', $username)->find();
        if (!$user) {
            exception('用户不存在');
        }
        $verify = password_verify($password, base64_decode($user->password));
        if (!$verify) {
            exception('密码错误');
        }
        return $user;
    }
}
