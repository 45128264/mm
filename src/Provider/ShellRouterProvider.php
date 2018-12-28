<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Router;

/**
 * 路由
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class ShellRouterProvider extends Router
{

    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'shellRouter';
    }

    /**
     * 解析当前请求的路由,并绑定当前的目标controller
     * @return bool
     */
    protected function parseRouterInfo(): bool
    {
        if (!isset($this->app->argv[2])) {
            echo 'missing methold';
            exit;
        }
        $this->currentController = ucfirst($this->app->argv[1]);
        $this->currentMethod     = $this->app->argv[2];
        //指定对应的方法
        return true;
    }

    /**
     * 执行功能
     */
    public function execute()
    {
    }
}