<?php

namespace Qyk\Mm\Dao\Mysql;

use Closure;
use Qyk\Mm\Dao\Mysql\Lib\Module\ConditionBuilder;
use Qyk\Mm\Dao\Mysql\Lib\Module\DbHandel;
use Qyk\Mm\Dao\Mysql\Lib\Module\OpDelete;
use Qyk\Mm\Dao\Mysql\Lib\Module\OpInsert;
use Qyk\Mm\Dao\Mysql\Lib\Module\OpSelect;
use Qyk\Mm\Dao\Mysql\Lib\Module\OpUpdate;
use Qyk\Mm\Dao\Mysql\Lib\Module\SelectRt;
use Qyk\Mm\Dao\Mysql\Lib\Module\TableBuilder;
use Qyk\Mm\Traits\SingletonTrait;

/**
 * 基础查询语句，如非复杂功能，尽可能少的自己写sql语句
 * 目标，结构清晰，方便使用，便于拓展
 * 使用单态组合的方式实现数据拼装(减少功能模块的基础与new)
 * @package default
 * @author  qyk
 *
 * ------table操作
 * @method BaseMysql setTableAlias(string $tableAlias)
 * @method BaseMysql tableRightJoin(string $table, string $tableAlias, string $on)
 * @method BaseMysql tableLeftJoin(string $table, string $tableAlias, string $on)
 * @method BaseMysql tableJoin(string $table, string $tableAlias, string $on, $joinType = '')
 *
 * ------条件
 * @method BaseMysql where(string $column, string $operatorOrEqVal, string $val = null)
 * @method BaseMysql whereRaw(string $express)
 * @method BaseMysql whereIsNull(string $column)
 * @method BaseMysql whereIsNotNull(string $column)
 * @method BaseMysql whereIn(string $column, array $val)
 * @method BaseMysql whereNotIn(string $column, array $val)
 * @method BaseMysql whereFindInSet(string $column, string $val)
 * @method BaseMysql whereLike(string $column, string $val, $withLeftMark = true, $withRightMark = true)
 * @method BaseMysql whereNotLike(string $column, string $val, $withLeftMark = true, $withRightMark = true)
 * @method BaseMysql whereWithPrefixTableAlias(string $tableAlias, Closure $closure)
 *
 * -----功能操作
 * --查询
 * @method OpSelect select(string ...$column)
 * @method OpSelect selectRaw(string ...$column)
 * @method SelectRt selectBySql(string $sql)
 *
 * --插入
 * @method OpInsert multiInsert(array $records)
 * @method OpInsert insert(string $column, string $val) 添加
 * @method OpInsert batchInsert(array $record)
 *
 * --更新
 * @method OpUpdate update(string $column, string $val, $tableAlias = '')
 * @method OpUpdate updateDecrement(string $column, int $val = 1, $tableAlias = '')
 * @method OpUpdate updateIncrement(string $column, $val = 1, $tableAlias = '')
 * @method OpUpdate batchUpdate(array $records, $tableAlias = '')
 * @method OpUpdate muiltCaseUpdate(string $keyName, array $keyRecords)
 * @method OpUpdate updateRaw(string $express)
 *
 * --删除
 * @method OpDelete delete()
 **/
class BaseMysql
{
    use SingletonTrait;

    protected $table           = 'load';       // 数据表
    protected $db              = 'loan';       // 数据库
    private   $bindings        = [];           // 组织数据
    private   $tmpAlias        = null;         // 表别名
    private   $isDirectFromSql = false;        // 是否是直接使用sql
    private   $executeRt;                      // 执行结果

    /**
     * 数据刷新
     * @return $this
     */
    protected function refresh()
    {
        $this->bindings        = [];
        $this->tmpAlias        = null;
        $this->isDirectFromSql = false;
        return $this;
    }

    /**
     * 组件方法调用
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        switch (true) {
            case strpos($name, 'where') !== false:
                $module = ConditionBuilder::class;
                break;
            case stripos($name, 'table') !== false:
                $module = TableBuilder::class;
                break;
            case strpos($name, 'select') !== false:
                $module = OpSelect::class;
                break;
            case stripos($name, 'insert') !== false:
                $module = OpInsert::class;
                break;
            case stripos($name, 'update') !== false:
                $module = OpUpdate::class;
                break;
            case strpos($name, 'delete') !== false:
                $module = OpDelete::class;
                break;
            default:
                echo 'error,missing=>' . $name;
                exit;
        }
        return call_user_func_array([$module::init($this), $name], $arguments);
    }
}
