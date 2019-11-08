<?php
/**
 * 后台操作日志模型
 * @author yupoxiong<i@yufuping.com>
 */

declare (strict_types = 1);

namespace app\admin\model;

/**
 * Class AdminLog
 * @package app\admin\model
 * @property int $id ID
 * @property int $user_id 用户ID
 * @property string $name 操作名称
 * @property string $method 访问方式
 * @property string $path 操作路径
 * @property string $ip IP
 */
class AdminLog extends Model
{
    protected $name = 'admin_log';

    public $softDelete = false;

    public $methodText = [
        1=>'GET',
        2=>'POST',
        3=>'PUT',
        4=>'DELETE',
    ];

    protected $autoWriteTimestamp = true;
    protected $updateTime= false;

    protected $searchField = [
        'name',
        'url',
        'log_ip'
    ];

    protected $whereField = [
        'admin_user_id'
    ];

    /**
     * 关联用户
     * @return \think\model\relation\BelongsTo
     */
    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
    }

    /**
     * 关联日志数据
     * @return \think\model\relation\HasOne
     */
    public function adminLogData()
    {
        return $this->hasOne(AdminLogData::class);
    }

}
