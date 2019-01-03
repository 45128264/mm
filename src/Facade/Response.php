<?php

namespace Qyk\Mm\Facade;

use Qyk\Mm\Route\Router;
use Qyk\Mm\Route\RouterContainer;
use Throwable;

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
     * @throws Throwable
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
        try {
            $content = $this->router->invokeController();
            $this->$controller($content);
        } catch (throwable $e) {
            $controller .= 'Error';
            $this->$controller($e);
            throw $e;
        }
        $router->terminate();
    }


    protected function getCrsfToken()
    {
        return 'crsfToken';
    }

    /**
     * 获取html模板别名
     * @return string
     */
    protected function getTpl(): string
    {
        return $this->router->getTplPath();
    }

    /**
     * 获取html模板路径
     * @return string
     */
    protected function getFullTplPath(string $alias): string
    {
        if (!$alias) {
            return false;
        }
        return APP_TEMPLE_PATH . '/' . str_replace('.', '/', $alias) . '.php';
    }

    /**
     * 渲染html内容
     * @param array $content
     * @return mixed
     */
    abstract protected function renderHtmlContent(array $content);

    /**
     * 渲染html内容，失败
     * @param Throwable $e
     * @return mixed
     */
    abstract protected function renderHtmlContentError(throwable $e);

    /**
     * 渲染json内容
     * @param array $content
     * @return mixed
     */
    abstract protected function renderJsonContent(array $content);

    /**
     * 渲染json内容失败
     * @param Throwable $e
     * @return mixed
     */
    abstract protected function renderJsonContentError(throwable $e);


}