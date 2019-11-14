<?php

declare (strict_types=1);

namespace app\admin\controller;

use app\admin\model\AdminMenu;
use app\admin\model\AdminUser;
use app\admin\traits\AdminAuth;
use app\admin\traits\AdminTree;
use think\facade\Env;
use think\facade\View;

class Controller
{

    use AdminAuth,AdminTree;

    /**
     * @var AdminUser 当前登录的用户
     */
    protected $user;
    /**
     * @var int 当前用户ID
     */
    protected $uid;
    /**
     * @var array 无需登录即可访问的URL，请在子类中配置
     */
    protected $loginExcept = [];
    /**
     * @var array 无需验证权限即可访问的URL，请在子类中配置
     */
    protected $authExcept = [];
    /**
     * 后台主变量,主要存后台的以下信息：
     * debug，标题，分页信息，
     * @var array
     */
    protected $admin = [];


    public function __construct()
    {
        //初始化
        $this->authInit();
        $this->adminInit();
    }


    protected function adminInit()
    {
        if ((int)request()->param('check_auth') === 1) {
            success();
        }


        //记录日志
        $menu = AdminMenu::where('url' ,'=', $this->url)->find();
        if ($menu) {
            $this->admin['title'] = $menu->name;
            //$this->createLog($this->uid, $menu);
        }

        $this->admin['per_page'] = cookie('admin_per_page') ?? 10;
        $this->admin['per_page'] = $this->admin['per_page'] < 100 ? $this->admin['per_page'] : 100;
    }

    protected function fetch($template = ''): string
    {

        $this->admin['title'] = 'Admin';
        $this->admin['name'] = config('admin.base.name');
        $this->admin['pjax'] = request()->isPjax();

        if (!$this->admin['pjax'] && 'admin/auth/login' !== $this->url) {
            $this->admin['menu'] = $this->getLeftMenu($this->user);
        }



        $this->assign([
            'admin'=>$this->admin,
            'debug'=> Env::get('APP_DEBUG')?'true':'false',
            'cookie_prefix' => config('cookie.prefix') ?? '',

        ]);

        return View::fetch($template);
    }

    protected function assign($name, $value = null)
    {
       return View::assign($name,$value);
    }


}