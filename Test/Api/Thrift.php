<?php

namespace Test\Api;

use Qyk\Mm\Facade\AbstractConnectService;
use Qyk\Mm\Traits\SingletonTrait;
use Qyk\Mm\Stage;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TSocket;

class Thrift extends AbstractConnectService
{
    use SingletonTrait;

    /**
     * @var TBufferedTransport
     */
    protected $transport;
    /**
     * 协议
     * @var TBinaryProtocol
     */
    protected $protocol;

    /**
     * 获取连接
     * @return TBinaryProtocol
     * @throws \Thrift\Exception\TTransportException
     */
    public function getProtocol()
    {
        if (!$this->protocol) {
            $this->connect();
            $this->protocol = new TBinaryProtocol($this->transport);
        }
        return $this->protocol;
    }

    /**
     * 建立连接
     * @return mixed
     * @throws \Thrift\Exception\TTransportException
     */
    protected function buildConnect()
    {
        $conf            = Stage::app()->config->get('thrift');
        $socket          = new TSocket($conf['host'], $conf['port']);
        $this->transport = new TBufferedTransport($socket, $conf['rBufSize'], $conf['wBufSize']);
        $this->transport->open();
    }

    /**
     * 关闭操作
     */
    protected function distConnect()
    {
        if (!$this->transport) {
            return;
        }
        $this->transport->close();
        $this->transport = null;
    }
}