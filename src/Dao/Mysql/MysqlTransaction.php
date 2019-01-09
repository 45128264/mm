<?php

namespace Qyk\Mm\Dao\Mysql;

/**
 * mysql事务层级识别
 */
class MysqlTransaction
{
    private static $startTransactionNum = 0;

    /**
     * 获取当前事务包裹标志的层级
     * @return int
     */
    public static function getWrapNums(): int
    {
        return self::$startTransactionNum;
    }

    /**
     * 开启事务包裹标志，类似<div>
     */
    public static function openWrap()
    {
        self::$startTransactionNum++;
    }

    /**
     * 关闭事务包裹标志，类似</div>
     */
    public static function closeWrap()
    {
        self::$startTransactionNum = self::$startTransactionNum < 1 ? 0 : --self::$startTransactionNum;
    }

    /**
     * 清除事务包裹标志
     */
    public static function clearWrap()
    {
        self::$startTransactionNum = 0;
    }
}

?>