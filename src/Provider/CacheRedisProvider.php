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