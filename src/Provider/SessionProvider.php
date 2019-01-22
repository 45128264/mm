<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Session;
use Qyk\Mm\Stage;

/**
 * session
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class SessionProvider extends Session
{
    /**
     * 是否存在指定的key数据，允许多层连接
     * @param string $key
     * @param string $keyMarker 层级字段分隔符
     * @return bool
     */
    protected function doneExists(string $key, string $keyMarker = '.')
    {
        $key = trim($key, $keyMarker);
        $key = explode($keyMarker, $key);
        $tmp = $_SESSION;
        $rt  = false;
        foreach ($key as $item) {
            $rt = isset($tmp[$item]);
            if (!$rt) {
                return false;
            }
            $tmp = $tmp[$item];
        }
        return $rt;
    }

    /**
     * 保存
     * @param string $key
     * @param        $val
     * @param string $keyMarker
     */
    protected function doneSet(string $key, $val, string $keyMarker = '.')
    {
        $key = trim($key, $keyMarker);
        $key = explode($keyMarker, $key);
        $tmp = &$_SESSION;
        $i   = count($key);
        foreach ($key as $item) {
            $i--;
            $tmp[$item] = $i ? ($tmp[$item] ?? []) : $val;
            $tmp = &$tmp[$item];
        }
    }

    /**
     * 获取指定的key数据，允许多层连接
     * @param string $key
     * @param string $keyMarker 层级字段分隔符
     * @return false | mixed
     */
    protected function doneGet(string $key, string $keyMarker = '.')
    {
        $key = trim($key, $keyMarker);
        $key = explode($keyMarker, $key);
        $tmp = $_SESSION;
        foreach ($key as $item) {
            $rt = isset($tmp[$item]);
            if (!$rt) {
                return false;
            }
            $tmp = $tmp[$item];
        }
        return $tmp;
    }

    /**
     * 删除
     * @param string $key
     * @param string $keyMarker
     * @return bool
     */
    protected function doneUnset(string $key, string $keyMarker = '.')
    {
        $key = trim($key, $keyMarker);
        $key = explode($keyMarker, $key);
        $tmp = $_SESSION;
        $i   = count($key);
        foreach ($key as $item) {
            $i--;
            $rt = isset($tmp[$item]);
            if (!$rt) {
                return false;
            }
            if ($i) {
                $tmp = &$tmp[$item];
            } else {
                unset($tmp[$item]);
            }
        }
        $_SESSION = $tmp;
        return true;
    }

    /**
     * 设置session的全局
     *
     * @return $this
     */
    protected function doneIniSet()
    {
        $conf = Stage::app()->config->get('app.session');
        if (isset($conf['maxLifeTime'])) {
            ini_set('session.gc_maxlifetime', $conf['maxLifeTime']);
        }
        if (isset($conf['driver'])) {
            ini_set('session.save_handler', $conf['driver']);
            $conf     = Stage::app()->config->get($conf['driverConf']);
            $savePath = 'tcp://' . $conf['host'] . ':' . $conf['port'];
            $params   = [];
            if ($conf['persistent']) {
                $params[] = 'persistent=1';
            }
            if (isset($conf['database'])) {
                $params[] = 'database=' . $conf['database'];
            }
            if (isset($conf['auth'])) {
                $params[] = 'auth=' . $conf['auth'];
            }
            if (isset($params[0])) {
                $savePath .= '?' . implode('&', $params);
            }
            ini_set('session.save_path', $savePath);
        }

        return $this;
    }

    /**
     * 启动
     */
    protected function start()
    {
        if ($this->isActive()) {
            return;
        }
        $this->doneIniSet();
        session_start();
    }
}