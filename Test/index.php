<?php

namespace Test;

use Qyk\Mm\RouterContainer;
use Qyk\Mm\RouterRegister;
use Qyk\Mm\Stage;

require_once dirname(__DIR__) . '/vendor/autoload.php';

RouterRegister::group(['middleware' => 'fuck', 'namespace' => 'xx', 'prefix' => 'test'], function () {
    RouterRegister::any('hello/{id}', function ($id) {
        echo $id;
    })->alias('hello');
    RouterRegister::group(['middleware' => 'fuck_1'], function () {
        RouterRegister::post('t', 'HelloController@index');
    });
});

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI']    = 'test/hell/110';


$container = RouterContainer::instance();

var_dump($container->getRequestRouter());

//
//Stage::instance()
//    ->make(new ShellApp(), 'test', __DIR__)
//    ->render();

