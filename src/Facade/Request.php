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

    /**
     * 通关ua判断是否为手机
     * @return bool
     */
    public function isMobile()
    {
        //正则表达式,批配不同手机浏览器UA关键词。
        $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
        $regex_match .= "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
        $regex_match .= "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
        $regex_match .= "symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
        $regex_match .= "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320×320|240×320|176×220";
        $regex_match .= "|mqqbrowser|juc|iuc|ios|ipad";
        $regex_match .= ")/i";
        $userAgent   = $_SERVER['HTTP_USER_AGENT'] ?? '';
        return isset($_SERVER['HTTP_X_WAP_PROFILE'])
            || isset($_SERVER['HTTP_PROFILE'])
            || ($userAgent && preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT'])));
    }

    /**
     * 获取请求者的ip地址
     * @return mixed
     */
    public function getIp()
    {
        switch (true) {
            case ($tmpIP = $_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($tmpIP, 'unknow'):
            case ($tmpIP = $_SERVER['HTTP_CLIENT_IP']) && strcasecmp($tmpIP, 'unknow'):
            case ($tmpIP = $_SERVER['REMOTE_ADDR']) && strcasecmp($tmpIP, 'unknow'):
                $ip = $tmpIP;
                break;
            default:
                $ip = '0.0.0.0';

        }
        $ip_arr = explode(',', $ip);
        return $ip_arr[0];
    }

    /**
     * 是否异步请求
     * @return bool
     */
    public function isAjax()
    {
        return $this->header('X_REQUESTED_WITH') === 'XMLHttpRequest';
    }


    /**
     * 获取header内容
     * @param $key
     * @return null
     */
    protected function header($key)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }
}