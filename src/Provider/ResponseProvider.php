<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Response;

/**
 * html输出
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class ResponseProvider extends Response
{
    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    public function getName(): string
    {
        return 'httpResponse';
    }

    /**
     * 渲染html内容
     */
    protected function renderHtmlContent()
    {
        $params  = $this->getContent();
        $viewTpl = $this->getTpl();
        if (!file_exists($viewTpl)) {
            echo 'missing view tpl=>' . $viewTpl;
        } else {
            extract($params);
            include($viewTpl);
        }
    }

    /**
     * 渲染json内容
     */
    protected function renderJsonContent()
    {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode($this->getContent(), JSON_UNESCAPED_UNICODE);
    }
}