<?php

namespace Qyk\Mm;

use Closure;

/**
 * 路由实体
 * Class RouterFactory
 * @package Qyk\Mm
 *
 * @method static post(string $uri, $action = null)
 */
class Router
{
    /**
     * uri
     * @var string
     */
    protected $uri;
    /**
     * 对应路由对应得请求类型
     * @var array
     */
    protected $methods;
    /**
     * 路由详情
     * @var array
     */
    protected $action;
    protected $defaults;
    protected $parameters;
    /**
     * 中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 命名空间
     * @var array
     */
    protected $namespace = [];
    /**
     * 路由容器
     * @var RouterContainer
     */
    protected $container;
    /**
     * uri解析出来的参数
     * @var []
     */
    protected $uriParams;

    /**
     * 实例化一个路由
     * Router constructor.
     * @param array|string $methods
     * @param string $uri
     * @param Closure|array $action
     */
    public function __construct($methods, string $uri, $action)
    {
        $this->uri     = $uri;
        $this->methods = (array)$methods;
        $this->action  = $this->parseAction($action);
    }

    /**
     * 添加额外属性
     * @param array $attribute
     */
    public function bindParameters(array $attribute)
    {
        if (isset($attribute['prefix'])) {
            $prefix    = trim($attribute['prefix'], '/');
            $this->uri = $prefix ? $prefix . '/' . $this->uri : $this->uri;
        }
        if (isset($attribute['namespace'])) {
            $this->namespace = $attribute['namespace'];
        }
        if (isset($attribute['middleware'])) {
            $this->middleware = $attribute['middleware'];
        }
    }

    /**
     * 解析指定的处理方法
     *
     * @param callable|array|string|null $action
     * @return array
     */
    protected function parseAction($action)
    {
        $missingAction = ['uses' => function () {
            echo 'route for ' . $this->uri . ' has no action';
            exit;
        }];
        if (!$action) {
            return $missingAction;
        }

        if (is_callable($action)) {
            return ['uses' => $action];
        }
        // 如果没有指定
        if (is_array($action)) {
            if (!isset($action['uses'])) {
                return $missingAction;
            }
            $uses = $action['uses'];
            if (is_callable($uses)) {
                return $action;
            }
            $rt = $action;

        } else {
            $rt   = [];
            $uses = $action;
        }
        if (!strpos($uses, '@') > 0) {
            return $missingAction;
        }
        $rt['uses']       = $action;
        $rt['controller'] = $action;
        return $rt;
    }

    /**
     * 获取完整的uri
     */
    public function getFullUri()
    {
        return 'todo';
    }

    /**
     * 获取完整的uri
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * 获取完整的uri
     */
    public function getAction(): array
    {
        return $this->action;
    }

    /**
     * 获取完整的uri
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * 设置别名
     */
    public function alias(string $as)
    {
        $this->action['as'] = $as;
        $this->container->setAlias($as, $this);
    }

    public function setContainer(RouterContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 设置uri解析出来的参数
     * @param array $params
     */
    public function setUriParams(array $params)
    {
        $this->uriParams = $params;
    }
}