<?php

namespace Qyk\Mm\Route;

use Closure;
use Exception;
use Qyk\Mm\Facade\Middleware;
use Qyk\Mm\Stage;
use ReflectionClass;

/**
 * 路由实体
 * Class RouterFactory
 * @package Qyk\Mm\Route
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

    /**
     * 默认参数
     * @var array
     */
    protected $defaultParameter = [];
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
    protected $uriParams = [];
    /**
     * 路由动态字段对应的正则表达式
     * @var []
     */
    protected $filter;
    /**
     * 输出类型,html or json
     * @var string
     */
    protected $response = 'html';

    /**
     * 模板文件地址
     * @var string
     */
    protected $tplPath = '';
    /**
     * uri连接符
     * @var string
     */
    protected $connector = '/';

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
        //连接符
        if (isset($attribute['connector'])) {
            $this->connector = $attribute['connector'];
        }
        //uri前缀
        if (isset($attribute['prefix'])) {
            $prefix    = trim($attribute['prefix'], $this->connector);
            $this->uri = $prefix ? $prefix . $this->connector . $this->uri : $this->uri;
        }
        //功能对应得命名空间
        if (isset($attribute['namespace'])) {
            $this->namespace = $attribute['namespace'];
        }
        //前置中间件
        if (isset($attribute['middleware'])) {
            $this->middleware['before'] = (array)$attribute['middleware'];
        }
        //前置中间件
        if (isset($attribute['before'])) {
            $this->middleware['before'] = (array)$attribute['before'];
        }
        //后置中间件
        if (isset($attribute['after'])) {
            $this->middleware['after'] = (array)$attribute['after'];
        }
        //模板渲染类型
        if (isset($attribute['response'])) {
            $this->response = $attribute['response'];
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
    public function getUri()
    {
        return $this->uri;
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
     * @param string $as
     * @return Router
     */
    public function alias(string $as): Router
    {
        $this->action['as'] = $as;
        $this->container->setAlias($as, $this);
        return $this;
    }


    /**
     * 返回的数据类型
     * @param string $responseType
     * @return Router
     */
    public function response(string $responseType)
    {
        $this->response = $responseType;
        return $this;
    }


    /**
     * 正则条件
     * @param string $column
     * @param string $pattern
     * @return Router
     */
    public function pregWhere(string $column, string $pattern): Router
    {
        $this->filter[$column] = $pattern;
        return $this;
    }

    /**
     * 设置路由对应的容器
     * @param RouterContainer $container
     */
    public function setContainer(RouterContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 获取路由动态变量对应的正则表达式
     * @param string $column
     * @return string
     */
    public function getColumnPregFilter(string $column)
    {
        $pattern = $this->filter[$column] ?? '[^\/]+';
        return '(' . $pattern . ')';
    }

    /**
     * 获取输出类型
     */
    public function getResponseType()
    {
        return $this->response;
    }

    /**
     * 设置uri解析出来的参数
     * @param array $params
     */
    public function setUriParams(array $params)
    {
        $this->uriParams = $params;
    }

    /**
     * 获取controller对应的文件路径
     * @return array|null
     * @throws \ReflectionException
     */
    public function invokeController()
    {
        if (!isset($this->action['controller'])) {
            return call_user_func($this->action['uses'], []);
        }
        $controller      = explode('@', $this->action['controller']);
        $controllerClass = $controller[0];
        $concrete        = APP_NAME_SPACE . '\\Controller\\' . $controllerClass;
        $reflector       = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            echo 'cant install controller=>' . $concrete;
            exit;
        }
        $action = $controller[1];
        $rt     = call_user_func_array([$reflector->newInstance(), $action], $this->getParameters());
        if (isset($rt['view'])) {
            $this->tplPath = str_replace(['.'], ['/'], $rt['view']);
            unset($rt['view']);
        } else {
            $this->tplPath = strtolower(strstr($controllerClass, 'Controller', true)) . '/' . $action;
        }

        return $rt;
    }

    /**
     * 获取路由路径
     */
    public function getTplPath()
    {
        return $this->tplPath;
    }

    /**
     * 获取传递的参数
     */
    protected function getParameters(): array
    {
        return array_merge($this->defaultParameter, $this->uriParams);
    }

    /**
     * 执行controller开始时触发的中间件
     */
    public function invokeBeforeMiddleware()
    {
        $this->invokeMiddleware($this->middleware, 'before');
    }

    /**
     * 输出结束时触发的中间件
     */
    public function invokeAfterMiddleware()
    {
        $this->invokeMiddleware($this->middleware, 'after');
    }

    /**
     * 触发中间件
     * @param array $middlewares
     * @param string $key
     * @throws \Qyk\Mm\Exception\MiddlewareExp
     * @throws \ReflectionException
     */
    protected function invokeMiddleware(array &$middlewares, string $key)
    {
        if (!isset($middlewares[$key])) {
            return;
        }

        foreach ($middlewares[$key] as $middleware) {
            if (!$middleware) {
                continue;
            }
            $middleware = Stage::app()->config->get('middleware', $middleware);
            if (!$middleware) {
                throw new Exception('missing middleware conf');
            }
            $reflectionClass = new ReflectionClass($middleware);
            if (!$reflectionClass->isInstantiable()) {
                throw new Exception('middleware conf with a wrong,cant instance');
            }
            $middleware = $reflectionClass->newInstance();
            if (!$middleware instanceof Middleware) {
                throw new Exception('middleware must extends Middleware Facade');
            }
            $middleware->run();
        }
        unset($middlewares[$key]);
    }
}