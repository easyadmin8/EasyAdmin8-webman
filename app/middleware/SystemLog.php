<?php

namespace app\middleware;

use app\common\services\annotation\ControllerAnnotation;
use app\common\services\annotation\MiddlewareAnnotation;
use app\common\services\annotation\NodeAnnotation;
use app\common\services\SystemLogService;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 系统操作日志中间件
 * Class SystemLog
 * @package app\admin\middleware
 */
class SystemLog implements MiddlewareInterface
{

    /**
     * 敏感信息字段，日志记录时需要加密
     * @var array
     */
    protected array $sensitiveParams = [
        'password',
        'password_again',
        'phone',
        'mobile',
    ];

    public function process(Request $request, callable $handler): Response
    {
        $response = $handler($request);
        if (!env('APP_ADMIN_SYSTEM_LOG', true)) return $response;
        if ($request->isAjax()) {
            $params = $request->all();
            if (isset($params['s'])) unset($params['s']);
            foreach ($params as $key => $val) {
                in_array($key, $this->sensitiveParams) && $params[$key] = "***********";
            }
            $method = strtolower($request->method());
            $url    = $request->path();
            if (in_array($method, ['post', 'put', 'delete'])) {
                $title = '';
                try {
                    $_controller = $request->controller;
                    $_action     = $request->action;
                    if ($_controller && $_action) {
                        $reflectionMethod = new \ReflectionMethod($_controller, $_action);
                        $attributes       = $reflectionMethod->getAttributes(MiddlewareAnnotation::class);
                        foreach ($attributes as $attribute) {
                            $annotation = $attribute->newInstance();
                            $_ignore    = (array)$annotation->ignore;
                            if (in_array('log', array_map('strtolower', $_ignore))) return $response;
                        }
                        $controllerTitle      = $nodeTitle = '';
                        $controllerAttributes = (new \ReflectionClass($_controller))->getAttributes(ControllerAnnotation::class);
                        $actionAttributes     = $reflectionMethod->getAttributes(NodeAnnotation::class);
                        foreach ($controllerAttributes as $controllerAttribute) {
                            $controllerAnnotation = $controllerAttribute->newInstance();
                            $controllerTitle      = $controllerAnnotation->title ?? '';
                        }
                        foreach ($actionAttributes as $actionAttribute) {
                            $actionAnnotation = $actionAttribute->newInstance();
                            $nodeTitle        = $actionAnnotation->title ?? '';
                        }
                        $title = $controllerTitle . ' - ' . $nodeTitle;
                    }
                }catch (\Throwable $exception) {
                }
                $ip = $request->getRealIp(true);
                // 限制记录的响应内容，避免过大
                $_response = $response->rawBody();
                $_response = mb_substr($_response, 0, 3000, 'utf-8');
                $data      = [
                    'admin_id'    => session('admin.id'),
                    'title'       => $title,
                    'url'         => $url,
                    'method'      => $method,
                    'ip'          => $ip,
                    'content'     => json_encode($params, JSON_UNESCAPED_UNICODE),
                    'response'    => $_response,
                    'useragent'   => $request->header('user-agent'),
                    'create_time' => time(),
                ];
                SystemLogService::instance()->setTableName()->save($data);
            }
        }
        return $response;
    }

}
