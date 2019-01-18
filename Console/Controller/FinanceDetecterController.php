<?php

namespace Console\Controller;

use Qyk\Mm\Utils\Daemon;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/16
 * Time: 16:03
 */
class FinanceDetecterController
{
    /**
     * @var Daemon
     */
    protected $daemonServer = null;

    public function __construct()
    {
        // $this->daemonServer = new Daemon();
        //  $this->bindTasks($this->getTasks());
    }

    /**
     *
     */
    public function restart()
    {
        echo 'this is my test';
        //        $this->daemonServer->restart();
    }

    /**
     *
     */
    public function start()
    {
        $this->daemonServer->start();
    }

    /**
     *
     */
    public function stop()
    {
        $this->daemonServer->stop();
    }

    /**
     * 获取任务
     */
    protected function getTasks(): array
    {
        return [
            DaemonFinanceDetecter::class,
            DaemonFinanceDetecter2::class
        ];
    }
}