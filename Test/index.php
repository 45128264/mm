<?php

namespace Test;

use Qyk\Mm\Stage;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$stage = Stage::instance();
$stage->define('test', __DIR__);
$response = $stage->make(new ShellApp());
$response->render();