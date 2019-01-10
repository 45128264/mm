<?php

namespace Qyk\Mm\Dao\Mysql;

use Exception;
use mysqli;
use Qyk\Mm\Dao\Mysql\Lib\Module\DbHandel;
use Qyk\Mm\Stage;
use Qyk\Mm\Traits\DebugTrait;
use Qyk\Mm\Traits\SingletonTrait;
use Throwable;

/**
 * mysql事务层级识别
 */
class MysqlTransaction
{
    use SingletonTrait, DebugTrait;

    /**
     * 事务对应的库
     * @var string
     */
    private $db = null;

    /**
     * 事物包裹层级
     * @var int
     */
    private $wrapNums = 0;

    /**
     * 是否允许事务多层嵌套
     * @var bool
     */
    private $isAllowWrap = false;

    /**
     * 默认的事务库别名
     * @var string
     */
    private $defaultDb;

    /**
     * 检测表操作必须是相同的数据库
     * @var bool
     */
    private $mustInSameDb;


    protected function __construct(string $aliasName = null)
    {
        $conf               = Stage::app()->config->get('mysql', 'transaction');
        $this->isAllowWrap  = $conf['isAllowWrap'];
        $this->defaultDb    = $conf['defaultDbAlias'];
        $this->mustInSameDb = $conf['mustInSameDb'];
    }

    /**
     * 获取当前事务包裹标志的层级
     * @return int
     */
    public function getWrapNums(): int
    {
        return $this->wrapNums;
    }

    /**
     * mysql事务不支持多层嵌套
     * 此函数支持多层嵌套，但事务的有效范围以最外层为准，建议不要使用多层嵌套，防止造成理解错误
     * rollback 当callback 抛出异常时
     * @param callable $callback
     * @throws Throwable
     */
    public function auto(callable $callback)
    {
        if (!$this->isAllowWrap && $this->wrapNums > 0) {
            throw new Exception('顶层事务设定不允许多层嵌套，请检测修改!');
        }
        try {
            $this->start();
            $callback();
            $this->commit();

        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * 开启事务
     * @throws Exception
     */
    protected function start()
    {
        ++$this->wrapNums;
        if ($this->wrapNums == 1) {
            $this->debugPrint('begin transaction');
            $this->getDbLink()->begin_transaction();
        }
    }

    /**
     * 回滚
     * @throws Exception
     */
    protected function rollback()
    {
        if ($this->wrapNums == 1) {
            $this->debugPrint('rollback');
            $this->getDbLink()->rollback();
            $this->db = null;
        }
        --$this->wrapNums;
    }

    /**
     * 提交
     * @throws Exception
     */
    protected function commit()
    {
        if ($this->wrapNums == 1) {
            $this->debugPrint('commit');
            $this->getDbLink()->commit();
            $this->db = null;
        }
        --$this->wrapNums;
    }


    /**
     * 获取连接
     * @return mysqli
     * @throws Exception
     */
    protected function getDbLink()
    {
        return DbHandel::instance($this->getDb())
            ->setIsWriter(true)
            ->getDBLink();
    }

    /**
     * 指定db
     * @return string
     */
    public function getDb(): string
    {
        //如果没有指定对应的数据库，将使用配置文件里边的设置
        if (!$this->db) {
            $this->db = $this->defaultDb;
        }
        return $this->db;
    }

    /**
     * 指定对应的数据库
     * @param string $db
     * @return MysqlTransaction
     */
    public function setDb(string $db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     * 判断事务是否包含其他库的表操作
     * @param string $db
     * @throws Exception
     */
    public function checkInSameDb(string $db)
    {
        if ($this->mustInSameDb && $this->db != $db) {
            throw new Exception('不支持XA事务，事务必须使用同一库配置');
        }
    }
}

?>