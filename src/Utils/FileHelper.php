<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/23
 * Time: 16:16
 */

namespace Qyk\Mm\Utils;

use ZipArchive;

/**
 * 导出
 * Class DownloadHelper
 * @package Qyk\Mm\Utils
 */
class FileHelper
{
    /**
     * 导出指定的的excel
     * @param string   $filename
     * @param callable $getContent
     */
    public function exportExcel(string $filename, callable $getContent)
    {
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $getContent();
    }


    /**
     * 将目标文件都打包进zipPath
     * @param string $zipPath
     * @param bool   $isRemoveSource 是否要删除原文件
     * @param String ...$filePath
     * @return FileHelper
     */
    public function groupZip(string $zipPath, bool $isRemoveSource, String ...$filePath)
    {
        $zip     = new ZipArchive();
        $zipType = is_file($zipPath) ? ZipArchive::OVERWRITE : ZipArchive::CREATE;
        if ($zip->open($zipPath, $zipType) === true) {
            foreach ($filePath as $item) {
                $zip->addFile($item);
            }
            $zip->close();
            if ($isRemoveSource) {
                foreach ($filePath as $item) {
                    unlink($item);
                }
            }
        }
        return $this;
    }

    /**
     * 导出2进制文件
     * @param string $contentType 文件内容类型
     * @param string $filename 导出文件名
     * @param string $filePath 源文件路径
     */
    public function exportBinary(string $contentType, string $filename, string $filePath)
    {
        // Redirect output to a client’s web browser
        header('Content-Type: application/' . $contentType);
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        header("Content-Transfer-Encoding: binary");//声明一个下载的文件
        header('Content-Length: ' . filesize($filePath));//声明文件大小
        readfile($filePath);
    }
}