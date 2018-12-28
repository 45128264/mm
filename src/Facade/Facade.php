<?php

namespace Qyk\Mm\Facade;

use app\core\Application;

/**
 * 公用服务接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Facade
{
    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    abstract public function getName(): string;

    /**
     * 获取当前Facade对应别名
     * @return string
     */
    abstract protected function getFacadeAliasName(): string;
}