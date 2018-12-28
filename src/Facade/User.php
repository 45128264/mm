<?php

namespace Qyk\Mm\Facade;

/**
 * 用户服务接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class User extends Facade
{
    protected $tokenKey;        //token对应的编号
    public    $userId;          //用户编号

    /**
     * 判断用户的访问权限
     * @return bool
     */
    abstract public function validatePermission(): bool;

    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'user';
    }
}