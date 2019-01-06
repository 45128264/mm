<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/5
 * Time: 7:58
 */

namespace Qyk\Mm\Facade;

use Qyk\Mm\Stage;

/**
 * 需要远程连接的服务
 * Class AbstractConnectService
 * @package Qyk\Mm\Facade
 */
abstract class AbstractConnectService
{
    final protected function connect()
    {
        Stage::app()->bindTerminate($this->getAbstractName(), [$this, 'close']);
        $this->buildConnect();
    }

    /**
     * 建立连接
     * @return mixed
     */
    abstract protected function buildConnect();

    /**
     * 关闭操作
     */
    final public function close()
    {
        Stage::app()->unsetTerminate($this->getAbstractName());
        $this->distConnect();
    }

    /**
     * 关闭操作
     */
    abstract protected function distConnect();

    /**
     * 获取别名
     */
    protected function getAbstractName()
    {
        return get_called_class();
    }
}