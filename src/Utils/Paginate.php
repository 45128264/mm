<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/24
 * Time: 11:08
 */

namespace Qyk\Mm\Utils;

use Qyk\Mm\Traits\SingletonTrait;

/**
 * 分页
 * Class Paginate
 * @package Qyk\Mm\Utils
 */
class Paginate
{
    use SingletonTrait;

    protected $totalSize;
    protected $pageSize     = 10;
    protected $currentPage  = 0;
    protected $aliasListRow = '';
    protected $aliasPage    = '';
    protected $uriParams    = [];
    protected $methodRouter;
    protected $rollPage;

    /**
     * 设置分页显示
     * @param int $totalSize 总数
     * @param int $pageSize 每页条数
     * @param int $currentPage 当前页
     * @param int $rollPage 分页栏每页显示的页数
     * @return Paginate
     */
    public function setListInfo(int $totalSize, int $pageSize, int $currentPage, int $rollPage = 10)
    {
        $this->totalSize   = $totalSize;
        $this->pageSize    = $pageSize;
        $this->currentPage = $currentPage;
        $this->rollPage    = $rollPage;
        return $this;
    }

    /**
     * 设置路由,默认使用当前的路由
     * @param string $methodRouter
     * @return Paginate
     */
    public function setMethod(string $methodRouter)
    {
        $this->methodRouter = $methodRouter;
        return $this;
    }

    /**
     * 获取html格式的分页
     * @param bool $withTip 附带信息提示
     * @param bool $withPerPage 附带每页条数选项
     * @param bool $withGoTo 附带跳转
     * @return string
     */
    public function getHtml($withTip = false, $withPerPage = false, $withGoTo = false)
    {
        $paginate = $this->getArray();
        if (empty($paginate)) {
            return '';
        }
        $html = [$this->getPageListHtml()];
        if ($withTip) {
            $html[] = '共' . $this->totalSizePages . '页 ( ' . $this->totalSizeRows . '条数据 )';
        }
        if ($withPerPage) {
            $perpage_list    = [10, 20, 50, 100];
            $perpage_options = '';
            foreach ($perpage_list as $v) {
                $perpage_options .= '<option value="' . $v . '"' . ($this->listRows == $v ? ' selected' : '') . ' >' . $v . '</option>';
            }
            $html[] = '每页 <select class="per_page form-control" name="perpage" style="display:inline-block;padding:2px; height:auto" onchange="location.href=\'' . $this->url(1) . '&perpage=\'+this.value">' . $perpage_options . '</select> 条数据 ';
        }
        if ($withGoTo) {
            $html[] = '跳转到<input type="text" class="form-control" style="display:inline-block;padding:2px; height:auto;width:48px;vertical-align: middle;" onkeyup="this.value = this.value.replace(/[^0-9]\D*$/,\'\')" title="输入页码，回车" value="' . $this->currentPage . '" onkeydown="if(this.value && event.keyCode==13){location.href=\'' . $this->url() . '&' . $this->varPage . '=\' + this.value}"> 页';
        }
        return '<div class="page-list">' . implode('', $html) . '</div>';
    }

    /**
     * 设置路由对应的参数，默认使用$_GET对应的参数
     * @param array $params
     * @return Paginate
     */
    public function setUriParams(array $params)
    {
        $this->uriParams = $params;
        return $this;
    }

    /**
     * 获取json格式的分页
     * @return string
     */
    public function getJson()
    {
        return json_encode($this->getArray());
    }

    /**
     * 获取array格式的分页
     * @return array
     */
    public function getArray()
    {
        if ($this->totalSize <= 0 || $this->currentPage <= 0 || $this->pageSize <= 0) {
            return [];
        }
        return [
            'totalSize'   => $this->totalSize,        // 总页码
            'currentPage' => $this->currentPage,      // 单前页码
            'pageSize'    => $this->pageSize,      // 每页条数
            'aliasPage'   => $this->aliasPage,    // 分页对应的变量别名
            'uri'         => '',                  // 对应的uri
            'params'      => [],                  // 参数
        ];
    }


    /**
     * 刷新
     * @return $this
     */
    protected function refresh()
    {
        $this->methodRouter = '';
        $this->uriParams    = $_GET;
        $this->totalSize    = 0;
        return $this;
    }

    /**
     * 获取page的list
     */
    protected function getPageListHtml()
    {
        /**
         * 创建li
         * @param string $desc
         * @param string $url
         * @return string
         */
        $buildLi = function (string $desc, string $url = '') {
            return ' <li><a href = "121">' . $desc . '</a>';
        };

        $midPages = ceil($this->pageSize / 2);
        // 分页总数
        $pages = ceil($this->totalSize / $this->pageSize);

        $html = [];

        // 上一页
        if ($this->currentPage > 1) {
            $html['prev'] = $buildLi('<span aria-hidden="true">&lt;</span>');
        }

        // 下一页
        if ($this->currentPage < $pages) {
            $html['next'] = $buildLi('<span aria-hidden="true">&gt;</span>');
        }
        //第一页
        if ($pages > $this->rollPage && ($this->currentPage - $midPages) >= 1) {
            $html['first'] = $buildLi('<span aria-hidden="true"> «</span>');
        }
        //最后一页
        if ($pages > $this->rollPage && ($this->currentPage + $midPages) < $pages) {
            $html['last'] = $buildLi('<span aria-hidden="true"> «</span>');
        }
        for ($i = 1; $i <= $this->rollPage; $i++) {
            if ($this->currentPage - $midPages <= 0) {
                $pageNum = $i;
            } elseif (($this->currentPage + $midPages) > $pages) {
                $pageNum = $pages - $this->rollPage + $i;
            } else {
                $pageNum = $this->currentPage - $midPages + $i;
            }
            if ($pageNum > $pages) {
                break;
            }
            if ($pageNum == $this->currentPage) {
                $html['main'][] = $buildLi($pageNum . '<span class="sr-only"> (currentPage)</span>', '#');
            } else {
                $html['main'][] = $buildLi($pageNum);
            }
        }
        return '<ul class="pagination" style="display: inline-block;vertical-align: middle;">'
            . ($html['first'] ?? '')
            . ($html['prev'] ?? '')
            . ($html['main'] ? implode('', $html['main']) : '')
            . ($html['next'] ?? '')
            . ($html['last'] ?? '')
            . '</ul>';
    }

    /**
     * 获取uil
     * @param int $page
     */
    protected function getUri(int $page)
    {

    }
}