<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/17
 * Time: 15:11
 */

namespace Qyk\Mm\Utils;

use Qyk\Mm\Dao\Redis\RedisHelper;
use Qyk\Mm\Traits\SingletonTrait;

/**
 * 优雅关机
 * Class ElegantStopUtils
 * @package Qyk\Mm\Utils
 */
class ElegantStopUtils
{
    /**
     * @var RedisHelper
     */
    protected $redis;

    /**
     * 缓存
     * @var string
     */
    protected $keyTerminate;
    protected $keyRunning;

    /**
     *
     * Singleton constructor.
     * @param string $taskName 任务名称
     */
    public function __construct(string $taskName)
    {
        $this->redis        = RedisHelper::instance();
        $this->keyTerminate = 'daemon_terminating' . $taskName;
        $this->keyRunning   = 'daemon_running' . $taskName;
    }

    /**
     * 每次单独任务执行完成，尝试终止进程任务
     * @param callable $call
     * @param int      $cacheTimeOut 缓存失效时间
     */
    public function autoTerminate(callable $call, int $cacheTimeOut = 300)
    {
        if ($this->redis->exists($this->keyTerminate)) {
            exit;
        }
        $this->redis->set($this->keyRunning, date('Y-m-d H:i:s'), ['ex' => $cacheTimeOut]);
        $call();
        $this->redis->del($this->keyRunning);
    }

    /**
     * 任务需要退出
     * @param int $timeout 超时设置
     * @param int $cacheTimeOut 缓存失效时间
     */
    public function taskTerminate($timeout = 60, $cacheTimeOut = 300)
    {
        if ($this->redis->exists($this->keyRunning)) {
            $this->redis->set($this->keyTerminate, date('Y-m-d H:i:s'), ['ex' => $cacheTimeOut]);
            while ($this->redis->exists($this->keyRunning) && $timeout) {
                echo 'this daemon task is waiting to stop...' . $timeout;
                echo PHP_EOL;
                sleep(1);
                $timeout--;
            }
            if ($timeout == 0) {
                echo 'fail to terminate task...';
                exit;
            }
            $this->redis->del($this->keyTerminate);
        }


    }
}