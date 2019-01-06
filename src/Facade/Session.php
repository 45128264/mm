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

    /**
     * 设置cookie
     * @param $key
     * @param $value
     * @param int $expire
     * @param string $path
     * @param null $domain
     */
    public function setCookie($key, $value, $expire=86400, $path='/', $domain=null)
    {
        setcookie($key, $value, time()+$expire, $path, $domain);
    }
}