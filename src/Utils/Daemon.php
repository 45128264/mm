<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/16
 * Time: 15:17
 */

namespace Qyk\Mm\Utils;

/**
 * php 守护进程
 * 守护进程是一个在后台运行并且不受任何终端控制的进程
 * 1、创建子进程，终止父进程
 * 2、在子进程中创建新会话
 * 3、改变工作目录
 * 4、重设文件创建掩码
 * Class Daemon
 * @package Qyk\Mm\Utils
 */
class Daemon
{
    /**
     * 任务模块
     * @var array
     */
    protected $taskContainer = [];
    /**
     * pid 储存文件
     * @var string
     */
    protected $pidFileLocation;

    /**
     * Daemon constructor.
     */
    public function __construct()
    {

    }

    /**
     * 绑定任务
     * @param array|string $tasks
     * @return Daemon
     */
    public function bindTasks($tasks)
    {
        $this->taskContainer = array_merge($this->taskContainer, $tasks);
        return $this;
    }

    /**
     * 启动任务
     */
    public function start()
    {
        if ($this->isRunning()) {
            $this->throwExp('Daemon already running with PID:');
        }
        $this->daemonize();
        $this->doTask();
    }

    /**
     * 重启
     */
    public function restart()
    {
        $this->stop();
        $this->start();
    }

    /**
     * 结束
     */
    public function stop()
    {
        //todo 需要优雅关机,这块需要使用对应的缓存功能
    }

    //region daemonize

    /**
     *  实例化守护
     */
    protected function daemonize()
    {
        $this->fork();
        $this->identity();
        $this->initProcess();
        $this->signal();
    }


    /**
     * 进程是否正在运行中
     */
    private function isRunning(): bool
    {
        if (!is_file($this->pidFileLocation)) {
            return false;
        }
        $oldPid = file_get_contents($this->pidFileLocation);
        // kill -0 pid  作用是用来检测指定的进程PID是否存在, 存在返回0, 反之返回1
        if ($oldPid !== false && posix_kill(trim($oldPid), 0)) {
            return true;
        }
        return false;
    }

    /**
     *  在当前进程当前位置产生分支（子进程）
     */
    private function fork()
    {
        switch (pcntl_fork()) {
            case -1:    // 失败时，在 父进程上下文返回-1，不会创建子进程，并且会引发一个PHP错误
                $this->throwExp('fork failed');
                break;
            case 0:     // 在子进程执行线程内返回0,
                exit(); //退出主进程
                break;
            default:    // 成功时，在父进程执行线程内返回产生的子进程的PID
        }
    }

    /**
     *
     */
    private function identity()
    {

    }

    private function initProcess()
    {

    }

    /**
     *
     */
    private function signal()
    {

    }

    //endregion


    private function doTask()
    {

    }

    /**
     * 抛出异常
     * @param $msg
     */
    protected function throwExp($msg)
    {
        echo $msg;
        exit;
    }
}