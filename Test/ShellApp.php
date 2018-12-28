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
    protected function getProvider()
    {
        return [
            'router' => ShellRouterProvider::class,
        ];
    }

}