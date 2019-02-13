<?php

namespace Test;

use Qyk\Mm\Stage;

require_once dirname(__DIR__) . '/vendor/autoload.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI']    = 'users/session/save.html';
//$_SERVER['REQUEST_URI']    = 'mq/receiver';
//$_SERVER['REQUEST_URI']    = 'thrift/hello';
//$_SERVER['REQUEST_URI']    = 'users/105/1/card';
//users/{user_id}/{card_id}/card
define('DEBUG_PRINT', false);
Stage::instance()
    ->define('test', __DIR__, 'Test')
    ->run(new ShellApp());