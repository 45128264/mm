<?php

namespace Qyk\Mm;

use Closure;
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
     * 脚本结束后的，后续操作，比如释放连接
     * @var []
     */
    public $terminateContainer = [];

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


    /**
     * 注册，脚本结束的关闭任务
     * @param string  $abstract
     * @param Closure $callback
     */
    public function bindTerminate(string $abstract, Closure $callback)
    {
        $this->terminateContainer[$abstract] = $callback;
    }

    /**
     * 清除任务
     * @param string $abstract
     */
    public function unsetTerminate(string $abstract)
    {
        if (isset($this->terminateContainer[$abstract])) {
            unset($this->terminateContainer[$abstract]);
        }
    }


}