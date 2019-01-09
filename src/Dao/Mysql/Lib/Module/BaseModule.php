<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;

use Qyk\Mm\Dao\Mysql\BaseMysql;
use Stripe\Error\Base;

/**
 * 基础组件
 * Class OpInserter
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class BaseModule
{
    /**
     * 实例化的组件
     * @var array
     */
    private static $instance = [];

    /**
     * 父级
     * @var BaseMysql
     */
    protected $baseMysql;

    /**
     * 单态，实例化
     * @param BaseMysql $baseMysql
     * @return mixed
     */
    public static function init(BaseMysql $baseMysql)
    {
        $class = get_called_class();
        if (!isset(self::$instance[$class])) {
            self::$instance[$class] = new $class();
        }
        $obj            = self::$instance[$class];
        $obj->baseMysql = $baseMysql;
        return $obj;
    }

    /**
     * 绑定条件
     * @param      $column
     * @param      $val
     * @param bool $isDistinct
     * @return bool
     */
    protected function appendBinding($column, $val, $isDistinct = false)
    {
        $fn = function () use ($column, $val, $isDistinct): bool {
            //todo to filter same column
            /* if ($isDistinct && !is_array($val)) {

             }*/
            $val = (array)$val;
            if (!isset($this->bindings[$column])) {
                $this->bindings[$column] = $val;
            } else {
                $this->bindings[$column] = array_merge($this->bindings[$column], $val);
            }
            return true;
        };
        return $fn->call($this->baseMysql);
    }

    /**
     * 绑定条件
     */
    protected function directBySql()
    {
        $fn = function () {
            $this->isDirectFromSql = true;
        };
        $fn->call($this->baseMysql);
    }

    /**
     * 判断是否是直接使用sql
     * @return bool
     */
    protected function isDirectBySql():bool
    {
        $fn = function () {
           return $this->isDirectFromSql;
        };
       return $fn->call($this->baseMysql);
    }

    /**
     * 数据表的别名
     */
    protected function getAlias(): string
    {
        $fn = function () {
            return $this->tmpAlias;
        };
        return $fn->call($this->baseMysql);
    }

    /**
     * 获取表名
     * @return string
     */
    public function getTable()
    {
        $fn = function () {
            return $this->table . ($this->tmpAlias ?: '');
        };
        return $fn->call($this->baseMysql);
    }

    /**
     * 获取库名
     * @return string
     */
    public function getDb()
    {
        $fn = function () {
            return $this->db;
        };
        return $fn->call($this->baseMysql);
    }

    /**
     * 初始化连接
     * @param bool $isWriter  是否是写操作，是则使用主库，否则会进行主从筛选
     * @return mixed
     */
    protected function getDbHandel($isWriter = true): DbHandel
    {
        return DbHandel::instance($this->getDb())->setIsWriter($isWriter);
    }

}