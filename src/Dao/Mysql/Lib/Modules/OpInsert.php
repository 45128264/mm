<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;
/**
 * mysql 的插入功能
 * Class OpInsert
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class OpInsert extends BaseOp
{
    /**
     * 获取sql
     * @return mixed
     */
    public function getSql()
    {
        $fn = function () {
            switch (true) {
                case isset($this->bindings['singleInsertColumns']) && isset($this->bindings['singleInsertValues']):
                    $columns = '`' . implode('`,`', $this->bindings['singleInsertColumns']) . '`';
                    $values  = "('" . implode("','", $this->bindings['singleInsertValues']) . "')";
                    break;
                case isset($this->bindings['mulInsertColumns']) && isset($this->bindings['mulInsertValues']):
                    $columns = '`' . implode('`,`', $this->bindings['mulInsertColumns']) . '`';
                    $values  = "('" . implode("'),('", $this->bindings['mulInsertValues']) . "')";
                    break;
                default:
                    return '';
            }
            $table = $this->getTable();
            $sql   = "insert into {$table} ({$columns}) values {$values}";
            return $sql;
        };
        return $fn->call($this->baseMysql);
    }

    /**
     * 批量插入
     * @param array $records [['id'=>n,...],...]
     * @return $this
     */
    public function multiInsert(array $records)
    {
        if (empty($records)) {
            return $this;
        }
        $keys      = array_keys(current($records));
        $keysCount = count($keys);
        $tmp       = [];
        foreach ($records as $recordItem) {
            if (count($recordItem) != $keysCount) {
                //todo
                echo 'error,批量插入的数据需要符合要求';
                exit;
            }
            $tmp[] = implode("','", $recordItem);
        }
        $this->appendBinding('mulInsertColumns', $keys);
        $this->appendBinding('mulInsertValues', $tmp);
        return $this;
    }

    /**
     * 添加
     * @param string $column
     * @param string $val
     * @return OpInsert
     */
    public function insert(string $column, string $val)
    {
        $this->appendBinding('singleInsertColumns', $column);
        $this->appendBinding('singleInsertValues', $val);
        return $this;
    }

    /**
     * 添加多个数据
     * @param array $records
     * @return OpInsert
     */
    public function batchInsert(array $records)
    {
        $this->appendBinding('singleInsertColumns', array_keys($records));
        $this->appendBinding('singleInsertValues', array_values($records));
        return $this;
    }

    /**
     * 执行
     * @return InsertRt
     * @throws \Exception
     */
    public function run()
    {
        return $this->getRt(InsertRt::instance());
    }
}