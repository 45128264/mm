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
    /**
     * 建立连接
     * @param  $params
     */
    final protected function connect($params = null)
    {
        Stage::app()->bindTerminate(get_called_class(), [$this, 'close']);
        $this->buildConnect($params);
    }

    /**
     * 建立连接
     * @param  $params
     * @return mixed
     */
    abstract protected function buildConnect($params = null);

    /**
     * 关闭操作
     */
    final public function close()
    {
        Stage::app()->unsetTerminate(get_called_class());
        $this->distConnect();
    }

    /**
     * 关闭操作
     */
    abstract protected function distConnect();


}