<?php

namespace Qyk\Mm\Facade;

use Qyk\Mm\Route\Router;
use Qyk\Mm\Route\RouterContainer;
use Qyk\Mm\Stage;
use Throwable;

/**
 * response接口
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Response extends Facade
{
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
        try {
            $router     = RouterContainer::instance()->getRequestRouter();
            $controller = 'render' . $router->getResponseType() . 'Content';
            if (!method_exists($this, $controller)) {
                echo 'fuck';
                exit;
            }
            $this->router = $router;

            $content = $this->router->invokeController();
            $this->$controller($content);
            $router->terminate();
            $this->terminate();
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
    protected function getCsrfToken()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-.';
        $mask  = substr(str_shuffle(str_repeat($chars, 5)), 0, 8);
        $token = $this->generateCsrf();
        $app   = Stage::app();
        $app->session->setCookie($app->config->get('app.token.csrf'), $token);
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


}