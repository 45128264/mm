<?php

namespace Qyk\Mm\Facade;

use Throwable;

/**
 * 日志接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Log extends Facade
{
    /**
     * 获取当前Facade对应别名
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'log';
    }

    /**
     * 错误日志记录
     * @param Throwable $e
     * @param string    $fileNameSubfix 文件名后缀
     */
    public function flushThrowable(Throwable $e, $fileNameSubfix = '')
    {
        $logContents = [
            '错误编号 errno: ' . $e->getCode(),
            '错误信息 errstr: ' . $e->getMessage(),
            '出错文件 errfile: ' . $e->getFile(),
            '出错行号 errline: ' . $e->getLine(),
        ];
        $this->flush($logContents, $fileNameSubfix);
    }

    /**
     * 日志记录
     * @param array  $logContents
     * @param string $fileNameSubfix 文件名后缀
     */
    public function flush(array $logContents, $fileNameSubfix = '')
    {
        if (empty($logContents)) {
            return;
        }
        $dir = APP_LOG_PATH . APP_NAME . '/' . date('Ym') . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        //write file
        $file = $dir . date('Ymd');
        if ($fileNameSubfix) {
            $file .= '_' . $fileNameSubfix;
        }
        $placeholder = '-----------------------';
        array_unshift($logContents, $placeholder . $placeholder . date('H:i:s') . PHP_EOL);
        array_push($logContents, PHP_EOL . $placeholder . PHP_EOL);
        $logContents = implode("\n", $logContents) . "\n";
        file_put_contents($file . '.log', $logContents, FILE_APPEND | LOCK_EX);

        //keep small than 1G
        if (filesize($file . '.log') > 1000000000) {
            rename($file . '.log', $file . '.' . date('H:i:s') . '.log');
        }
    }
}