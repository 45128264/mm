<?php

namespace Test;

use Qyk\Mm\Application;
use Qyk\Mm\Facade\Log;
use Qyk\Mm\Provider\ShellRouterProvider;

/**
 * Class App
 * @package Test
 * @property Log $log
 */
class ShellApp extends Application
{
    public function __construct()
    {
        defined('IS_CLI') or define('IS_CLI', true);
    }

    protected function getProvider()
    {
        return [
            'router' => ShellRouterProvider::class,
        ];
    }

}