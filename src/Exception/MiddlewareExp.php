<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 11:09
 */

namespace Qyk\Mm\Exception;


use Exception;
use Throwable;

/**
 * 中间件异常
 * Class MiddlewareExp
 * @package Qyk\Mm\Exception
 */
class MiddlewareExp extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}