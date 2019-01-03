<?php
return [
    'error'        => [
        'error_reporting' => E_ALL,    //设置应该报告何种 PHP 错误
    ],
    'http'         => [
        'var_method' => '_method',
    ],
    'debug'        => [
        'show_exp'     => true,
        'exp_view_tpl' => 'exp.expDebugReport',
    ],
    'exp_view_tpl' => 'exp.errorReport',
];