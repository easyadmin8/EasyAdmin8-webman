<?php

namespace app\common\services\annotation;

use Attribute;

/**
 * controller 节点注解类
 */
#[Attribute]
final class ControllerAnnotation
{
    /**
     * @param string $title
     * @param bool $auth 是否需要权限
     * @param string|array $ignore
     */
    public function __construct(public string $title = '', public bool $auth = true, public string|array $ignore = '') {}
}