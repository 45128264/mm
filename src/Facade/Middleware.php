<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 10:54
 */

namespace Qyk\Mm\Facade;

use Exception;
use Qyk\Mm\Exception\MiddlewareExp;

/**
 * 中间件
 * Class Middleware
 * @package Qyk\Mm\Facade
 */
abstract class Middleware extends Facade
{
    /**
     * exp对应的错误提示
     * @var string
     */
    protected $expMsg = '';
    /**
     * exp对应的错误编码
     * @var int
     */
    protected $expCode = 0;

    /**
     * 获取当前Facade对应别名
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'middleware';
    }

    /**
     * 统一入口
     * @return void
     * @throws MiddlewareExp
     */
    final public function run()
    {
        try {
            $rt = $this->handle();
        } catch (Exception $e) {
            $rt = false;
        }
        if (!$rt) {
            throw new MiddlewareExp(...$this->getExpParams());
        }
    }

    /**
     * 执行,如果执行成功返回true,反之false
     * @return bool
     */
    abstract protected function handle(): bool;

    /**
     * 异常对应的错误提示
     * @return array [string $message = "", int $code = 0, Throwable $previous = null]
     */
    protected function getExpParams(): array
    {
        return [
            get_called_class() . "\t" . $this->expMsg,
            $this->expCode
        ];
    }

}