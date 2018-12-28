<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Log;

/**
 * 日志
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class LogProvider extends Log
{

    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'log';
    }
}