<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;
/**
 * mysql 操作结果
 * Class OpInserter
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class ModifyRt extends BaseRt
{
    /**
     * 获取执行结果
     */
    public function getRt(): bool
    {
        return $this->executeRt;
    }
};