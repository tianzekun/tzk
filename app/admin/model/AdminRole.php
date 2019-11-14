<?php
/**
 * 后台角色模型
 * @author yupoxiong<i@yufuping.com>
 */

declare (strict_types = 1);

namespace app\admin\model;

use think\Model;

/**
 * Class AdminRole
 * @package app\admin\model
 * @property int $id
 * @property array|string $url
 */
class AdminRole extends Model
{
    protected $name = 'admin_role';

    public $softDelete = false;

    protected $searchField = [
        'name',
        'description'
    ];

    public $noDeletionId = [
        1
    ];

    /**
     * @param AdminRole $model
     * @return mixed|void
     */
    public static function onBeforeInsert($model)
    {
        $model->url = [1,2,18];
    }


    protected function getUrlAttr($value)
    {
        return $value !== '' ? explode(',', $value) : [];
    }

    protected function setUrlAttr($value)
    {
        return $value !== '' ? implode(',', $value) : [];
    }
}
