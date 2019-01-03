<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Config;
use Qyk\Mm\Facade\ErrorListener;
use Qyk\Mm\Stage;
use Throwable;

/**
 * 异常监控
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class ErrorListenerProvide extends ErrorListener
{
    protected $isExp = false;
    /**
     * 配置
     * @var []
     */
    private $config;

    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'ErrorListenerProvide';
    }

    /**
     * 开始监听异常
     * @return mixed|void
     */
    public function listen()
    {
        error_reporting(E_ALL);
        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
        register_shutdown_function([$this, 'shutdownHandler']);
    }

    /**
     * 错误处理
     * @param        $errno 错误编号
     * @param        $errstr 详细错误信息
     * @param string $errfile 出错的文件
     * @param int    $errline 出错行号
     */
    public function errorHandler($errno, $errstr, $errfile = '', $errline = 0)
    {
        $contents = date('H:i:s') . "\t{$errfile}:{$errline}\t{$errstr}\terrno:{$errno}\n";
        $this->addLog('error', $contents);
    }

    /**
     * 异常处理
     * @param throwable $e
     */
    public function exceptionHandler(throwable $e)
    {
        $this->isExp = true;
        $dateTime    = date('Y-m-d H:i:s');
        $file        = $e->getFile();
        $line        = $e->getLine();
        $msg         = $e->getMessage();
        $trace       = $e->getTraceAsString();
        $code        = $e->getCode();
        $contents    = "$dateTime\t{$file}({$line})\t$code\t$msg\n$trace\n\n";
        $this->addLog('exp', $contents);
    }

    /**
     * 中断处理
     */
    public function shutdownHandler()
    {
        if ($this->isExp) {
            return;
        }
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {
            // 将错误信息托管至think\ErrorException
            //$exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);

            // self::appException($exception);
        }
    }

    /**
     * 确定错误类型是否致命
     *
     * @access protected
     * @param  int $type
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    /**
     * 记录日志
     * @param string $type
     * @param        $content
     */
    protected function addLog(string $type, $content)
    {
        if (!is_dir(RUNTIME_LOG_PATH)) {
            mkdir(RUNTIME_LOG_PATH, 0777, true);
        }
        $file = RUNTIME_LOG_PATH . '/' . $type . '_' . date('ymd');
        file_put_contents($file . '.log', $content, FILE_APPEND);
        if (filesize($file . '.log') > 1000000000) {
            rename($file . 'log', $file . '.' . date('His') . '.log');
        }
    }
}