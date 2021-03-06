<?php
return [
    //region 脚本执行配置
    'runtime' => [
        'debug'        => [
            'show_exp'     => true,                     //是否开启异常调试模式
            'exp_view_tpl' => 'exp.expDebugReport',     //调试状态，异常时，对应的异常模板
        ],
        'exp_view_tpl' => 'exp.errorReport',            //非调试状态，异常时，对应的html模板
    ],
    //endregion
    //region 请求配置
    'request' => [
        'http' => [
            'var_method' => '_method',          //post请求,模拟其他put等请求时指定的变量名称
        ],
        'csrf' => [
            'trueTokenKey' => 'biny-csrf',
            'tokenKey'     => 'csrf-token',
            'postKey'      => '_csrf',
            'headerKey'    => 'X-CSRF-TOKEN',
            'debug'        => false,             //是否忽略csrf验证
        ],
    ],
    //endregion
    'redis'   => [
        'default' => [
            'host' => '127.0.0.1',
            'port' => 6379
        ],
    ]
];