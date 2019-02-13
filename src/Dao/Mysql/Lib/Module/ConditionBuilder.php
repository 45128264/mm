<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;

use Closure;

/**
 * mysql 的条件拼装
 * Class OpConditionBuilder
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class ConditionBuilder extends BaseModule
{
    protected $whereTableAlias = null;

    /**
     * 添加条件
     * @param string $column
     * @param string $operatorOrEqVal ["=", "!=", ">", ">=", "<", "<=", "<>"]
     * @param string $val
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     * @throws \Exception
     */
    public function where(string $column, string $operatorOrEqVal, string $val = null)
    {
        if (func_num_args() == 2) {
            list($val, $operatorOrEqVal) = [$operatorOrEqVal, '='];
        } else {
            $operator = ["=", "!=", ">", ">=", "<", "<=", "<>"];
            if (!in_array($operatorOrEqVal, $operator)) {
                //todo
                echo 'error,builder,where';
                exit;
                //                $this->throwExp('operator(' . $operatorOrEqVal . ') is forbidden');
            }
        }
        return $this->appendCondition($column, $operatorOrEqVal, $val);
    }

    /**
     * 不进行任何校对的条件
     * @param string $express
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     */
    public function whereRaw(string $express)
    {
        $this->appendBinding('where', $express);
        return $this->baseMysql;
    }

    /**
     * 添加is null条件
     * @param string $column
     * @return $this
     * @throws \Exception
     */
    public function whereIsNull(string $column)
    {
        $this->appendCondition($column, 'is', 'null', false);
        return $this;
    }

    /**
     * 添加is not null条件
     * @param string $column
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     * @throws \Exception
     */
    public function whereIsNotNull(string $column)
    {
        return $this->appendCondition($column, 'is', 'not null', false);
    }

    /** 添加in条件
     * @param string $column
     * @param array  $val
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     * @throws \Exception
     */
    public function whereIn(string $column, array $val)
    {
        return $this->appendCondition($column, 'in', $val);
    }

    /**
     *  添加notIn条件
     * @param string $column
     * @param array  $val
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     * @throws \Exception
     */
    public function whereNotIn(string $column, array $val)
    {
        return $this->appendCondition($column, 'not in', $val);
    }

    /**
     *  添加find_in_set条件
     * @param string $column
     * @param string $val
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     * @throws \Exception
     */
    public function whereFindInSet(string $column, string $val)
    {
        $column = $this->getWhereColumn($column, true);
        $val    = $this->wrapValQuotationMark($this->getDbHandel()->escape($val));
        $this->appendBinding('where', "find_in_set($val,$column)");
        return $this->baseMysql;
    }

    /**
     * like条件，左右包裹%
     * @param string $column
     * @param string $val
     * @param bool   $withLeftMark 是否左接 %, 如果column是索引在左接不存在时，将使用索引
     * @param bool   $withRightMark 是否右接 %
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     * @throws \Exception
     */
    public function whereLike(string $column, string $val, $withLeftMark = true, $withRightMark = true)
    {
        return $this->appendLikeCondition(' like ', $column, $val, $withLeftMark, $withRightMark);
    }

    /**
     * not like条件，左右包裹%
     * @param string $column
     * @param string $val
     * @param bool   $withLeftMark 是否左接 %, 如果column是索引在左接不存在时，将使用索引
     * @param bool   $withRightMark 是否右接 %
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     * @throws \Exception
     */
    public function whereNotLike(string $column, string $val, $withLeftMark = true, $withRightMark = true)
    {
        return $this->appendLikeCondition(' not like ', $column, $val, $withLeftMark, $withRightMark);
    }

    /**
     * 添加条件,会附带表别名前缀
     * @param string  $tableAlias
     * @param Closure $closure
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     */
    public function whereWithPrefixTableAlias(string $tableAlias, Closure $closure)
    {
        $this->whereTableAlias = $tableAlias;
        call_user_func_array($closure, [$this->baseMysql]);
        $this->whereTableAlias = null;
        return $this->baseMysql;
    }

    /**
     * like条件
     * @param string $operator
     * @param string $column
     * @param string $val
     * @param bool   $withLeftMark
     * @param bool   $withRightMark
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     * @throws \Exception
     */
    protected function appendLikeCondition(string $operator, string $column, string $val, $withLeftMark = true, $withRightMark = true)
    {
        $column = $this->getWhereColumn($column);
        $val    = $this->getDbHandel()->escape($val);
        if ($withLeftMark) {
            $val = '%' . $val;
        }
        if ($withRightMark) {
            $val .= '%';
        }
        $val = $this->wrapValQuotationMark($val);
        $this->appendBinding('where', $column . $operator . $val);
        return $this->baseMysql;
    }

    /**
     * 添加条件
     * @param      $column
     * @param      $operator
     * @param      $val
     * @param bool $isWrapVal
     * @return \Qyk\Mm\Dao\Mysql\BaseMysql
     * @throws \Exception
     */
    protected function appendCondition($column, $operator, $val, $isWrapVal = true)
    {
        if (is_array($val)) {
            $val = $this->getDbHandel()->escape_array($val);
            $val = implode("','", $val);
            $val = $this->wrapMark($val, "('", "')");
        } else {
            $val = $this->getDbHandel()->escape($val);
            if ($isWrapVal) {
                $val = $this->wrapValQuotationMark($val);
            }
        }
        $operator = $this->wrapMark($operator, ' ');
        $column   = $this->getWhereColumn($column);
        $this->appendBinding('where', $column . $operator . $val);
        return $this->baseMysql;
    }

    /**
     * 添加单引号
     * @param String $val
     * @return string
     */
    protected function wrapValQuotationMark(String $val)
    {
        return $this->wrapMark($val, "'");
    }

    /**
     * 包裹引号
     * @param $key
     * @param $prefixMark
     * @param $subfixMark
     * @return string
     */
    protected function wrapMark($key, $prefixMark, $subfixMark = null)
    {
        if (func_num_args() == 2) {
            $subfixMark = $prefixMark;
        }
        return $prefixMark . $key . $subfixMark;
    }

    /**
     * 获取包裹表别名后的条件字段
     * @param      $column
     * @param bool $ignoreLike
     * @return string
     * @throws \Exception
     */
    protected function getWhereColumn($column, $ignoreLike = true): string
    {
        $column = $this->getDbHandel()->escape($column, $ignoreLike);
        if ($this->whereTableAlias) {
            $column = $this->whereTableAlias . '.' . $column;
        } else if ($alias = $this->getAlias()) {
            $column = $alias . '.' . $column;
        }
        return $column;
    }

}