<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/16
 * Time: 15:17
 */

namespace Qyk\Mm\Utils\Daemon;

use Qyk\Mm\Stage;
use Throwable;

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
     * 进程是否还在运行中
     * @var bool
     */
    protected $isOldPidRunning = false;

    /**
     * 优雅关机插件
     * @var ElegantStopUtils
     */
    protected $elegantStopUtil;

    /**
     * Daemon constructor.
     * @param string $taskName 任务名称，必须是唯一的
     */
    public function __construct(string $taskName)
    {
        $this->pidFileLocation = '/tmp/daemon_' . $taskName . '.pid';
        $this->elegantStopUtil =  new ElegantStopUtils($taskName);
        $this->initOldPidIsRunning();
    }

    /**
     * 绑定任务
     * @param AbstractDaemonTask $task
     * @return Daemon
     */
    public function bindTask(AbstractDaemonTask $task)
    {
        $task->setElegantStopUtils($this->elegantStopUtil);
        $this->taskContainer[] = $task;
        return $this;
    }

    /**
     * 启动任务
     */
    public function start()
    {
        if ($this->isOldPidRunning) {
            $this->throwExp('Daemon already running with PID:' . file_get_contents($this->pidFileLocation));
        }
        $this->fork();
        $this->setSid();
        $this->bindSigHandle();
        $this->doTask();
    }

    /**
     * 重启
     */
    public function restart()
    {
        $this->stop();
        $this->isOldPidRunning = false;
        $this->start();
    }

    /**
     * 结束
     */
    public function stop()
    {
        if ($this->isOldPidRunning) {
            $this->elegantStopUtil->taskTerminate();
            $pid = file_get_contents($this->pidFileLocation);
            if (posix_kill($pid, SIGKILL)) {
                unlink($this->pidFileLocation);
            }
        }
    }

    /**
     * 设置回话
     */
    protected function setSid()
    {
        $userID  = 65534;
        $groupID = 65533;
        if (!posix_setgid($groupID) || !posix_setuid($userID)) {
            $this->throwExp('could not set identity');
        }
        $pid = posix_getpid();
        if (!posix_setsid()) {
            $this->throwExp('could not make a current process a session leader');
        }
        chdir('/');
        umask(0); // 修改文件模式，让进程有较大权限，保证进程有读写执行权限
        //关闭打开的文件描述符
        file_put_contents($this->pidFileLocation, $pid);
    }

    /**
     * 进程是否正在运行中
     */
    private function initOldPidIsRunning()
    {
        if (!is_file($this->pidFileLocation)) {
            return;
        }
        $oldPid = file_get_contents($this->pidFileLocation);
        // kill -0 pid  作用是用来检测指定的进程PID是否存在, 存在返回0, 反之返回1
        if ($oldPid !== false && posix_kill(trim($oldPid), 0)) {
            $this->isOldPidRunning = true;
        }
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
                break;
            default:    // 成功时，在父进程执行线程内返回产生的子进程的PID
                exit(); //退出主进程
        }
    }

    /**
     * 绑定事件监听
     */
    private function bindSigHandle()
    {
        declare(ticks=1);
        pcntl_signal(SIGTERM, function () {
            $this->throwExp('shutdown signal');
        });

        pcntl_signal(SIGCHLD, function () {
            while (pcntl_waitpid(-1, $status, WNOHANG) > 0) ;
        });
    }


    /**
     * 遍历执行指定的任务
     */
    private function doTask()
    {
        fclose(STDIN);
        while (!empty($this->taskContainer)) {
            /**
             * @var AbstractDaemonTask $task
             */
            $task = array_shift($this->taskContainer);
            try {
                $task->run();
                sleep(1);
            } catch (Throwable $e) {
                $task->runningWithExp();
                $this->logExp($e);
            }
            if ($task->isLiving() && !$task->isMaxExp()) {
                $this->taskContainer[] = $task;
            }
        }
    }

    /**
     * 抛出异常
     * @param $msg
     */
    protected function throwExp($msg)
    {
        echo 'daemon.exp=>' . $msg;
        exit;
    }

    /**
     * 记录异常日志
     * @param Throwable $e
     */
    private function logExp(Throwable $e)
    {
        $this->isExp = true;
        $dateTime    = date('Y-m-d H:i:s');
        $file        = $e->getFile();
        $line        = $e->getLine();
        $msg         = $e->getMessage();
        $trace       = $e->getTraceAsString();
        $code        = $e->getCode();
        $contents    = "$dateTime\t{$file}({$line})\t$code\t$msg\n$trace\n\n";
        Stage::log('exp', $contents);
    }
}