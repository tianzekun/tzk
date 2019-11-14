<?php
/**
 *
 * @author yupoxiong<i@yupoxiong.com>
 */

declare (strict_types=1);


namespace app\admin\controller;

use app\admin\model\AdminUser;
use app\admin\validate\AdminUserValidate;
use think\captcha\Captcha;
use think\Request;
use tools\GeeTest;


class AuthController extends Controller
{


    protected $loginExcept = [
        'admin/auth/login',
        'admin/auth/logout',
        'admin/auth/captcha',
        'admin/auth/test',
        'admin/auth/initGeeTest',
    ];

    //登录
    public function login(Request $request, AdminUser $adminUser, AdminUserValidate $validate)
    {

        if ($request->isPost()) {
            $config = config('admin.login');
            $param  = $request->param();
            //如果需要验证码
            switch ($config['captcha']) {
                case 1:
                    if (!captcha_check($param['captcha'])) {
                        return error('验证码错误');
                    }
                    break;
                case 2:
                    $geeTest = new GeeTest(config('gee_test.id'), config('gee_test.key'));
                    $data = array(
                        'user_id'     => session('gt_uid'),
                        'client_type' => 'web',
                        'ip_address'  => $request->ip(),
                    );
                    if (session('gt_server') == 1) {
                        $gee_test_result = $geeTest->successValidate($param['geetest_challenge'], $param['geetest_validate'], $param['geetest_seccode'], $data);
                        if (!$gee_test_result) {
                            return error('验证失败，请刷新重试');
                        }
                    } else {
                        if (!$geeTest->failValidate($param['geetest_challenge'], $param['geetest_validate'])) {
                            return error('验证失败，请刷新重试');
                        }
                    }
                    break;
                default:
                    break;
            }

            $validate_result = $validate->scene('login')->check($param);
            if (!$validate_result) {
                return error($validate->getError());
            }

            //如果需要验证登录token
            if ($config['token']) {
                $token_validate        = \think\Validate::make();
                $token_validate_result = $token_validate->rule('__token__', 'token')
                    ->check($param);
                if (!$token_validate_result) {
                    return error($token_validate->getError());
                }
            }


            try {
                $user   = $adminUser->login($param);
                $result = $this->authLogin($user);
                $msg    = '登录成功';
            } catch (\Exception $exception) {
                $result = false;
                $msg    = $exception->getMessage();
            }

            return $result ? success('','/admin/index/index', $msg) : error($msg);
        }

        $this->assign([
            //登录设置，参考/config/admin/admin.php文件配置
            'login_config' => config('admin.login'),
        ]);
        return $this->fetch();
    }

    //退出
    public function logout()
    {
        $this->authLogout();
        return redirect('/admin/auth/login');
    }


    //极验初始化
    public function initGeeTest(Request $request)
    {
        $geeTest =  new GeeTest(config('gee_test.id'), config('gee_test.key'));

        $ip = $request->ip();
        $ug = $request->header('user-agent');

        $data = array(
            'gt_uid'      => md5($ip . $ug),
            'client_type' => 'web',
            'ip_address'  => $ip,
        );

        $status = $geeTest->preProcess($data);

        session('gt_server', $status);
        session('gt_uid', $data['gt_uid']);

        return success($status, URL_CURRENT, $geeTest->getResponse());
    }


    //ThinkPHP 图形验证码
    public function captcha(Captcha $captcha): \think\Response
    {
        return $captcha->create('login');
    }


}