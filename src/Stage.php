<?php

namespace Qyk\Mm;

use Qyk\Mm\Facade\ErrorEventListener;
use Qyk\Mm\Provider\ErrorEventListenerProvide;

/**
 * 舞台
 * Class Stage
 * @package Qyk\Mm
 */
class Stage
{
    use Singleton;

    /**
     * 异常监控
     * @var ErrorEventListener
     */
    private $erroEvenListener;

    /**
     * @var Application
     */
    public $app;

    private function __construct()
    {
    }

    /**
     * @param string $appName 项目名称
     * @param string $appDirPath 项目路径
     * @param string $appNameSpace 顶层命名空间
     * @return Stage
     */
    public function define(string $appName, string $appDirPath, string $appNameSpace)
    {
        defined('APP_NAME') || define('APP_NAME', $appName);
        defined('APP_PATH') || define('APP_PATH', $appDirPath);
        defined('APP_CONF_PATH') || define('APP_CONF_PATH', $appDirPath . '/conf');
        defined('DS') || define('DS', DIRECTORY_SEPARATOR);
        defined('APP_NAME_SPACE') || define('APP_NAME_SPACE', $appNameSpace);
        return $this;
    }

    /**
     * 返回app
     * @return Application
     */
    public static function app()
    {
        return self::$instance->app;
    }

    /**
     * 注册异常监控
     * @param ErrorEventListener $errorEventListener
     */
    public function registeErrorEventListener(ErrorEventListener $errorEventListener)
    {
        $this->erroEvenListener = $errorEventListener;
    }

    /**
     * 执行
     * @param Application $application
     */
    public function run(Application $application)
    {
        $this->addErrorListener();
        //region initApp
        $initApp = function () {
            $this->init();
        };
        $initApp->call($application);
        //endregion
        $this->app = $application;
        $this->app->response->render();
    }


    /**
     *  异常监控
     */
    private function addErrorListener()
    {
        if (!$this->erroEvenListener) {
            $this->erroEvenListener = new ErrorEventListenerProvide();
        }
        $this->erroEvenListener->listen();
    }
}