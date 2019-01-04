<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 15:51
 */

namespace Test\Controller;


use Test\Api\RabbitMq;

/**
 * 消息队列
 * Class RabbitMqController
 * @package Test\Controller
 */
class RabbitMqController
{
    /**
     * 发送消息
     */
    public function sender()
    {
        RabbitMq::instance()->pushStr('hello', 'queue_hello', 'routingKey_hello');
        return [
            'rt'  => true,
            'msg' => 'sender'
        ];
    }

    /**
     * 接受消息
     */
    public function receiver()
    {
        RabbitMq::instance()->receiver('hello', function ($msg) {
            echo $msg;
            echo PHP_EOL;
        });
        return ['rt' => true, 'msg' => 'receiver'];
    }
}