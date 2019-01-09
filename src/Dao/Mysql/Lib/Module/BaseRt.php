<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;

use mysqli;
use Qyk\Mm\Traits\SingletonTrait;

/**
 * mysql 操作结果
 * Class OpInserter
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class BaseRt
{
    use SingletonTrait;

    /**
     * @var mysqli
     */
    protected $dbLink;
    /**
     * @var
     */
    protected $executeRt;

    /**
     * 执行
     * @param DbHandel $dbHandel
     * @param string   $sql
     * @param bool     $isDetectSql
     * @return $this
     * @throws \Exception
     */
    public function run(DbHandel $dbHandel, string $sql, $isDetectSql = false)
    {
        $this->dbLink    = $dbHandel->getDBLink();
        $this->executeRt = $dbHandel->excute($sql, $isDetectSql);
        return $this;
    }

    /**
     * 获取编辑影响的数据个数
     * Returns the number of rows affected by the last INSERT, UPDATE, REPLACE or DELETE query.
     * For SELECT statements mysqli_affected_rows() works like mysqli_num_rows().
     */
    public function getAffectedRows()
    {
        return mysqli_affected_rows($this->dbLink);
    }
}