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
}