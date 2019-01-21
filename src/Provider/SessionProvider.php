<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Session;

/**
 * session
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class SessionProvider extends Session
{
    /**
     * 启动
     */
    protected function start()
    {
        if ($this->isActive()) {
            return;
        }
        session_start();
    }

    /**
     * 关闭,防止session死锁
     */
    protected function close()
    {
        if ($this->isActive()) {
            session_write_close();
        }
    }

    /**
     * session是否是启用中
     * @return bool
     */
    protected function isActive(): bool
    {
        return session_status() == PHP_SESSION_ACTIVE;
    }

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
        $key        = trim($key, $keyMarker);
        $key        = explode($keyMarker, $key);
        $tmp        = &$_SESSION;
        $i          = count($key);
        foreach ($key as $item) {
            $i--;
            if (!isset($tmp[$item])) {
                $tmp[$item] = $i ? [] : $val;
            }
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
     * 清空
     */
    protected function doneClear()
    {
        $_SESSION = [];
    }

    /**
     * 设置session的全局
     * @return $this
     */
    protected function doneIniSet()
    {
        $conf = Stage::app()->config->get('app.session');
        if (isset($conf['save_handler'])) {
            ini_set('session.save_handler', $conf['save_handler']);
        }
        if (isset($conf['maxlifetime'])) {
            ini_set('session.gc_maxlifetime');
        }
        if (isset($conf['save_path'])) {
            ini_set('session.save_path', $conf['save_path']);
        }
        return $this;
    }
}