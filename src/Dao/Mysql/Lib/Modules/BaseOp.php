<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;
/**
 * op的公共功能
 * Class OpInserter
 * @package Qyk\Mm\Dao\Mysql\lib
 */
abstract class BaseOp extends BaseModule
{
    /**
     * 获取sql
     * @return mixed
     */
    abstract public function getSql();

    /**
     * 执行sql
     * @return mixed
     * @throws \Exception
     */
    abstract function run();

    /**
     * 获取sql执行结果
     * @param BaseRt $rt
     * @param bool $isWriter sql是否是写操作
     * @return mixed
     */
    protected function getRt(BaseRt $rt, $isWriter = true)
    {
        $fn = function ($dbHandle, $sql, $isDirectBySql) {
            return $this->run($dbHandle, $sql, $isDirectBySql);
        };
        return $fn->call($rt, $this->getDbHandel($isWriter), $this->getSql(), $this->isDirectBySql());
    }
}