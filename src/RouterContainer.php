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
        $uri    = $router->getFullUri();
        if (isset($action['as'])) {
            $this->aliasList[$action['as']] = $uri;
        }
        if (isset($action['controller'])) {
            $indexKey                    = trim($action['controller'], '\\');
            $this->actionList[$indexKey] = $uri;
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
}