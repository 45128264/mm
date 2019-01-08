<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Config;

/**
 * 配置
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class ConfigProvide extends Config
{
    /**
     * 配置
     * @var []
     */
    private $config;


    /**
     * 获取指定config文件类型下的key数据
     * @param        $cacheKey 配置文件/组合，（master/ master.host)
     * @param string $key 查找的数据key, 类似host
     * @return mixed
     * @return mixed
     */
    public function get($cacheKey, string $key = null)
    {
        $keys = explode('.', $cacheKey);
        if ($key) {
            $keys[] = $key;
        }
        $cacheKey = array_shift($keys);
        if (isset($this->config[$cacheKey])) {
            return $this->getArrayDeepVal($this->config[$cacheKey], $keys);
        }
        $conf = $this->getConfigByFile($cacheKey);
        //是否需要缓存对应的配置
        if (isset($conf['cache']) && $conf['cache']) {
            $this->config[$cacheKey] = $conf;
        }
        return $this->getArrayDeepVal($conf, $keys);
    }

    /**
     * 链式获取array深层数据
     * @param       $val
     * @param array $keys
     * @return mixed
     */
    private function getArrayDeepVal($val, array $keys)
    {
        if (empty($keys)) {
            return $val;
        }
        $key = array_shift($keys);
        if (!isset($val[$key])) {
            //todo exception
            trigger_error('missing key val with key:' . $key, E_NOTICE);
            return false;
        }
        if (!empty($keys)) {
            return $this->getArrayDeepVal($val[$key], $keys);
        }
        return $val[$key];
    }

    /**
     * 读取配置文件
     * @param $type
     * @return mixed
     */
    private function getConfigByFile($type)
    {
        $appEnv = $this->getAppEnv();
        $file   = APP_CONF_PATH . '/' . $type . '.php';
        if ($appEnv) {
            $file = APP_CONF_PATH . '/' . $type . '.' . $appEnv . '.php';
            if (!file_exists($file)) {
                $file = APP_CONF_PATH . '/' . $type . '.php';
            }
        }

        if (!file_exists($file)) {
            //todo exception
            echo 'missing config file=>' . $file;
            exit;
        }
        $config = include $file;
        if (!is_array($config)) {
            //todo exception
            echo 'config is not array';
            exit;
        }
        return $config;
    }

    /**
     * 获取app对应的环境
     */
    protected function getAppEnv()
    {
        $env = get_cfg_var(APP_NAME);
        return $env ? $env : 'dev';
    }
}