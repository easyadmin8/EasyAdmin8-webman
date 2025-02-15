<?php

namespace app\middleware;

use app\common\services\annotation\MiddlewareAnnotation;
use app\common\traits\JumpTrait;
use ReflectionClass;
use ReflectionException;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class CheckLogin implements MiddlewareInterface
{
    use JumpTrait;

    /**
     * @throws ReflectionException
     */
    public function process(Request $request, callable $handler): Response
    {
        $controller = $request->controller;
        $next       = $handler($request);
        if (empty($controller)) return $next;
        $action    = $request->action;
        $classObj   = new ReflectionClass($controller);
        $properties = $classObj->getDefaultProperties();
        // 整个控制器是否忽略登录
        $ignoreLogin   = $properties['ignoreLogin'] ?? false;
        $adminUserInfo = session('admin');
        if (!$ignoreLogin) {
            $noNeedCheck = $properties['noNeedCheck'] ?? [];
            if (in_array($action, $noNeedCheck)) {
                return $next;
            }
            $reflectionMethod = new \ReflectionMethod($controller, $action);
            $attributes       = $reflectionMethod->getAttributes(MiddlewareAnnotation::class);
            foreach ($attributes as $attribute) {
                $annotation = $attribute->newInstance();
                $_ignore    = (array)$annotation->ignore;
                // 控制器中的某个方法忽略登录
                if (in_array('LOGIN', $_ignore)) return $next;
            }
            if (empty($adminUserInfo)) {
                return $this->responseView('请先登录后台', [], __url("/login"));
            }
            // 判断是否登录过期
            $expireTime = $adminUserInfo['expire_time'];
            if ($expireTime !== true && time() > $expireTime) {
                $request->session()->forget('admin');
                return $this->responseView('登录已过期，请重新登录', [], __url("/login"));
            }
        }
        $request->adminUserInfo = $adminUserInfo ?: [];
        return $next;
    }
}