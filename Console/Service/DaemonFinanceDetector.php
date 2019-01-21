<?php

namespace Console\Service;

use Qyk\Mm\Utils\AbstractDaemonTask;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/16
 * Time: 16:07
 */
class DaemonFinanceDetector extends AbstractDaemonTask
{
    protected $num = 0;

    /**
     * 公共执行入口
     */
    public function run()
    {
        $this->elegantStopUtil->autoTerminate([$this, 'test']);
    }

    /**
     *
     */
    public function test()
    {
        echo PHP_EOL . 'this is test' . PHP_EOL;
    }

    /**
     * 获取当前任务名称
     * @return string
     */
    protected function getTaskName(): string
    {
        // TODO: Implement getTaskName() method.
    }

    /**
     * 判断任务是否已经执行完成
     * @return bool
     */
    public function isLiving(): bool
    {
        return $this->num < 30;
    }


}