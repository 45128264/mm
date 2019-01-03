<?php

namespace Qyk\Mm;

use Qyk\Mm\Facade\ErrorListener;
use Qyk\Mm\Provider\ErrorListenerProvide;

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
     * @var ErrorListener
     */
    private $erroEvenListener;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param string $appName 项目名称
     * @param string $appDirPath 项目路径
     * @param string $appNameSpace 顶层命名空间
     * @return Stage
     */
    public function define(string $appName, string $appDirPath, string $appNameSpace)
    {
        date_default_timezone_set('Asia/Shanghai');
        defined('APP_NAME') || define('APP_NAME', $appName);
        defined('APP_PATH') || define('APP_PATH', $appDirPath);
        defined('APP_CONF_PATH') || define('APP_CONF_PATH', $appDirPath . '/conf');
        defined('APP_TEMPLE_PATH') || define('APP_TEMPLE_PATH', $appDirPath . '/temple');
        defined('DS') || define('DS', DIRECTORY_SEPARATOR);
        defined('APP_NAME_SPACE') || define('APP_NAME_SPACE', $appNameSpace);
        defined('RUNTIME_LOG_PATH') || define('RUNTIME_LOG_PATH', dirname(APP_PATH) . '/log/' . APP_NAME);
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
     * 注册异常监控
     * @param ErrorListener $errorEventListener
     * @return Stage
     */
    public function registeErrorEventListener(ErrorListener $errorEventListener)
    {
        $this->erroEvenListener = $errorEventListener;
        return $this;
    }

    /**
     *  异常监控
     */
    private function addErrorListener()
    {
        if (!$this->erroEvenListener) {
            $this->erroEvenListener = new ErrorListenerProvide();
        }
        $this->erroEvenListener->listen();
    }
}