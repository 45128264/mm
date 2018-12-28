<?php

namespace Qyk\Mm\Facade;

/**
 * 错误监测
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class ErrorEventListener extends Facade
{
    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'errorEventListener';
    }

    /**
     * 开始监听
     * @return mixed
     */
    abstract function listen();
}