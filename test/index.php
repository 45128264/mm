<?php

use Qyk\Mm\Hello;
use Qyk\Mm\Stage;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$stage = new Stage(__DIR__);
//Hello::Test();

//$starget  = require dirname(__DIR__, 3) . '/mm/frame/Stage.php';
//$provider = require dirname(__DIR__, 3) . '/mm/frame/Stage.php';
//$response = $starget->make($provider);
//$response->send();
//$starget->terminate();