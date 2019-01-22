<?php

namespace Qyk\Mm\Facade;

use Qyk\Mm\Traits\SingletonTrait;

/**
 * session接口
 * Class Cache
 * @package Qyk\Mm\Facade
 *
 * @method bool exists(string $key, string $keyMarker = '.')  是否存在指定的key数据，允许多层连接, $keyMarker 层级字段分隔符
 * @method void set(string $key, $val, string $keyMarker = '.')  保存指定的key数据, $keyMarker 层级字段分隔符
 * @method bool get(string $key, string $keyMarker = '.')  获取指定的key数据，允许多层连接, $keyMarker 层级字段分隔符
 * @method bool unset(string $key, string $keyMarker = '.')  删除指定的key数据，允许多层连接, $keyMarker 层级字段分隔符
 * @method void clear() 清空
 */
abstract class Session extends Facade
{
    use SingletonTrait;

    public function __construct(string $aliasName = null)
    {

    }

    /**
     * 获取当前Facade对应别名
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'session';
    }

    /**
     * 设置cookie
     * @param        $key
     * @param        $value
     * @param int    $expire
     * @param string $path
     * @param null   $domain
     */
    public function setCookie($key, $value, $expire = 86400, $path = '/', $domain = null)
    {
        setcookie($key, $value, time() + $expire, $path, $domain);
    }


    /**
     * 是否存在指定的key数据，允许多层连接
     * @param string $key
     * @param string $keyMarker 层级字段分隔符
     * @return bool
     */
    abstract protected function doneExists(string $key, string $keyMarker = '.');

    /**
     * 保存
     * @param string $key
     * @param        $val
     * @param string $keyMarker
     */
    abstract protected function doneSet(string $key, $val, string $keyMarker = '.');

    /**
     * 删除
     * @param string $key
     * @param string $keyMarker
     * @return bool
     */
    abstract protected function doneUnset(string $key, string $keyMarker = '.');

    /**
     * 获取指定的key数据，允许多层连接
     * @param string $key
     * @param string $keyMarker
     * @return array|mixed
     */
    abstract protected function doneGet(string $key, string $keyMarker = '.');

    /**
     * 设置session的全局
     * @return $this
     */
    abstract protected function doneIniSet();

    /**
     * session start
     * @return void
     */
    abstract protected function start();


    /**
     * 清空
     */
    protected function doneClear()
    {
        $_SESSION = [];
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
     * 关闭,防止session死锁
     */
    protected function close()
    {
        if ($this->isActive()) {
            session_write_close();
        }
    }

    /**
     * 获取sessionId
     * @return string
     */
    public function getSid(): string
    {
        return session_id();
    }

    /**
     * 魔法函数
     * @param string $fn
     * @param array  $args
     * @return mixed
     */
    public function __call(string $fn, array $args)
    {
        $fn = 'done' . ucfirst($fn);
        if (!method_exists($this, $fn)) {
            echo 'missing method=>' . $fn;
            exit;
        }
        $this->start();
        $rt = call_user_func_array([$this, $fn], $args);
        $this->close();
        return $rt;
    }

}