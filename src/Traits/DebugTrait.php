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
 * 简单调试
 */
trait DebugTrait
{
    /**
     * 调试输出
     * @param $msg
     */
    protected function debugPrint($msg)
    {
        if (defined('DEBUG_PRINT') && DEBUG_PRINT) {
            echo $msg . PHP_EOL;
        }
    }
}