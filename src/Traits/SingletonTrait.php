<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 16:49
 */

namespace Qyk\Mm\Traits;

/**
 * 单态模式
 */
trait SingletonTrait
{
    /**
     * 容器
     * @var []
     */
    private static $instance = [];

    /**
     * 防止被new
     * Singleton constructor.
     * @param string $aliasName
     */
    protected function __construct(string $aliasName = null)
    {
    }

    /**
     * 单态实例化
     * @param null|string $name
     * @return static
     */
    public static function instance(string $name = null)
    {
        $class = get_called_class();
        if (!isset(self::$instance[$class]) || ($name && !isset(self::$instance[$class][$name]))) {
            $obj = new static($name);
            if ($name) {
                self::$instance[$class][$name] = $obj;
            } else {
                self::$instance[$class] = $obj;
            }
        }

        if ($name) {
            return self::$instance[$class][$name]->refresh();
        }
        return self::$instance[$class]->refresh();
    }

    /**
     * 数据刷新
     * @return $this
     */
    protected function refresh()
    {
        return $this;
    }
}