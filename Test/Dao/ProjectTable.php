<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/10
 * Time: 11:46
 */

namespace Test\Dao;


use Qyk\Mm\Dao\Mysql\BaseMysql;

class ProjectTable extends BaseMysql
{
    protected $db    = 'myBlog';
    protected $table = 'project';
}