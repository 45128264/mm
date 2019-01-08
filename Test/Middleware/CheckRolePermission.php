<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 16:06
 */

namespace Test\Middleware;


use Qyk\Mm\Facade\Middleware;

/**
 * 判断角色权限
 * Class CheckRolePermission
 * @package Test\Middleware
 */
class CheckRolePermission extends Middleware
{
    protected $expMsg = '判断角色权限验证失败';

    /**
     * 执行,如果执行成功返回true,反之false
     * @return bool
     */
    protected function handle(): bool
    {
        sleep(1);
        return true;
    }
}