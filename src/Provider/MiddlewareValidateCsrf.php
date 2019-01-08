<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 10:56
 */

namespace Qyk\Mm\Provider;

use Exception;
use Qyk\Mm\Facade\Middleware;
use Qyk\Mm\Stage;

/**
 * 验证csrf是否正确
 * Class MiddlewareValidateCsrf
 * @package Qyk\Mm\Provider
 */
class MiddlewareValidateCsrf extends Middleware
{
    protected $expMsg = 'csrf验证失败';

    /**
     * 执行,如果执行成功返回true,反之false
     * @return bool
     * @throws Exception
     */
    protected function handle(): bool
    {
        $method = Stage::app()->request->getMethod();
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }
        $conf = Stage::app()->config->get('app.request.csrf');
        if (!isset($conf['trueTokenKey'])) {
            throw new Exception('missing csrf conf');
        }
        //region 如果是测试跳过csrf验证
        $isDebug = $conf['debug'] ?? false;
        if ($isDebug) {
            return true;
        }
        //endregion
        $trueToken  = $conf['trueTokenKey'];
        $csrfPost   = $conf['postKey'];
        $csrfHeader = $conf['headerKey'];

        $trueToken  = $_COOKIE[$trueToken];
        $test       = @hash_hmac('sha256', '', '', false);
        $hashLength = mb_strlen($test, '8bit');
        $trueToken  = unserialize(mb_substr($trueToken, $hashLength, mb_strlen($trueToken, '8bit'), '8bit'))[1];
        $token      = $_POST[$csrfPost] ?? ($_SERVER[$csrfHeader] ?? null);

        $token = base64_decode(str_replace('.', '+', $token));
        $n     = mb_strlen($token, '8bit');
        if ($n <= 8) {
            return false;
        }
        $mask  = mb_substr($token, 0, 8, '8bit');
        $token = mb_substr($token, 8, $n - 8, '8bit');

        $n1 = mb_strlen($mask, '8bit');
        $n2 = mb_strlen($token, '8bit');
        if ($n1 > $n2) {
            $token = str_pad($token, $n1, $token);
        } elseif ($n1 < $n2) {
            $mask = str_pad($mask, $n2, $n1 === 0 ? ' ' : $mask);
        }
        $token = $mask ^ $token;
        return $token === $trueToken;
    }
}