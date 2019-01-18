<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Response;
use Qyk\Mm\Route\RouterContainer;
use Qyk\Mm\Stage;
use Throwable;

/**
 * console输出
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class ConsoleResponseProvider extends Response
{
    /**
     * 渲染结果
     * @return mixed
     * @throws Throwable
     */
    public function render()
    {
        $controller = '';
        $this->tickStart();
        try {
            $router       = RouterContainer::instance()->getRequestRouter();
            $router->invokeBeforeMiddleware();
            $router->invokeController();
            $router->invokeAfterMiddleware();
            $this->tickEnd(60);

        } catch (throwable $e) {
            if ($controller) {
                $controller .= 'Error';
                $this->$controller($e);
            } else {
                echo '系统繁忙，请稍后再试';
            }
            throw $e;
        }
    }
}