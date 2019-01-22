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
        $this->close();
    }

    /**
     * 关闭链接
     * @return mixed
     */
    abstract public function close();
}