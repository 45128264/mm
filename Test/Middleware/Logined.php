<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 16:06
 */

namespace Test\Middleware;


use Qyk\Mm\Facade\Middleware;

class Logined extends Middleware
{

    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'logined';
    }

    /**
     * 执行,如果执行成功返回true,反之false
     * @return bool
     */
    protected function handle(): bool
    {
        return true;
    }
}