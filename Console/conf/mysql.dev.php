<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/9
 * Time: 14:40
 */

return [
    'myBlog' => [
        'rw_separate'  => false,  // 数据库读写是否分离 分布式(主从服务器)有效
        'slave_no'     => '',     // 指定从服务器序号
        'pass_compile' => true,   // 对应的连接密码再加密
        'compile_key'  => 'loan', // 加密的key
        'charset'      => 'utf8',
        // 主服务器对应的连接配置
        'master'       => [
            [
                'host'     => '47.106.239.137',
                'user'     => 'loan',
                'password' => 'load',
                'port'     => '3306',
                'database' => 'my_blog',
            ],
        ],
        // 从服务器对应的连接配置
        'slave'        => [
            [
                'host'     => '127.0.0.1',
                'user'     => '',
                'password' => '',
                'port'     => '',
                'database' => '',
            ],
        ]
    ],

    'myBlog1'     => [
        'rw_separate'  => false,  // 数据库读写是否分离 分布式(主从服务器)有效
        'slave_no'     => '',     // 指定从服务器序号
        'pass_compile' => true,   // 对应的连接密码再加密
        'compile_key'  => 'loan', // 加密的key
        'charset'      => 'utf8',
        // 主服务器对应的连接配置
        'master'       => [
            [
                'host'     => '47.106.239.137',
                'user'     => 'loan',
                'password' => 'load',
                'port'     => '3306',
                'database' => 'my_blog',
            ],
        ],
        // 从服务器对应的连接配置
        'slave'        => [
            [
                'host'     => '127.0.0.1',
                'user'     => '',
                'password' => '',
                'port'     => '',
                'database' => '',
            ],
        ]
    ],
    'transaction' => [
        'defaultDbAlias' => 'myBlog',     // 默认事务使用的数据库，其他需要在使用的时候指定
        'mustInSameDb'   => true,         // 检测表操作必须是相同的数据库
        'isAllowWrap'    => true,         // 是否允许事务嵌套
    ]
];