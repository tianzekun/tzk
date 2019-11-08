<?php
declare (strict_types = 1);

namespace app\index\controller;

class IndexController
{

    public function index()
    {
        return 'index-index';

    }

    public function test()
    {
        cookie('test',null);
        session('test','dfadfadsf');
        dump(session('test'));
        dump(cookie('test'));
    }

    public function test1(){



        $a = session('test')??cookie('test');
        dump($a);



        dump(session('test'));
        dump(cookie('test'));
    }
}