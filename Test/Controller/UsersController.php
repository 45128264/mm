<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 15:51
 */

namespace Test\Controller;

use Qyk\Mm\Dao\Redis\RedisHelper;
use Qyk\Mm\Provider\SessionProvider;
use Qyk\Mm\Stage;
use Qyk\Mm\Traits\DebugTrait;
use Qyk\Mm\Utils\Captcha\Captcha;
use Qyk\Mm\Utils\HttpCurl;
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

    public function sessionSave()
    {
        Stage::app()->session->set('user', ['id' => 105, 'name' => 'hello world']);
        return ['rt' => true];
    }

    public function sessionShow()
    {
        $rt = Stage::app()->session->get('user.id');
        return ['rt' => $rt];
    }

    public function curlGetClient()
    {
        $userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36 OPR/54.0.2952.54";
        $uri       = 'www.myblog.com/mm/users/curl/service';
        $rt        = HttpCurl::instance()
            ->useRandClientIp()
            ->setUserAgent($userAgent)
            ->doGet($uri);
        return $rt;
    }

    public function curlGetServer()
    {
        return $_SERVER;
    }


    /**
     * @param int $width
     * @param int $height
     * @param int $font_size
     */
    public function captchaIndex($width = 180, $height = 50, $font_size = 26)
    {
        (new Captcha)
            ->mulSetAttribute(['width' => $width, 'height' => $height, 'font_size' => $font_size])
            ->outPutImg();

    }

    /**
     */
    public function captchaVerrify($code)
    {
        $rt = (new Captcha)
            ->validate($code);
        var_dump($rt);

    }
}