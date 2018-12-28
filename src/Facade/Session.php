<?php

namespace Qyk\Mm\Facade;

/**
 * session接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Session extends Facade
{
    /**
     * 获取当前Facade对应别名
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'session';
    }
}