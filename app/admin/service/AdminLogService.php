<?php
/**
 * 操作日志
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);

namespace app\admin\service;


use app\admin\model\AdminLog;

class AdminLogService extends Service
{

    /**
     * @var AdminLog
     */
    protected $adminLog;

    public function __construct(AdminLog $adminLog)
    {
        $this->adminLog = $adminLog;
    }

    /**
     * @param int $user_id 用户ID
     * @param string $name 行为/操作名称
     * @param string $path 操作路径
     */
    public function create($user_id, $name, $path)
    {
        $this->adminLog::create([
            'user_id' => $user_id,
            'name'    => $name,
            'path'    => $path,
            'method'  => request()->method(),
            'ip'      => request()->ip(),
        ]);
    }
}