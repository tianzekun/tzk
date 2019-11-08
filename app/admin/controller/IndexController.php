<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\Request;

class IndexController extends Controller
{



    public function index(Request $request)
    {

        $controller = $request->controller();
        $action = $request->action();

        dump($controller);
        dump($action);

        return view('admin_user/index');
    }

}