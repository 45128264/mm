<?php

namespace Test;

use Qyk\Mm\RouterContainer;
use Qyk\Mm\RouterRegister;
use Qyk\Mm\Stage;

require_once dirname(__DIR__) . '/vendor/autoload.php';

RouterRegister::group(['middleware' => 'fuck', 'namespace' => 'xx', 'prefix' => 'test'], function () {
    RouterRegister::post('hello', function () {
        echo 'fuck';
    })->alias('hello');
    RouterRegister::group(['middleware' => 'fuck_1'], function () {
        RouterRegister::post('t', 'HelloController@index');
    });
});

$container = RouterContainer::instance();

//var_dump($container->getActionList());
var_dump($container->getAliasList());


//Stage::instance()
//    ->make(new ShellApp(), 'test', __DIR__)
//    ->render();