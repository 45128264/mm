<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Config;
use Qyk\Mm\Facade\ErrorListener;

/**
 * 异常监控
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class ErrorListenerProvide extends ErrorListener
{
    /**
     * 配置
     * @var []
     */
    private $config;

    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'ErrorListenerProvide';
    }

    /**
     * 开始监听异常
     * @return mixed|void
     */
    public function listen()
    {
        error_reporting(E_ALL);
        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
        register_shutdown_function([$this, 'shutdownHandler']);
    }

    /**
     * 错误处理
     */
    protected function errorHandler()
    {

    }

    /**
     * 异常处理
     */
    protected function exceptionHandler()
    {

    }

    /**
     * 中断处理
     */
    protected function shutdownHandler()
    {

    }
}