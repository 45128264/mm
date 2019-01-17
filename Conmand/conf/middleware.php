<?php
return [
    'logined'             => \Test\Middleware\Logined::class,
    'checkRolePermission' => \Test\Middleware\CheckRolePermission::class,
    'validateCsrf'        => \Qyk\Mm\Provider\MiddlewareValidateCsrf::class,
];