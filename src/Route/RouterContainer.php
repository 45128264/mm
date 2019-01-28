<?php

namespace Qyk\Mm\Route;

use Exception;
use Qyk\Mm\Traits\SingletonTrait;
use Qyk\Mm\Stage;

/**
 * 路由容器
 * Class RouterContainer
 * @package Qyk\Mm\Route
 */
class RouterContainer
{
    use SingletonTrait;
    /**
     * An array of the routes keyed by method.
     *
     * @var array
     */
    protected $routers = [];

    /**
     * 别名索引
     * @var array
     */
    protected $aliasList = [];
    /**
     * action索引
     * @var array
     */
    protected $actionList = [];
    /**
     * 当前request对应的路由
     * @var Router
     */
    protected $activeRouter;

    /**
     * 注入容器
     * @param Router $router
     */
    public function add(Router $router)
    {
        $this->addToCollections($router);
        $this->addAliasIndex($router);
    }

    /**
     * 添加method,uri=>action对应的索引，用于执行指定的方法
     * @param Router $router
     */
    protected function addToCollections(Router $router)
    {
        $uri = $router->getUri();
        foreach ($router->getMethods() as $method) {
            $this->routers[$method][$uri] = $router;
        }
    }

    /**
     * 添加别名索引,用于生产路由
     * @param Router $router
     */
    protected function addAliasIndex(Router $router)
    {
        $action = $router->getAction();
        if (isset($action['as'])) {
            $this->aliasList[$action['as']] = $router;
        }
        if (isset($action['controller'])) {
            $indexKey                    = trim($action['controller'], '\\');
            $this->actionList[$indexKey] = $router;
        }
    }

    /**
     * 定义别名
     * @param string $as
     * @param Router $router
     */
    public function setAlias(string $as, Router $router)
    {
        $this->aliasList[$as] = $router;
    }

    /**
     * 初始化
     */
    protected function init()
    {
        foreach (glob(APP_CONF_PATH . '/routers/*.php') as $file) {
            require $file;
        }
    }

    /**
     *
     */
    public function getActionList()
    {
        return $this->actionList;
    }

    /**
     *
     */
    public function getAliasList()
    {
        return $this->aliasList;
    }

    //region 路由解析

    /**
     * 获取request请求对应得处理router
     */
    public function getRequestRouter(): Router
    {
        $this->init();
        $method = Stage::app()->request->getMethod();
        if (!isset($this->routers[$method])) {
            echo 'not found http method =>' . $method;
            exit;
        }
        $uri = Stage::app()->request->getUri();
        if (!$uri) {
            echo 'not found http uri';
            exit;
        }
        $this->activeRouter = $this->getRouterByUri($uri, $method);
        return $this->activeRouter;
    }

    /**
     * 根据uri获取指定的router
     * @param $uri
     * @param $method
     * @return mixed
     */
    protected function getRouterByUri($uri, $method): Router
    {
        if (strpos($uri, '?')) {
            $uri = strstr($uri, '?', true);
        }
        $uri = trim($uri, '/');
        foreach ($this->routers[$method] as $uriRule => $router) {
            $uriRule = trim($uriRule, '/');
            $uriRule = trim($uriRule, '.');
            preg_match_all('/{([^\{]+)}/', $uriRule, $matches);
            $alias   = null;
            $search  = ['/', '.'];
            $replace = ['\/', '\.'];
            //如果有动态字段
            if (isset($matches[1][0])) {
                $alias = (array)$matches[1];
                foreach ($alias as $tmp) {
                    $search[]  = '{' . $tmp . '}';
                    $replace[] = $router->getColumnPregFilter($tmp);
                }
            }
            $uriRulePattern = '/^' . str_replace($search, $replace, $uriRule) . '$/';

            if (preg_match($uriRulePattern, $uri, $matches)) {
                array_shift($matches);
                if ($alias) {
                    $router->setUriParams(array_combine($alias, $matches));
                }
                return $router;
            }

        }
        echo 'not found http request method => ' . $uri;
        exit;
    }

    //endregion

    //region 获取路由路径
    /**
     * 根据method获取uri
     * @param string $method
     * @param array  $query
     * @return string
     * @throws Exception
     */
    public function getUriByMethod(string $method = '', array $query = [])
    {
        if ($method) {
            $router = $this->actionList[$method] ?? '';
        } else {
            $router = $this->activeRouter;
        }
        if (!$router) {
            return '';
        }
        return $this->parseUri($router, $query);
    }

    /**
     * 根据路由规则与参数，生成uri
     * @param Router $router
     * @param array  $params
     * @return string
     * @throws Exception
     */
    protected function parseUri(Router $router, array $params = [])
    {
        $allParams = array_merge($router->getParameters(), $params);
        $uriRule   = $router->getUri();
        preg_match_all('/{([^\{]+)}/', $uriRule, $matches);
        $alias  = null;
        $search = [];
        //如果有动态字段
        if (isset($matches[1][0])) {
            $alias = (array)$matches[1];
            foreach ($alias as $tmp) {
                $search[] = '{' . $tmp . '}';
                if (!isset($allParams[$tmp])) {
                    throw new Exception('missing column =>' . $tmp . ' \'s value');
                }
                $replace[] = $allParams[$tmp];
                unset($params[$tmp]);
            }
        }
        $uri = str_replace($search, $replace, $uriRule);
        if ($params) {
            $uri .= '?' . http_build_query($params);
        }
        return $uri;
    }

    /**
     * 根据alias获取uri
     * @param string $alias
     * @param array  $params 参数
     * @return string
     * @throws Exception
     */
    public function getUriByAlias(string $alias, array $params = [])
    {
        $router = $this->aliasList[$alias] ?? '';
        if (!$router) {
            return '';
        }
        return $this->parseUri($router, $query);
    }
    //endregion
}