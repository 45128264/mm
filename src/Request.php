<?php

namespace Qyk\Mm;


/**
 * 请求
 * Class RouterFactory
 * @package Qyk\Mm
 *
 * @method static post(string $uri, $action = null)
 */
class Request
{
    use Singleton;
    /**
     * 请求类型
     * @var string
     */
    protected $method;
    /**
     * 请求对应得uri
     * @var string
     */
    protected $uri;

    /**
     * 获取当前请求的类型
     */
    public function getMethod()
    {
        if (!$this->method) {
            $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            if (!$this->isCli() && 'POST' == $this->method) {
                switch (true) {
                    //如果使用的是post别名
                    case $tmp = $_POST[Stage::app()->config->get('http.var_method')] ?? '':
                        //如果method有重新定义
                    case $tmp = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? '':
                        $this->method = strtoupper($tmp);
                        break;
                }
            }
        }
        return $this->method;

    }

    /**
     * 是否是命令请求
     * @return bool
     */
    protected function isCli(): bool
    {
        $isCli = defined('IS_CLI') && IS_CLI;
        if ($isCli) {
            return true;
        }
        $sapiType = php_sapi_name();
        if (substr($sapiType, 0, 3) == 'cgi') {
            return true;
        }
        return false;
    }


    /**
     * 获取路由
     */
    public function getUri()
    {
        if (!$this->uri) {
            $uri       = $_SERVER['REQUEST_URI'] ?? '';
            $this->uri = $uri;
        }
        return $this->uri;
    }
}