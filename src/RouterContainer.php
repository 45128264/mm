<?php

namespace Qyk\Mm;

/**
 * 路由容器
 * Class RouterContainer
 * @package Qyk\Mm
 */
class RouterContainer
{
    use Singleton;
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
        $uri = $router->getFullUri();
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
        $method = Request::instance()->getMethod();
        if (!isset($this->routers[$method])) {
            echo 'not found http method';
            exit;
        }
        $uri = Request::instance()->getUri();
        if (!$uri) {
            echo 'not found http uri';
            exit;
        }
        return $this->getRouterByUri($uri, $method);
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
            preg_match('/{([^\{]+)}/', $uriRule, $matches);
            var_dump($uriRule);
            exit;
            //如果有动态字段
            if (isset($matches[1][0])) {
                $search  = ['/'];
                $replace = ['\/'];
                $alias   = $matches[1];
                foreach ($matches[1] as $tmp) {
                    $search[] = '{' . $tmp . '}';
                    //默认
                    $replace[] = '([^\/]+)';
                }
                $uriRulePattern = '/^' . str_replace($search, $replace, $uriRule) . '$/';

                echo $uriRulePattern . PHP_EOL;
                if (preg_match($uriRulePattern, $uri, $matches)) {
                    array_shift($matches);
                    $router->setUriParams(array_combine($alias, $matches));
                    return $router;
                }
            }
        }
        echo 'not found http request method => ' . $uri;
        exit;
    }
    //endregion
}