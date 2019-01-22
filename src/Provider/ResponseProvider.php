<?php

namespace Qyk\Mm\Provider;

use Qyk\Mm\Facade\Response;
use Qyk\Mm\Stage;
use Throwable;

/**
 * html输出
 * Class Cache
 * @package Qyk\Mm\Provider
 */
class ResponseProvider extends Response
{
    /**
     * 渲染html内容
     * @param array $params
     */
    protected function renderHtmlContent(array $params)
    {
        if (!isset($params['viewTpl'])) {
            $viewTpl = $this->getTpl();
        }
        extract($params);
        $viewTpl = $this->getFullTplPath($viewTpl);
        if (!file_exists($viewTpl)) {
            echo 'missing view tpl=>' . $viewTpl;
        } else {
            include($viewTpl);
        }
    }

    /**
     * 渲染json内容
     * @param array $content
     */
    protected function renderJsonContent(array $content)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode($content, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 渲染html内容失败
     * @param Throwable $e
     * @return mixed|void
     */
    protected function renderHtmlContentError(throwable $e)
    {
        $isShowExp = Stage::app()->config->get('app.runtime.debug.show_exp');
        if ($isShowExp) {
            $this->renderHtmlContent([
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'msg'     => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'code'    => $e->getCode(),
                'viewTpl' => Stage::app()->config->get('app.runtime.debug.exp_view_tpl')
            ]);
        } else {
            $this->renderHtmlContent([
                'viewTpl' => Stage::app()->config->get('app.runtime.exp_view_tpl')
            ]);
        }
    }

    /**
     * 渲染json内容失败
     * @param Throwable $e
     * @return mixed|void
     */
    protected function renderJsonContentError(throwable $e)
    {
        $isShowExp = Stage::app()->config->get('app.runtime.debug.show_exp');
        if ($isShowExp) {
            $contents = ['result' => false, 'msg' => 'exp', 'data' => [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                //                'msg'   => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code'  => $e->getCode(),
            ]];
        } else {
            $contents = ['result' => false, 'msg' => '系统繁忙，稍后再试'];
        }
        $this->renderJsonContent($contents);
    }
}