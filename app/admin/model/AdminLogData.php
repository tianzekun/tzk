<?php
/**
 * 后台管理员操作日志数据模型
 * @author yupoxiong<i@yufuping.com>
 */

declare (strict_types = 1);

namespace app\admin\model;


/**
 * Class AdminLogData
 * @package app\admin\model
 * @property int $id ID
 * @property int $admin_log_id 日志ID
 * @property string $content 日志数据内容
 */
class AdminLogData extends Model
{

    public $softDelete = false;

    /**
     * 关联日志
     * @return \think\model\relation\BelongsTo
     */
    public function adminLog()
    {
        return $this->belongsTo(AdminLog::class);
    }

    
}
