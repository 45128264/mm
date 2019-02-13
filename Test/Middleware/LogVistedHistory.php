<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 16:06
 */

namespace Test\Middleware;


use Qyk\Mm\Facade\Middleware;
use Qyk\Mm\Stage;
use Test\Dao\VisitedHistoryTable;

/**
 * 记录访问日志
 * Class LogVistedHistory
 * @package Test\Middleware
 */
class LogVistedHistory extends Middleware
{

    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'logVisitedHistory';
    }

    /**
     * 执行,如果执行成功返回true,反之false
     * @return bool
     * @throws \Exception
     */
    protected function handle(): bool
    {
        VisitedHistoryTable::instance()
            ->insert('create_at', time())
            ->insert('ip', ip2long(Stage::app()->request->getIp()))
            ->insert('url', Stage::app()->request->getUri())
            ->run();
        return true;
    }
}