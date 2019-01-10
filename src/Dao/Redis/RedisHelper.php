<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/10
 * Time: 17:37
 */

namespace Qyk\Mm\Dao\Redis;


use Qyk\Mm\Facade\AbstractConnectService;
use Qyk\Mm\Traits\SingletonTrait;

/**
 * redis服务
 * Class RedisHelper
 * @package Qyk\Mm\Dao\Redis
 */
class RedisHelper extends AbstractConnectService
{
    use SingletonTrait;

    /**
     * 建立连接
     * @param  $params
     * @return mixed
     */
    protected function buildConnect($params = null)
    {
        // TODO: Implement buildConnect() method.
    }

    /**
     * 关闭操作
     */
    protected function distConnect()
    {
        // TODO: Implement distConnect() method.
    }
}