<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/23
 * Time: 11:42
 */

namespace Qyk\Mm\Utils;

use Exception;
use Generator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Settings;
use Qyk\Mm\Traits\SingletonTrait;

/**
 * excel适配器
 * Class ExcelHelper
 * @package Qyk\Mm\Utils
 */
class ExcelHelper
{
    use SingletonTrait;

    /**
     * 表单对应的字段数据
     * @var array ['key'=>'tittle',...]
     */
    protected $sheetIndex = [];
    protected $totalSheet = 0;

    /**
     * @var PHPExcel
     */
    protected $objPhpExcel;

    /**
     * 文件头部coordinate
     * @var array
     */
    protected $headerChars = [];

    /**
     * 是否有进行文件保存，如果有，下载直接使用此文件
     * @var string
     */
    protected $storeFilePath = null;

    protected function __construct()
    {
        $this->objPhpExcel = new PHPExcel();
        $this->headerChars = range('A', 'Z');
    }

    /**
     * 创建
     * @param string $sheet
     * @param array  $tittleKey ['key'=>'tittle',...]
     * @param array  $data ['key'=>'value',...]
     * @param int    $columnWidth 字段宽度
     * @return ExcelHelper
     * @throws \PHPExcel_Exception
     */
    public function create(string $sheet, array $tittleKey, array $data, int $columnWidth = 20)
    {
        return $this->doneCreate($sheet, $tittleKey, $data, $columnWidth);
    }

    /**
     * 创建
     * @param string    $sheet
     * @param array     $tittleKey ['key'=>'tittle',...]
     * @param Generator $data => ['key'=>'value',...]
     * @param int       $columnWidth 字段宽度
     * @return ExcelHelper
     * @throws \PHPExcel_Exception
     */
    public function createByGenerator(string $sheet, array $tittleKey, Generator $data, int $columnWidth = 20)
    {
        return $this->doneCreate($sheet, $tittleKey, $data, $columnWidth);
    }

    /**
     * 保存文件
     * @param string $filePath
     * @param string $extension xls | xlsx
     * @return ExcelHelper
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function store(string $filePath, string $extension = 'xls')
    {
        $writerType = $extension == 'xls' ? 'Excel5' : 'Excel2017';
        $objWriter  = PHPExcel_IOFactory::createWriter($this->objPhpExcel, $writerType);
        $objWriter->save($filePath);
        $this->storeFilePath = $filePath;
        return $this;
    }

    /**
     * 创建
     * @param $sheet
     * @param $tittleKey
     * @param $data
     * @param $columnWidth
     * @return $this
     * @throws \PHPExcel_Exception
     */
    protected function doneCreate($sheet, $tittleKey, $data, $columnWidth)
    {
        $this->createSheet($sheet, $tittleKey, $columnWidth);
        foreach ($data as $item) {
            foreach ($item as $key => $val) {
                $this->appendCellValue($sheet, $key, $val);
            }
            $this->sheetIndex[$sheet]['activeRowIndex']++;
        }
        return $this;
    }

    /**
     * 刷新缓存,请求之前的缓存数据
     * @return $this
     */
    public function refresh()
    {
        $this->sheetIndex    = [];
        $this->totalSheet    = 0;
        $this->storeFilePath = null;
        return $this;
    }

    /**
     * 导出文件
     * @param string $filename
     */
    public function export(string $filename)
    {
        (new FileHelper())->exportExcel($filename, function () use ($filename) {
            if (!$this->storeFilePath) {
                $extension  = array_pop(explode('.', $filename));
                $writerType = $extension == 'xls' ? 'Excel5' : 'Excel2017';
                $objWriter  = PHPExcel_IOFactory::createWriter($this->objPhpExcel, $writerType);
                $objWriter->save('php://output');
            } else {
                readfile($this->storeFilePath);
            }
        });
    }

    /**
     * 创建表单，或者选择已有的表单
     * @param string $sheet
     * @param array  $tittleKey
     * @param int    $columnWidth
     * @throws \PHPExcel_Exception
     */
    protected function createSheet(string $sheet, array $tittleKey, int $columnWidth)
    {
        if (isset($this->sheetIndex[$sheet])) {
            $diff = array_diff_key($this->sheetIndex[$sheet]['keyCoordinate'], $tittleKey);
            if ($diff) {
                throw new Exception('sheet=>' . $sheet . ';多次定义，存在有差异的字段数据，请修改=>' . json_encode($diff, JSON_UNESCAPED_UNICODE));
            }
            $sheetIndex = $this->sheetIndex[$sheet]['sheetIndex'];
            $this->objPhpExcel->setActiveSheetIndex($sheetIndex);
        } else {
            $this->objPhpExcel->setActiveSheetIndex($this->totalSheet)->setTitle($sheet);
            $keyCoordinate = [];
            $i             = 0;
            foreach ($tittleKey as $key => $val) {
                if ($i >= 24) {
                    $prefix     = floor($i / 24) - 1;
                    $suffix     = $i % 24;
                    $coordinate = $this->headerChars[$prefix] . $this->headerChars[$suffix];
                } else {
                    $coordinate = $this->headerChars[$i];
                }
                $keyCoordinate[$key] = $coordinate;
                $this->objPhpExcel->getActiveSheet()->setCellValue($coordinate . '1', $val);
                $this->objPhpExcel->getActiveSheet()->getColumnDimension($coordinate)->setWidth($columnWidth);
                $i++;
            }
            $this->sheetIndex[$sheet]['keyCoordinate']  = $keyCoordinate;
            $this->sheetIndex[$sheet]['sheetIndex']     = $this->totalSheet;
            $this->sheetIndex[$sheet]['activeRowIndex'] = 2;
            $this->totalSheet++;
        }
    }

    //region set document properties

    /**
     * 设置文档参数
     * @param $creator
     * @return ExcelHelper
     */
    public function setPropertiesCreator(string $creator)
    {
        $this->objPhpExcel->getProperties()->setCreator($creator);
        return $this;
    }

    /**
     * 最近修改者
     * @param string $modified
     * @return ExcelHelper
     */
    public function setPropertiesLastModifiedBy(string $modified)
    {
        $this->objPhpExcel->getProperties()->setLastModifiedBy($modified);
        return $this;
    }

    /**
     * 设置文档参数
     * @param string $tittle
     * @return ExcelHelper
     */
    public function setPropertiesTitle(string $tittle)
    {
        $this->objPhpExcel->getProperties()->setTitle($tittle);
        return $this;
    }

    /**
     * 设置文档参数
     * @param string $subject
     * @return ExcelHelper
     */
    public function setPropertiesSubject(string $subject)
    {
        $this->objPhpExcel->getProperties()->setSubject($subject);
        return $this;
    }

    /**
     * 设置文档参数
     * @param string $desc
     * @return ExcelHelper
     */
    public function setPropertiesDescription(string $desc)
    {
        $this->objPhpExcel->getProperties()->setDescription($desc);
        return $this;
    }

    /**
     * 设置文档参数
     * @param string $keyword
     * @return ExcelHelper
     */
    public function setPropertiesKeywords(string $keyword)
    {
        $this->objPhpExcel->getProperties()->setKeywords($keyword);
        return $this;
    }

    /**
     * 设置文档参数
     * @param string $category
     * @return ExcelHelper
     */
    public function setPropertiesCategory(string $category)
    {
        $this->objPhpExcel->getProperties()->setCategory($category);
        return $this;
    }

    /**
     * 设置文档参数
     * @param string $company
     * @return ExcelHelper
     */
    public function setPropertiesCompany(string $company)
    {
        $this->objPhpExcel->getProperties()->setCompany($company);
        return $this;
    }

    /**
     * 添加字段数据
     * @param string $sheet
     * @param        $key
     * @param        $val
     * @throws \PHPExcel_Exception
     */
    protected function appendCellValue(string $sheet, string $key, string $val)
    {
        $prefix = $this->sheetIndex[$sheet]['keyCoordinate'][$key];
        $suffix = $this->sheetIndex[$sheet]['activeRowIndex'];
        $this->objPhpExcel->getActiveSheet()->setCellValue($prefix . $suffix, $val);
    }
    //endregion
}