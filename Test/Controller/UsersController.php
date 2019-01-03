<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 15:51
 */

namespace Test\Controller;

use http\Exception;

/**
 * 用户
 * Class UsersController
 * @package Test\Controller
 */
class UsersController
{
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
        return [
            'user_id' => $userId,
            'card_id' => $cardId,
        ];
    }
}