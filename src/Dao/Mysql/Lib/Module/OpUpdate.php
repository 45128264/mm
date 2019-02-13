<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;

use Exception;

/**
 * mysql 的更新功能
 * Class OpUpdate
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class OpUpdate extends BaseOp
{
    protected $whereTableAlias = null;
    protected $planColumns     = [];

    /**
     * 获取sql
     * @return mixed
     */
    public function getSql()
    {
        $fn = function () {
            if (empty($this->bindings['update'])) {
                return '';
            }
            $table = $this->getTable();
            if (isset($this->bindings['join'])) {
                $table .= ' ' . implode(' ', $this->bindings['join']);
            }
            $where = empty($this->bindings['where']) ? '' : 'where ' . implode(' and ', $this->bindings['where']);
            $set   = implode(',', $this->bindings['update']);

            $sql = "update {$table} set {$set} {$where}";
            return $sql;
        };
        return $fn->call($this->baseMysql);
    }

    /**
     * 更新
     * @param string $column
     * @param string $val
     * @param string $tableAlias 是否使用别名
     * @return OpUpdate
     * @throws Exception
     */
    public function update(string $column, string $val, $tableAlias = '')
    {
        $column = $this->getColumn($column, $tableAlias);
        $this->appendUpdate($column, $this->getDbHandel()->escape($val));
        return $this;
    }

    /**
     * 自减
     * @param string $column
     * @param int $val
     * @param string $tableAlias
     * @return $this
     * @throws Exception
     */
    public function updateDecrement(string $column, int $val = 1, $tableAlias = '')
    {
        $column = $this->getColumn($column, $tableAlias);
        $val    = $column . '=' . $column . '-' . $val;
        $this->appendUpdate($column, $val);
        return $this;
    }

    /**
     * 自增
     * @param string $column
     * @param int $val
     * @param string $tableAlias
     * @return $this
     * @throws Exception
     */
    public function updateIncrement(string $column, $val = 1, $tableAlias = '')
    {
        $column = $this->getColumn($column, $tableAlias);
        $val    = $column . '=' . $column . '+' . $val;
        $this->appendUpdate($column, $val);
        return $this;
    }

    /**
     * 更新多个字段
     * @param array $records
     * @param string $tableAlias
     * @return $this
     * @throws Exception
     */
    public function batchUpdate(array $records, $tableAlias = '')
    {
        foreach ($records as $column => $val) {
            $this->appendUpdate($this->getColumn($column, $tableAlias), $this->getDbHandel()->escape($val));
        }
        return $this;
    }

    /**
     * 自定义更新
     * @param string $express
     * @return $this
     */
    public function updateRaw(string $express)
    {
        $this->appendBinding('update', $express);
        $this->directBySql();
        return $this;
    }

    /**
     * case when 批量更新
     * @param string $key
     * @param array $keyValRecords
     * @return $this
     * @throws Exception
     */
    public function batchCaseUpdate(string $key, array $keyValRecords)
    {
        if (empty($keyValRecords)) {
            throw  new Exception('error');
        }
        $keyVals    = array_keys($keyValRecords);
        $setColumns = [];
        foreach ($keyValRecords as $keyId => $item) {
            foreach ($item as $column => $columnVal) {
                if (!isset($setColumns[$column])) {
                    $setColumns[$column] = [];
                }
                $setColumns[$column][$keyId] = $columnVal;
            }
        }
        $set = [];
        foreach ($setColumns as $columnKey => $item) {
            $str = '`' . $columnKey . '`= case `' . $key . '` ';
            foreach ($item as $itemKey => $itemVal) {
                $str .= ' when ' . $itemKey . ' then ' . $itemVal;
            }
            $str   .= ' else `' . $columnKey . '`';
            $str   .= ' end';
            $set[] = $str;
        }

        $this->appendBinding('update', implode(',', $set));
        $this->appendBinding('where', $this->getDbHandel()->escape($key) . ' in ("' . implode('","', $keyVals) . '")');
        return $this;
    }

    /**
     * 数据过滤
     * @param      $column
     * @param      $tableAlias
     * @return string
     * @throws Exception
     */
    protected function getColumn($column, $tableAlias = ''): string
    {
        $column = $this->getDbHandel()->escape($column);
        $column = '`' . $column . '`';
        if ($tableAlias) {
            $column = $tableAlias . '.' . $column;
        } else if ($alias = $this->getAlias()) {
            $column = $alias . '.' . $column;
        }
        return $column;
    }


    /**
     * 添加需要更新的字段
     * @param $column
     * @param $val
     */
    protected function appendUpdate($column, $val)
    {
        if (in_array($column, $this->planColumns)) {
            echo 'error,column is duplicated=>(' . $column . ')';
            exit;
        }
        $this->planColumns[] = $column;
        $this->appendBinding('update', $column . '=' . $val);
    }

    /**
     * 执行
     * @return ModifyRt
     * @throws Exception
     */
    public function run()
    {
        return $this->getRt(ModifyRt::instance());
    }


}