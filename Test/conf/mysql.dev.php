<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/9
 * Time: 14:40
 */

return [
    'loan' => [
        'debug'        => false,  // 数据库调试模式
        'rw_separate'  => false,  // 数据库读写是否分离 分布式(主从服务器)有效
        'slave_no'     => '',     // 指定从服务器序号
        'pass_compile' => true,   // 对应的连接密码再加密
        'compile_key'  => 'loan', // 加密的key
        // 主服务器对应的连接配置
        'master'       => [
            [
                'host'    => '127.0.0.1',
                'pass'    => '',
                'user'    => '',
                'port'    => '',
            ],
        ],
        // 从服务器对应的连接配置
        'slave'        => [
            [
                'host' => '127.0.0.1',
                'pass' => '',
                'user' => '',
                'port' => '',
            ],
        ]
    ]
];