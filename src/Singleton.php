<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 16:49
 */

namespace Qyk\Mm;

/**
 * 单态模式
 * Class Singleton
 * @package Qyk\Mm
 */
trait Singleton
{
    /**
     * @var Singleton
     */
    private static $instance;


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