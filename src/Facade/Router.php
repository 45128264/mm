<?php

namespace Qyk\Mm\Facade;

/**
 * 路由接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Router extends Facade
{
    /**
     * 路由解析出来的参数
     * @var array
     */
    protected $routerArgs = [];

    /**
     * 当前方法
     * @var string
     */
    public $currentController;
    /**
     * 当前方法
     * @var string
     */
    public $currentMethod;

    /**
     * 执行功能
     */
    abstract public function execute();

    /**
     * 获取当前Facade对应别名
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'router';
    }

    //-----------------------------------------------------------------------------------

    public function group()
    {

    }
}