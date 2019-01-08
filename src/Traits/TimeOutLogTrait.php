<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/29
 * Time: 16:49
 */

namespace Qyk\Mm\Traits;

use Closure;
use Qyk\Mm\Stage;
use Throwable;

/**
 * 超时记录
 */
trait TimeOutLogTrait
{
    private $startTime;

    /**
     * 开始记录时间戳
     */
    protected function tickStart()
    {
        $this->startTime = microtime(true);
    }

    /**
     * 结束，并分析是否运行超过了设定时间
     * @param int  $timeOut 设定的超时时间，s
     * @param null $extractMsg
     */
    protected function tickEnd(int $timeOut, $extractMsg = null)
    {
        $used = microtime(true) - $this->startTime;
        if ($used > $timeOut) {
            $backtrace = array_shift(debug_backtrace());
            $backtrace = $backtrace['file'] . '(' . $backtrace['line'] . ')' . "\nfunction " . $backtrace['function'];
            $diff      = $used - $timeOut;
            $date      = date('Y-m-d H:i:s');
            $msg       = "$date\n$backtrace\t执行超时\t限定时间：$timeOut \t使用时间：$used\t超时：$diff";
            if ($extractMsg) {
                $msg .= "\n$extractMsg";
            }
            Stage::log('timeout', $msg . "\n\n");
        }
    }
}