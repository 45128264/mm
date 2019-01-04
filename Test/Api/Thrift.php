<?php

namespace Test\Api;

use Qyk\Mm\Singleton;
use Qyk\Mm\Stage;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TSocket;

class Thrift
{
    use Singleton;

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
            $this->openTransport();
            $this->protocol = new TBinaryProtocol($this->transport);
        }
        return $this->protocol;
    }

    /**
     * 开启连接
     * @throws \Thrift\Exception\TTransportException
     */
    protected function openTransport()
    {
        if ($this->protocol) {
            return;
        }
        $conf            = Stage::app()->config->get('thrift');
        $socket          = new TSocket($conf['host'], $conf['port']);
        $this->transport = new TBufferedTransport($socket, $conf['rBufSize'], $conf['wBufSize']);
        $this->transport->open();
        Stage::app()->bindTerminate('thrift', function () {
            $this->closeTransport();
        });
    }

    /**
     * 关闭连接
     */
    public function closeTransport()
    {
        if (!$this->transport) {
            return;
        }
        $this->transport->close();
        $this->transport = null;
        Stage::app()->unsetTerminate('thrift');
    }
}