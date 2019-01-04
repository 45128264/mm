<?php

namespace Test;

use Qyk\Mm\Stage;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI']    = 'mq/sender';
$_SERVER['REQUEST_URI']    = 'mq/receiver';
$_SERVER['REQUEST_URI']    = 'thrift/hello';
//users/{user_id}/{card_id}/card

Stage::instance()
    ->define('test', __DIR__, 'Test')
    ->run(new ShellApp());
