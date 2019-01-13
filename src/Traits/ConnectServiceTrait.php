<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/13
 * Time: 21:45
 */

namespace Qyk\Mm\Traits;


trait ConnectServiceTrait
{
    public function __destruct()
    {
        echo PHP_EOL;
        echo get_called_class() . '析构函数' . PHP_EOL;
        $this->close();
    }

    /**
     * 关闭链接
     * @return mixed
     */
    abstract public function close();
}