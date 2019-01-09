<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;

use Qyk\Mm\Dao\Mysql\BaseMysql;

/**
 * table组件
 * Class TableBuilder
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class TableBuilder extends BaseModule
{
    /**
     * left表关联
     * @param string $table
     * @param string $tableAlias
     * @param string $on
     * @return BaseMysql
     * @throws \Exception
     */
    public function tableLeftJoin(string $table, string $tableAlias, string $on)
    {
        return $this->tableJoin($table, $tableAlias, $on, 'left');
    }

    /**
     * right表关联
     * @param string $table
     * @param string $tableAlias
     * @param string $on
     * @return BaseMysql
     * @throws \Exception
     */
    public function tableRightJoin(string $table, string $tableAlias, string $on)
    {
        return $this->tableJoin($table, $tableAlias, $on, 'right');
    }

    /**
     * 表关联
     * @param string $table
     * @param string $tableAlias 别名
     * @param string $on
     * @param string $joinType in[left ,right,inner]
     * @return BaseMysql
     * @throws \Exception
     */
    public function tableJoin(string $table, string $tableAlias, string $on, $joinType = '')
    {
        if (!$on) {
            //todo
            echo 'error,on express is missing';
            exit;
        }
        $table = $this->getDbHandel()->escape($table);
        $this->appendBinding('join', "{$joinType} join {$table} {$tableAlias} on {$on}");
        $this->directBySql();
        return $this->baseMysql;
    }

    /**
     * 数据表的别名
     * @param string $tableAlias
     * @return BaseMysql
     */
    public function setTableAlias(string $tableAlias)
    {
        $fn = function ($tableAlias) {
            $this->tmpAlias = " {$tableAlias}";
        };
        $fn->call($this->baseMysql, $tableAlias);
        return $this->baseMysql;
    }

}