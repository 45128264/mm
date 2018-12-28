<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Config;
use Qyk\Mm\Facade\ErrorEventListener;

/**
 * 异常监控
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class ErrorEventListenerProvide extends ErrorEventListener
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
        return 'ErrorEventListenerProvide';
    }

    /**
     * 开始监听异常
     * @return mixed|void
     */
    public function listen()
    {
    }
}