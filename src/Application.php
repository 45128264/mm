<?php

namespace Qyk\Mm;

use Qyk\Mm\Facade\Cache;
use Qyk\Mm\Facade\Config;
use Qyk\Mm\Facade\Request;
use Qyk\Mm\Facade\Response;
use Qyk\Mm\Facade\Session;
use Qyk\Mm\Facade\User;
use Qyk\Mm\Provider\CacheRedisProvider;
use Qyk\Mm\Provider\ConfigProvide;
use Qyk\Mm\Provider\LogProvider;
use Qyk\Mm\Provider\RequestProvider;
use Qyk\Mm\Provider\ResponseProvider;
use Qyk\Mm\Provider\SessionProvider;
use Qyk\Mm\Provider\UserRedisProvider;

/**
 * app模块
 * Class Application
 * @package Qyk\Mm
 * @package mm
 * @property Cache    $cache
 * @property User     $user
 * @property Session  $session
 * @property Request  $request
 * @property Response $response
 * @property Config   $config
 */
class Application
{
    /**
     * 依赖容器
     * @var Container
     */
    private $container;

    /**
     * 获取服务
     * @param $provider
     * @return mixed
     * @throws \ReflectionException
     */
    final public function __get($provider)
    {
        return $this->container->make($provider);
    }

    /**
     * 初始化
     */
    protected function init()
    {
        if ($this->container) {
            return;
        }
        $this->container = new Container();

        /**
         * 默认的服务
         * @var array
         */
        $defaultProvider = [
            'cache'    => CacheRedisProvider::class,
            'user'     => UserRedisProvider::class,
            'session'  => SessionProvider::class,
            'request'  => RequestProvider::class,
            'response' => ResponseProvider::class,
            'config'   => ConfigProvide::class,
            'log'      => LogProvider::class,
        ];
        $provider        = array_merge($defaultProvider, $this->provider());
        foreach ($provider as $abstract => $concrete) {
            $this->container->bind($abstract, $concrete);
        }
    }

    /**
     * 自定义服务模块
     * @return array
     */
    protected function provider()
    {
        return [];
    }


}