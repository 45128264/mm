<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;
/**
 * mysql 的删除功能
 * Class OpDelete
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class OpDelete extends BaseOp
{
    /**
     * 获取sql
     * @return mixed
     */
    public function getSql()
    {
        $fn = function () {
            $table = $this->getTable();
            if (isset($this->bindings['join'])) {
                $table .= ' ' . implode(' ', $this->bindings['join']);
            }
            $where = empty($this->bindings['where']) ? '' : 'where ' . implode(' and ', $this->bindings['where']);
            $order = empty($this->bindings['order']) ? '' : 'order by ' . implode(',', $this->bindings['order']);
            $limit = empty($this->bindings['limit']) ? '' : 'limit ' . implode(' , ', $this->bindings['limit']);
            $sql   = "delete from {$table}  {$where}  {$order} {$limit}";
            return $sql;
        };
        return $fn->call($this->baseMysql);
    }

    /**
     * 删除,只做主表删除
     * @return OpDelete
     */
    public function delete()
    {
        return $this;
    }

    /**
     * 执行
     * @return ModifyRt
     * @throws \Exception
     */
    public function run()
    {
        return $this->getRt(ModifyRt::instance());
    }
}