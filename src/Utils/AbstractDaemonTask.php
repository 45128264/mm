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
     * @var ElegantStopUtils
     */
    protected $elegantStopUtil;

    final public function setElegantStopUtils(ElegantStopUtils $elegantStopUtil)
    {
        $this->elegantStopUtil = $elegantStopUtil;
    }

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
        return true;
    }

    /**
     * 是否运行中产生的异常数已经到达顶峰
     * @return bool
     */
    public function isMaxExp(): bool
    {
        return $this->maxThrowExpTimes < 0;
    }

    /**
     * 执行中,有抛出异常的情况
     */
    public function runningWithExp()
    {
        $this->maxThrowExpTimes--;
    }

}