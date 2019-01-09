<?php

namespace Qyk\Mm\Dao\Mysql;

use Closure;
use Qyk\Mm\Traits\SingletonTrait;
use Throwable;

/**
 * mysql事务层级识别
 */
class MysqlTransaction
{
    use SingletonTrait;

    /**
     * 事物包裹层级
     * @var int
     */
    private $wrapNums = 0;

    /**
     * 获取当前事务包裹标志的层级
     * @return int
     */
    public function getWrapNums(): int
    {
        return $this->wrapNums;
    }

    /**
     * commit 当callback返回true
     * rollback 当callback返回false
     * @param Closure $callback return bool
     * @throws Throwable
     */
    public function auto(Closure $callback)
    {
        //startTransaction
        try {
            $this->start();
            $rt = $callback();
            if ($rt) {
                $this->commit();
            } else {
                $this->rollback();
            }
        } catch (Throwable $e) {
            //            $this->rollback();
            throw $e;
        }
        //endTransaction
    }
}

?>