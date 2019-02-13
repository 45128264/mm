<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;

/**
 * mysql 的查询功能
 * Class OpSelect
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class OpSelect extends BaseOp
{
    private $sql = '';

    /**
     * 获取sql
     * @return mixed
     */
    public function getSql()
    {
        if ($this->isDirectBySql() && $this->sql) {
            return $this->sql;
        }
        $fn = function () {
            $table = $this->getTable();
            if (isset($this->bindings['join'])) {
                $table .= ' ' . implode(' ', $this->bindings['join']);
            }
            $select = empty($this->bindings['select']) ? '*' : implode(',', $this->bindings['select']);
            $where  = empty($this->bindings['where']) ? '' : 'where ' . implode(' and ', $this->bindings['where']);
            $group  = empty($this->bindings['group']) ? '' : 'group by ' . implode(' and ', $this->bindings['group']);
            $order  = empty($this->bindings['order']) ? '' : 'order by ' . implode(',', $this->bindings['order']);
            $limit  = empty($this->bindings['limit']) ? '' : 'limit ' . implode(' , ', $this->bindings['limit']);
            $sql    = "select {$select} from {$table}  {$where} {$group} {$order} {$limit}";
            return $sql;
        };
        return $fn->call($this->baseMysql);
    }

    /**
     * 查询
     * @param string[] $column
     * @return OpSelect
     */
    public function select(string ...$column)
    {
        return $this->appendSelect(...array_map(function ($val) {
            return '`' . $val . '`';
        }, $column));
    }

    /**
     * 不进行任何校对的查询
     * 可以传多个参数
     * @param string ...$express
     * @return OpSelect
     */
    public function selectRaw(string ...$express)
    {
        return $this->appendSelect(...$express);
    }

    /**
     * 不进行任何校对的limit
     * start从0开始
     * @param int $start
     * @param int $perpage
     * @return OpSelect
     */
    public function limit(int $start, int $perpage = null)
    {
        $this->appendBinding('limit', $start);
        if ($perpage) {
            $this->appendBinding('limit', $perpage);
        }
        return $this;

    }

    /**
     * 不进行任何校对的group
     * @param string $express
     * @return OpSelect
     */
    public function group(string $express)
    {
        $this->appendBinding('group', $express);
        return $this;
    }

    //region order 排序

    /**
     * 自定义排序
     * @param string $express
     * @return OpSelect
     */
    public function order(string $express)
    {
        $this->appendBinding('order', $express);
        return $this;
    }

    /**
     * 升序
     * @param string $column
     * @return OpSelect
     */
    public function orderAsc(string $column)
    {
        $this->appendBinding('order', $column . ' asc');
        return $this;
    }

    /**
     * 降序
     * @param string $column
     * @return OpSelect
     */
    public function orderDesc(string $column)
    {
        $this->appendBinding('order', $column . ' desc');
        return $this;
    }

    /**
     * 自定义字段排序
     * @param string $column
     * @param string ...$orderVals
     * @return $this
     */
    public function orderField(string $column, string ...$orderVals)
    {
        $this->bindings['order'][] = 'field(' . $column . ',' . implode(',', $orderVals) . ')';
        return $this;
    }
    //endregion

    /**
     * 添加查询
     * @param string ...$column
     * @return OpSelect
     */
    protected function appendSelect(string ...$column)
    {
        $this->appendBinding('select', implode(',', $column));
        return $this;
    }

    /**
     * 根据sql查询
     * @param string $sql
     * @return SelectRt
     * @throws \Exception
     */
    public function selectBySql(string $sql)
    {
        $this->directBySql();
        $this->sql = $sql;
        return $this->getRt(SelectRt::instance(), false);
    }


    /**
     * 执行sql
     * @return SelectRt
     * @throws \Exception
     */
    public function run()
    {
        return $this->getRt(SelectRt::instance(), false);
    }
}