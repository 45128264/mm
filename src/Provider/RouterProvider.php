<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Router;

/**
 * 路由
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class RouterProvider extends Router
{
    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'router';
    }

    /**
     * 解析当前请求的路由,并绑定当前的目标controller
     * @return bool
     */
    protected function parseRouterInfo(): bool
    {
        $pathInfo = '';
        if (isset($_SERVER['REQUEST_URI'])) {
            $pathRoot = strpos($_SERVER['REQUEST_URI'], '?') ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];
            $pathRoot = $this->reRouter($pathRoot);
            $pathInfo = trim($pathRoot, '/') ? explode("/", trim($pathRoot, '/')) : false;
        }
        if (!$pathInfo || !isset($pathInfo[1])) {
            $this->routerArgs = [];
            $pathInfo         = $this->app->config->get('router.default');
        }
        list($this->currentController, $this->currentMethod) = $pathInfo;
        //指定对应的方法
        return true;
    }

    /**
     * 路由重定向
     * @param $url
     * @return array
     */
    private function reRouter($url)
    {
        $url   = trim($url, '/');
        $path  = NULL;
        $rules = $this->app->config->get('router.rules');
        foreach ($rules as $key => $value) {
            $key = trim($key, '/');
            preg_match_all("/<([\w_]+):([^>]+)>/", $key, $matchs);
            foreach ($matchs[2] as &$val) {
                $val = '(' . $val . ')';
            }
            unset($val);
            $matchs[0][] = '/';
            $matchs[0][] = '.';
            $matchs[2][] = '\/';
            $matchs[2][] = '\.';
            $key         = str_replace($matchs[0], $matchs[2], $key);
            if (preg_match('/' . $key . '$/', $url, $args)) {
                foreach ($matchs[1] as $key => $val) {
                    $this->routerArgs[$val] = $args[$key + 1];
                }
                if (preg_match_all("/<([\w_]+)>/", $value, $matchs)) {
                    $replaces = [];
                    foreach ($matchs[1] as &$val) {
                        $replaces[] = isset($this->routerArgs[$val]) ? $this->routerArgs[$val] : $val;
                    }
                    $value = str_replace($matchs[0], $replaces, $value);
                }
                $path = str_replace($args[0], $value, $url);
                break;
            }
        }
        return $path;
    }

    /**
     * 执行功能
     * @return bool|void
     */
    public function execute()
    {
        echo 'sfs';exit;
    }
}