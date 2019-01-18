<?php

namespace Console;

use Qyk\Mm\Application;
use Qyk\Mm\Facade\Log;
use Qyk\Mm\Provider\ConsoleResponseProvider;

/**
 * Class ShellApp
 * @package Test
 * @property Log $log
 */
class ShellApp extends Application
{
    public function __construct()
    {
        defined('IS_CLI') or define('IS_CLI', true);
    }

    /**
     * 自定义服务模块
     * @return array
     */
    protected function provider()
    {
        return [
            'response' => ConsoleResponseProvider::class
        ];
    }

}