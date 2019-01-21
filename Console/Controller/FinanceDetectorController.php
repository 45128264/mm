<?php

namespace Console\Controller;

use Console\Service\DaemonFinanceDetector;
use Qyk\Mm\Utils\Daemon\Daemon;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/16
 * Time: 16:03
 */
class FinanceDetectorController
{
    /**
     * @var Daemon
     */
    protected $daemonServer = null;

    public function __construct()
    {
        $this->daemonServer = new Daemon('financeDetector');
    }

    /**
     *
     */
    public function restart()
    {
        echo 'this is my test,restart';
        $this->bindTask();
        $this->daemonServer->restart();
    }

    /**
     *
     */
    public function start()
    {
        echo 'this is my test,start';
        $this->daemonServer->start();
    }

    /**
     *
     */
    public function stop()
    {
        echo 'this is my test,stop';
        $this->daemonServer->stop();
    }


    /**
     * 绑定任务
     */
    private function bindTask()
    {
        $this->daemonServer->bindTask(new DaemonFinanceDetector());
    }
}