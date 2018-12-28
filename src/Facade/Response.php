<?php

namespace Qyk\Mm\Facade;

use Throwable;

/**
 * response接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Response extends Facade
{
    public    $isJson = false;
    protected $throwable; //异常

    /**
     * 获取当前Facade对应别名
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'response';
    }

    /**
     * 渲染结果
     * @return mixed
     */
    abstract public function render();

    /**
     * 设置运行脚本结果
     * @param array $rt
     * @return mixed
     */
    abstract public function setControllerRt(array $rt = []);

    /**
     * 获取crsfToken
     */
    public function getCrsfToken(): string
    {
        return '';
    }

    public function setExp(Throwable $e)
    {
        $this->throwable = $e;
    }
}