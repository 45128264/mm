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
    /**
     * 实例化
     * @var Stage
     */
    private static $instance;

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
    public function define(string $appName, string $appDirPath)
    {
        defined('APP_NAME') || define('APP_NAME', $appName);
        defined('APP_PATH') || define('APP_PATH', $appDirPath);
        defined('DS') || define('DS', DIRECTORY_SEPARATOR);
    }

    /**
     *
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
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
    public function make(Application $application)
    {
        $this->addErrorListener();
        $this->app = $application;
        $this->app->router->execute();
        return $application->response;
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