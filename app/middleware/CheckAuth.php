<?php

namespace app\middleware;

use app\common\services\annotation\MiddlewareAnnotation;
use app\common\traits\JumpTrait;
use app\common\services\AuthService;
use ReflectionAttribute;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class CheckAuth implements MiddlewareInterface
{
    use JumpTrait;

    /**
     * @desc
     * @param Request $request
     * @param callable $handler
     * @return Response
     * @throws \ReflectionException
     */
    public function process(Request $request, callable $handler): Response
    {
        // 检测 .env 文件，正式环境后可删除
        if (!is_file(base_path() . DIRECTORY_SEPARATOR . ".env")) return $this->error('请配置.env文件');

        $adminConfig         = config('admin');
        $admin_domain_status = $adminConfig['admin_domain_status']; // 是否开启了后台域名绑定功能
        if (!$admin_domain_status) {
            if ($request->host(true) == $adminConfig['admin_domain']) {
                return json(['code' => -1, 'msg' => '访问地址异常']);
            }
        }
        $adminId         = session('admin.id', 0);
        $controllerClass = explode('\\', $request->controller);
        $controller      = strtolower(str_replace('Controller', '', array_pop($controllerClass)));
        try {
            $reflectionClass  = new \ReflectionClass($request->controller);
            $action           = $request->action;
            $checkIgnoreLogin = $reflectionClass->getMethod($action)->getAttributes(MiddlewareAnnotation::class)[0]->newInstance()->ignore;
            // 不需要登录的页面 跳过检测权限
            if (strtolower($checkIgnoreLogin) == 'login') return $handler($request);
        }catch (\Throwable) {
        }
        // 验证权限
        if ($adminId) {
            $authService = new AuthService($adminId);
            $currentNode = $authService->getCurrentNode();
            if (!in_array($controller, $adminConfig['no_auth_controller']) && !in_array($controller, $adminConfig['no_auth_node'])) {
                $check = $authService->checkNode($currentNode);
                if (!$check) return ($request->isAjax() || $request->method() == 'POST') ? $this->error('无权限访问') : $this->responseView('无权限访问');
                // 判断是否为演示环境
                if (env('EASYADMIN.IS_DEMO', false) && $request->method() == 'POST') {
                    if ($currentNode != 'system/log/record') return ($request->isAjax() || $request->method() == 'POST') ? $this->error('演示环境下不允许修改') : $this->responseView('无权限访问');
                }
            }
        }
        return $handler($request);
    }
}
