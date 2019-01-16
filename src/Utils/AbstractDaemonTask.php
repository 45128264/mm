<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/16
 * Time: 15:35
 */

namespace Qyk\Mm\Utils;

/**
 * 守护任务基类
 * Class AbstractDaemonTask
 * @package Qyk\Mm\Utils
 */
abstract class AbstractDaemonTask
{
    /**
     * 最大抛出异常次数
     * @var int
     */
    protected $maxThrowExpTimes = 5;

    /**
     * 公共执行入口
     */
    abstract public function run();

    /**
     * 获取当前任务名称
     * @return string
     */
    abstract protected function getTaskName(): string;

    /**
     * 是否需要继续执行
     * @return bool
     */
    public function isLiving(): bool
    {
        return $this->maxThrowExpTimes > 0;
    }

    /**
     * 执行中抛出异常
     */
    public function runningWithExp()
    {
        $this->maxThrowExpTimes--;
    }

}