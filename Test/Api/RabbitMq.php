<?php

namespace Test\Api;

use Closure;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Qyk\Mm\Singleton;
use Qyk\Mm\Stage;

class RabbitMq
{
    use Singleton;

    /**
     * 队列的参数
     * @var array
     */
    protected $queueDeclareParams = [];

    /**
     * 队列消费者接口参数
     * @var array
     */
    protected $basicConsumeParams = [];

    /**
     * @var AMQPStreamConnection
     */
    protected $connection;

    /**
     * @var AMQPChannel
     */
    protected $channel;


    /**
     * 定义队列的参数
     * @param bool $passive
     * @param bool $durable
     * @param bool $exclusive
     * @param bool $autoDelete
     * @return RabbitMq
     */
    public function setQueueDeclareParams($passive = false, $durable = false, $exclusive = false, $autoDelete = false)
    {
        $this->queueDeclareParams = [$passive, $durable, $exclusive, $autoDelete];
        return $this;
    }


    /**
     * 定义，消费者队列的参数
     * @param bool $consumerTag
     * @param bool $noLocal
     * @param bool $noAck
     * @param bool $exclusive
     * @param bool $nowait
     * @return RabbitMq
     */
    public function setBasicConsumeParams($consumerTag = false, $noLocal = false, $noAck = false, $exclusive = false, $nowait = false)
    {
        $this->basicConsumeParams = [$consumerTag, $noLocal, $noAck, $exclusive, $nowait];
        return $this;
    }

    /**
     * 注入队列
     * @param string $push
     * @param        $queueKey
     * @param        $routingKey
     * @param string $exchange
     */
    public function pushStrAndClose(string $push, $queueKey, $routingKey, $exchange = '')
    {
        $this->pushStr($push, $queueKey, $routingKey, $exchange)->closeChanel();
    }

    /**
     * str注入，但不会自动关闭连接
     * @param string $push
     * @param        $queueKey
     * @param        $routingKey
     * @param string $exchange
     * @return RabbitMq
     */
    public function pushStr(string $push, $queueKey, $routingKey, $exchange = '')
    {
        $this->openChanel();

        $this->refreshBasicConsumeParams();
        array_unshift($this->queueDeclareParams, $queueKey);
        $this->channel->queue_declare(...$this->queueDeclareParams);
        $this->channel->basic_publish(new AMQPMessage($push), $exchange, $routingKey);

        //清除队列,自定义的参数
        $this->queueDeclareParams = [];
        return $this;
    }

    /**
     * array注入，但不会自动关闭连接
     * @param array  $push
     * @param        $queueKey
     * @param        $routingKey
     * @param string $exchange
     * @return RabbitMq
     */
    public function pushArray(array $push, $queueKey, $routingKey, $exchange = '')
    {
        return $this->pushStr(json_encode($push, JSON_UNESCAPED_UNICODE), $queueKey, $routingKey, $exchange);
    }


    /**
     * 获取队列内容但不闭连接，减少其他操作需要重新连接
     * @param         $queueKey
     * @param Closure $callback 获取队列内容后的执行回调
     * @return RabbitMq
     * @throws \ErrorException
     */
    public function receiver($queueKey, Closure $callback)
    {
        $this->openChanel();
        $this->refreshBasicConsumeParams();
        array_unshift($this->queueDeclareParams, $queueKey);
        $this->channel->queue_declare(...$this->queueDeclareParams);
        $this->queueDeclareParams = []; //清除队列,自定义的参数

        $this->refreshBasicConsumeParams();
        array_unshift($this->basicConsumeParams, $queueKey);
        array_push($this->basicConsumeParams, $callback);
        $this->channel->basic_consume(...$this->basicConsumeParams);
        $this->basicConsumeParams = [];

        while (count($this->channel->callbacks)) {
            sleep(1);
            return $this;
            $this->channel->wait();
        }

        return $this;
    }

    /**
     * 刷新,队列消费者接口参数
     */
    protected function refreshQueueDeclareParams()
    {
        if (!$this->queueDeclareParams) {
            $this->setQueueDeclareParams();
        }
    }

    /**
     * 刷新,消费者队列的参数
     */
    protected function refreshBasicConsumeParams()
    {
        if (!$this->basicConsumeParams) {
            $this->setBasicConsumeParams();
        }
    }

    /**
     * 启动连接
     */
    protected function openChanel()
    {
        if ($this->connection) {
            return;
        }
        $conf             = Stage::app()->config->get('rabbitMq');
        $this->connection = new AMQPStreamConnection($conf['host'], $conf['port'], $conf['user'], $conf['password']);
        $this->channel    = $this->connection->channel();
        Stage::app()->bindTerminate('rabbitMq', function () {
            $this->closeChanel();
        });
    }

    /**
     * 关闭管道
     */
    public function closeChanel()
    {
        if (!$this->connection) {
            return;
        }
        $this->connection->close();
        $this->channel->close();
        $this->connection = null;
        $this->channel    = null;
        Stage::app()->unsetTerminate('rabbitMq');
    }
}