<?php

namespace Qyk\Mm\Facade;

use Qyk\Mm\Route\Router;
use Qyk\Mm\Route\RouterContainer;
use Qyk\Mm\Stage;
use Qyk\Mm\Traits\TimeOutLogTrait;
use Throwable;

/**
 * response接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Response extends Facade
{
    use TimeOutLogTrait;

    /**
     * @var Router
     */
    private $router;

    /**
     * 获取当前Facade对应别名
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'response';
    }

    /**
     * 渲染结果
     * @return mixed
     * @throws Throwable
     */
    public function render()
    {
        $controller = '';
        $this->tickStart();
        try {
            $router     = RouterContainer::instance()->getRequestRouter();
            $controller = 'render' . $router->getResponseType() . 'Content';
            if (!method_exists($this, $controller)) {
                echo 'missing method=>' . $controller;
                exit;
            }
            $this->router = $router;
            $this->router->invokeBeforeMiddleware();
            $content = $this->router->invokeController();
            $this->$controller($content);
            $router->invokeAfterMiddleware();
            $this->terminate();
            $this->tickEnd(60);

        } catch (throwable $e) {
            if ($controller) {
                $controller .= 'Error';
                $this->$controller($e);
            } else {
                echo '系统繁忙，请稍后再试';
            }
            $this->terminate();
            throw $e;
        }
    }


    /**
     * ^异或运算
     * 不一样返回1 否者返回 0
     * 在PHP语言中,经常用来做加密的运算,解密也直接用^就行
     * 字符串运算时 利用字符的ascii码转换为2进制来运算
     * @return mixed
     */
    protected function createCsrfToken()
    {
        $trueToken = $this->generateCsrf();
        $csrfToken = $this->createCsrfCookie($trueToken);
        $app       = Stage::app();
        $conf      = $app->config->get('app.request.csrf');
        $trueKey   = $conf['trueTokenKey'];
        $app->session->setCookie($trueKey, $this->hashData(serialize([$trueKey, $trueToken]), 'platformtest'));
        $app->session->setCookie($conf['tokenKey'], $csrfToken);

        return $csrfToken;
    }

    /**
     * @param $token
     * @return string
     */
    public function createCsrfCookie($token)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-.';
        $mask  = substr(str_shuffle(str_repeat($chars, 5)), 0, 8);
        return str_replace('+', '.', base64_encode($mask . $this->xorTokens($token, $mask)));
    }

    /**
     * 获取随机字符串
     * @param int $len
     * @return string
     */
    protected function generateCsrf($len = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code  = '';
        for ($i = 0; $i < $len; $i++) {
            $code .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $code;
    }

    /**
     * arg1比 arg1比 arg2短,arg1要用自动补齐补到和和 arg1要用自身补齐补到和和 arg2一样的长度
     * 因为在php里
     * 'a'^'bc' 会只算 a^b 而不考虑c了,这里采用了向长度更长的来补
     * 如果用
     * xorTokens来处理 'a'和'bc'
     * 会先把a用自己填充到和bc一样的长度后再进行异或运算
     * @param $token1
     * @param $token2
     * @return int
     */
    private function xorTokens($token1, $token2)
    {
        $n1 = mb_strlen($token1, '8bit');
        $n2 = mb_strlen($token2, '8bit');
        if ($n1 > $n2) {
            $token2 = str_pad($token2, $n1, $token2);
        } elseif ($n1 < $n2) {
            $token1 = str_pad($token1, $n2, $n1 === 0 ? ' ' : $token1);
        }

        return $token1 ^ $token2;
    }

    /**
     * 获取html模板别名
     * @return string
     */
    protected function getTpl(): string
    {
        return $this->router->getTplPath();
    }

    /**
     * 获取html模板路径
     * @return string
     */
    protected function getFullTplPath(string $alias): string
    {
        if (!$alias) {
            return false;
        }
        return APP_TEMPLE_PATH . '/' . str_replace('.', '/', $alias) . '.php';
    }

    /**
     * 渲染html内容
     * @param array $content
     * @return mixed
     */
    abstract protected function renderHtmlContent(array $content);

    /**
     * 渲染html内容，失败
     * @param Throwable $e
     * @return mixed
     */
    abstract protected function renderHtmlContentError(throwable $e);

    /**
     * 渲染json内容
     * @param array $content
     * @return mixed
     */
    abstract protected function renderJsonContent(array $content);

    /**
     * 渲染json内容失败
     * @param Throwable $e
     * @return mixed
     */
    abstract protected function renderJsonContentError(throwable $e);

    /**
     * 关闭操作
     */
    protected function terminate()
    {
        foreach (Stage::app()->terminateContainer as $callback) {
            $callback();
        }
    }

    /**
     * 获取csrf对应的form变量名称
     */
    protected function getCsrfPostKey()
    {
        return Stage::app()->config->get('app.request.csrf.postKey');
    }

    private function hashData($data, $key)
    {
        $hash = hash_hmac('sha256', $data, $key);
        return $hash . $data;
    }
}