<?php

namespace Qyk\Mm;
/**
 * 舞台
 * Class Stage
 * @package Qyk\Mm
 */
class Stage
{
    /**
     * @var Application
     */
    private $app;

    /**
     * 项目目录
     * @var string
     */
    private $appDirPath;

    public function __construct($appDirPath)
    {
        $this->appDirPath = $appDirPath;
    }

    /**
     * 注册provider
     */
    public function registProvider($abstract,$provider)
    {

    }

    /**
     * 实例剧场
     */
    public function make()
    {
        return $this->app;
    }
}