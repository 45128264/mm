<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Response;

/**
 * 输出
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class ResponseProvider extends Response
{
    private $rt;

    /**
     * 设置运行脚本结果
     * @param array $rt
     * @return mixed
     */
    function setControllerRt(array $rt = [])
    {
        $this->rt = $rt;
    }

    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'response';
    }

    /**
     * 渲染结果
     * @return mixed
     */
    public function render()
    {
        if ($this->throwable) {
            var_dump($this->throwable);
            return $this->renderThrowable();
        }
        return $this->renderNormal();
    }

    /**
     * 普通渲染
     */
    protected function renderNormal()
    {
        ob_start();
        ob_clean();
        if ($this->isJson) {
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode($this->rt, JSON_UNESCAPED_UNICODE);
        } else {
            if ($this->rt) {
                extract($this->rt);
            }
            //如果没有指定模板
            if (!isset($viewTpl)) {
                $viewTpl = $this->app->router->currentController . DIRECTORY_SEPARATOR . $this->app->router->currentMethod;
            }
            $viewTpl = APP_PATH . 'view' . DIRECTORY_SEPARATOR . $viewTpl . '.php';
            if (!file_exists($viewTpl)) {
                echo 'missing view tpl=>' . $viewTpl;
            } else {
                include($viewTpl);
            }
        }
        echo ob_get_clean();
        exit;
    }

    /**
     * 异常输出
     */
    protected function renderThrowable()
    {
        //todo exception
        echo 'exp,todo';
    }

    /**
     * 获取模板路径
     */
    private function getViewTpl()
    {

    }
}