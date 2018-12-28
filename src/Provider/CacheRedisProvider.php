<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Cache;

/**
 * 缓存
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class CacheRedisProvider extends Cache
{
    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'cache_redis';
    }

    public function hExists($key, $field): bool
    {
        return true;
    }

    public function exists($key): bool
    {
        return true;
    }

    public function sIsMember($key, $field): bool
    {
        return true;
    }

    public function hGet($key, $field): string
    {
        return '';
    }


}