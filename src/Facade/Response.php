<?php

namespace Qyk\Mm\Facade;

use Qyk\Mm\Route\Router;
use Qyk\Mm\Route\RouterContainer;

/**
 * response接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Response extends Facade
{
    /**
     * @var Router
     */
    private $router;

    /**
     * 获取当前Facade对应别名
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'response';
    }

    /**
     * 渲染结果
     * @return mixed
     */
    public function render()
    {
        $router     = RouterContainer::instance()->getRequestRouter();
        $controller = 'render' . $router->getResponseType() . 'Content';
        if (!method_exists($this, $controller)) {
            echo 'fuck';
            exit;
        }
        $this->router = $router;
        $this->$controller();
        $router->terminate();
    }

    /**
     * 获取内容
     */
    protected function getContent(): array
    {
        return $this->router->invokeController();
    }

    protected function getCrsfToken()
    {
        return 'crsfToken';
    }

    /**
     * 获取默认html模板
     * @return string
     */
    protected function getTpl(): string
    {
        return $this->router->getTplPath();
    }

    /**
     * 渲染html内容
     * @param array $content
     * @return mixed
     */
    abstract protected function renderHtmlContent();

    /**
     * 渲染json内容
     * @param array $content
     * @return mixed
     */
    abstract protected function renderJsonContent();


}