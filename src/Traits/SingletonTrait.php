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
     * @var SingletonTrait
     */
    private static $instance;

    /**
     * 防止被new
     * Singleton constructor.
     */
    protected function __construct()
    {

    }

    /**
     * 单态实例化
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}