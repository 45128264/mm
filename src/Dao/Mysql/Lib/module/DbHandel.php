<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;

use Exception;
use mysqli;
use Qyk\Mm\Dao\Mysql\MysqlTransaction;
use Qyk\Mm\Stage;
use Qyk\Mm\Traits\SingletonTrait;

/**
 * mysql 的更新功能
 * Class OpInserter
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class DbHandel
{
    use SingletonTrait;
    /**
     * 数据库命
     * @var string
     */
    private $db;

    /**
     * mysql连接
     * @var array
     */
    private $dbLink = [];
    /**
     * 是否是写操作
     * @var bool
     */
    private $isWriter = true;

    protected function __construct(string $db)
    {
        $this->db = $db;
    }

    /**
     * escape sql string
     * @param string $var
     * @param bool   $ignoreLike
     * @return string
     * @throws Exception
     */
    public function escape($var, $ignoreLike = false)
    {
        $rt = mysqli_real_escape_string($this->getDBLink(), $var);
        if ($ignoreLike) {
            return $rt;
        }
        return str_replace(['%', '_'], ['\\%', '\\_'], $rt);
    }

    /**
     * escape array
     * @param string $items
     * @return string
     */
    public function escape_array($items)
    {
        return array_map([$this, 'escape'], $items);
    }

    /**
     * 获取对应的dblink
     * @return mysqli
     * @throws Exception
     */
    public function getDBLink()
    {
        $conf = Stage::app()->config->get('mysql', $this->db);
        if ($this->isWriter) {    //如果是写操作
            $type = 'master';
        } else {
            $isSlave = $conf['rw_separate'] && MysqlTransaction::getWrapNums() == 0;
            $type    = $isSlave ? 'slave' : 'master';
        }

        if (isset($this->dbLink[$type])) {
            return $this->dbLink[$type];
        }

        //判断单前是否是master连接
        if ($type == 'master') {
            $dbConf = $conf['master'];
            $dbConf = $dbConf[array_rand($dbConf)];
        } else {
            $dbConf  = $conf['slave'];
            $slaveNo = $dbConf['slave_no'] ?: array_rand($dbConf);
            $dbConf  = $dbConf[$slaveNo];
        }


        $this->dbLink[$type] = mysqli_connect($dbConf['host'], $dbConf['user'], $dbConf['password'], $dbConf['database'], $dbConf['port']);
        if (!$this->dbLink[$type]) {
            throw new Exception("database connect failed,code:" . mysqli_connect_errno() . ',msg:' . mysqli_connect_error());
        }
        if ($conf['charset']) {
            $sql    = "SET NAMES {$conf['charset']}";
            $result = mysqli_query($this->dbLink[$type], $sql);
            if (!$result) {
                throw new Exception("query failed,sql:[{$sql}],code:" . mysqli_errno($this->db_link[$type]) . ',msg:' . mysqli_error($this->db_link[$type]));
            }
        }
        return $this->dbLink[$type];
    }


    /**
     * 执行sql并返回结果
     * Returns FALSE on failure.
     * For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will return a mysqli_result object.
     * For other successful queries mysqli_query() will return TRUE.
     * @param      $sql
     * @param bool $isDetectSql
     * @return bool|\mysqli_result
     * @throws Exception
     */
    public function excute($sql, $isDetectSql = false)
    {
        if (Defined('DEBUG_MYSQL') && DEBUG_MYSQL) {
            echo 'execute=>' . PHP_EOL;
            echo $sql . PHP_EOL;
        }
        if ($isDetectSql) {
            $this->detectSqlLimitColumns($sql);
        }
        if (!$result = mysqli_query($this->getDBLink(), $sql)) {
            throw new Exception('execute sql failed,error:' . mysqli_error($this->db_link) . ',sql:[' . $sql . ']');

        }
        return $result;
    }

    /**
     * 检查sql是有错误的关键字眼
     * @param $sql
     * @throws Exception
     */
    protected function detectSqlLimitColumns($sql)
    {
        $limitColumns = [
            'information_schema',
            'tables',
            '#',
            '//',
            '--',
            'drop',
            'rename',
            'truncate',
            'grant',
            'alter',
            'remove',
            'privileges'
        ];
        $sql          = strtolower($sql);
        foreach ($limitColumns as $limitColumn) {
            if (strpos($sql, $limitColumn) > -1) {
                throw  new Exception('execute sql failed,error: with limitColumns, sql:[' . $sql . ']');
            }
        }
    }

    /**
     * 是否是写操作
     * @param bool $isWriter
     * @return DbHandel
     */
    public function setIsWriter($isWriter = true)
    {
        $this->isWriter = $isWriter;
        return $this;
    }

}