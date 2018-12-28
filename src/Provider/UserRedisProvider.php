<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\User;

/**
 * 用户服务
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class UserRedisProvider extends User
{
    /**
     * @var CacheRedisProvider
     */
    protected $cache;
    /**
     * 用户数据
     * @var array
     */
    private $userInfo = [];

    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'user_redis';
    }

    /**
     * 判断用户的访问权限
     * @return bool
     */
    public function validatePermission(): bool
    {
        $this->init();
        //检测controller是否有访问权限
        $this->checkControllerPermission();
        return true;
    }

    /**
     * 初始化用户数据
     */
    protected function init()
    {
        if ($this->userInfo) {
            return;
        }
        //region init cache
        if ($this->app->cache->getName() == 'cache_redis') {
            $this->cache = $this->app->cache;
        } else {
            $this->cache = new CacheRedisProvider();
        }
        //endregion
        $userToken = $_COOKIE[$this->getSessionKey()] ?? null;
        if (!$userToken) {
            return;
        }

    }

    /**
     * 检测controller是否有访问权限
     */
    protected function checkControllerPermission()
    {
        //首先判断对应的模块是否需要角色权限
        $key = $this->app->router->currentController . '.' . $this->app->router->currentMethod;
        if (!$this->cache->exists($key)) {
            return;
        }
        //如果用户角色没有权限
        if (!$this->cache->sIsMember($key, $this->getUserInfoByKey('roleId'))) {
            //todo
            echo '404';
            exit;
        }
    }

    /**
     * 获取用户数据
     * @param $name
     * @return mixed
     */
    private function getUserInfoByKey($name)
    {
        if (isset($this->userInfo[$name])) {
            return $this->userInfo[$name];
        }
        if (!$this->cache->hExists($this->app->config->get('cache.key.userInfo') . '_' . $this->userId, $name)) {
            return false;
        }
        $val                   = $this->cache->hget($this->app->config->get('cache.key.userInfo') . '_' . $this->userId, $name);
        $this->userInfo[$name] = $val;
        return $val;
    }


    /**
     * 获取缓存的key名称
     */
    protected function getSessionKey()
    {
        if (is_null($this->tokenKey)) {
            $this->tokenKey = $this->app->config->get('cache.key.userToken');
        }
        return $this->tokenKey;
    }
}