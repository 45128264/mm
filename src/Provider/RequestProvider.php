<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Request;

/**
 * 请求
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class RequestProvider extends Request
{

    /**
     * 获取当前服务对应名称
     * 方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'requestProvider';
    }
}