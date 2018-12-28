<?php

namespace Qyk\Mm\Facade;

/**
 * 缓存接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Cache extends Facade
{
    /**
     * 获取当前Facade对应别名
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'cache';
    }

    /**
     * 是否存在,类似redis hexists
     * @param $key
     * @param $field
     * @return bool
     */
    abstract public function hExists($key, $field): bool;

    /**
     * 是否存在,类似redis exists
     * @param $key
     * @return bool
     */
    abstract public function exists($key): bool;

    /**
     * 判断 filed 元素是否是集合 key 的成员,类似redis 的sismember
     * @param $key
     * @param $field
     * @return bool
     */
    abstract public function sIsMember($key, $field): bool;

    /**
     * 类似redis hget
     * @param $key
     * @param $field
     * @return bool
     */
    abstract public function hGet($key, $field): string;
}