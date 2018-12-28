<?php

namespace Qyk\Mm\Facade;

/**
 * config接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Config extends Facade
{
    /**
     * 获取指定config文件类型下的key数据
     * @param        $cacheKey 配置文件/组合，（master/ master.host)
     * @param string $key    查找的数据key, 类似host
     * @return mixed
     */
    abstract public function get($cacheKey, string $key = null);

    /**
     * 获取当前Facade对应别名
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'config';
    }

}