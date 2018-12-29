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
     * @param string $appName
     * @param string $appDirPath
     */
    protected function define(string $appName, string $appDirPath)
    {
        defined('APP_NAME') || define('APP_NAME', $appName);
        defined('APP_PATH') || define('APP_PATH', $appDirPath);
        defined('DS') || define('DS', DIRECTORY_SEPARATOR);
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
     * app实例化
     * @param Application $application
     * @return Facade\Response
     */
    public function make(Application $application, $appName, $appPath)
    {
        $this->addErrorListener();
        $this->define($appName, $appPath);
        //region initApp
        $initApp = function () {
            $this->init();
        };
        $initApp->call($application);
        //endregion
        $this->app = $application;
        $this->app->router->execute();
        return $this->app->response;
    }

    /**
     * 执行
     */
    public function run()
    {

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