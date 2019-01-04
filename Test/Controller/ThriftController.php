<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 15:51
 */

namespace Test\Controller;

use Test\Api\Thrift;
use Test\Service\HelloServiceClient;

/**
 * rpc
 * Class ThriftController
 * @package Test\Controller
 */
class ThriftController
{
    public function index()
    {
        $helloClient = new HelloServiceClient(Thrift::instance()->getProtocol());
        $msg         = $helloClient->say('你好');
        return [
            'rt'  => true,
            'msg' => $msg,
        ];
    }
}