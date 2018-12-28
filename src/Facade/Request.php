<?php

namespace Qyk\Mm\Facade;

/**
 * request接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Request extends Facade
{
    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'request';
    }

    /**
     * 强制跳转
     * @param      $uri
     * @param null $http_response_code
     */
    public function redirect($uri, $http_response_code = null)
    {
        header('Location: ' . $uri, true, $http_response_code);
        exit;
    }
}