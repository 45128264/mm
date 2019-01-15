<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 15:51
 */

namespace Test\Controller;

use http\Exception;
use Qyk\Mm\Dao\Mysql\MysqlTransaction;
use Qyk\Mm\Dao\Redis\RedisHelper;
use Qyk\Mm\Traits\DebugTrait;
use Test\Dao\Project1Table;
use Test\Dao\ProjectTable;

/**
 * 用户
 * Class UsersController
 * @package Test\Controller
 */
class UsersController
{
    use DebugTrait;
    public function login()
    {
        return [
            'rt'  => 'success',
            'msg' => 'hello'
        ];
    }

    public function cardList(int $userId)
    {
        return [
            'user_id' => $userId
        ];
    }

    public function cardDetail(int $userId, int $cardId)
    {

        //        MysqlTransaction::instance()->auto([$this, 'test']);
        $help = RedisHelper::instance();
        //        $rt = RedisHelper::instance()->set('test1', 121, ['nx', 'ex' => 10]);

        $this->debugPrint();
        $keyval = ['test' => '1212', 'test1' => 'test1'];
        $params = ['nx', 'ex' => 60];
        $rt     = $help->lRem($keyval, $params);

        return [
            'user_id' => $userId,
            'card_id' => $cardId,
            'rt'      => $rt
        ];
    }

    public function test()
    {
        ProjectTable::instance()->insert('content', 11)->run();
    }
}