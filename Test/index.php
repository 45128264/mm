<?php

namespace Test;

use Qyk\Mm\Stage;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI']    = 'users/110/1/card';
//users/{user_id}/{card_id}/card


Stage::instance()
    ->define('test', __DIR__, 'Test')
    ->run(new ShellApp());
