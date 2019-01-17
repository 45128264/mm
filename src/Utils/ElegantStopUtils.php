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
    use SingletonTrait;
    /**
     * @var RedisHelper
     */
    protected $redis;

    /**
     * daemon_terminating对应的任务名称
     * @var string
     */
    protected $taskName;

    /**
     * 防止被new
     * Singleton constructor.
     */
    protected function __construct()
    {
        $this->redis = RedisHelper::instance();
    }

    /**
     * 每次单独任务执行完成，尝试终止进程任务
     */
    public function attemptTerminate()
    {
        if ($this->redis->hExists('daemon_terminating', $this->taskName)) {
            $this->redis->hDel('daemon_terminating', $this->taskName);
            exit;
        }
    }

    /**
     * 进程尝试启动
     * @param int $timeout 超时设置
     */
    public function taskAttemptStart(int $timeout = 60)
    {
        while ($this->redis->hExists('daemon_terminating', $this->taskName) && $timeout) {
            echo 'this daemon task is waiting to restart...' . $timeout;
            echo PHP_EOL;
            sleep(1);
            $timeout--;
        }
        if ($timeout == 0) {
            echo 'fail to restart...';
            exit;
        }
    }

    /**
     * 任务需要退出
     */
    public function taskTerminate()
    {
        $this->redis->hSet('daemon_terminating', $this->taskName, date('Y-m-d H:i:s'));
    }
}