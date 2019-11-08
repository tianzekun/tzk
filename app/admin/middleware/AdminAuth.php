<?php
declare (strict_types=1);

namespace app\admin\middleware;

class AdminAuth
{
    /**
     * @param \think\Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Redirect
     */
    public function handle($request, \Closure $next)
    {
        $path = $request->pathinfo();

        return $next($request);
    }
}