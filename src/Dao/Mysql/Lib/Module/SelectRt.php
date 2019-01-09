<?php

namespace Qyk\Mm\Dao\Mysql\Lib\Module;
/**
 * mysql 操作结果
 * Class OpInserter
 * @package Qyk\Mm\Dao\Mysql\lib
 */
class SelectRt extends ModifyRt
{
    /**
     * 将结果转为array数组
     */
    public function toArray(): array
    {
        $items = [];
        while ($item = mysqli_fetch_assoc($this->executeRt)) {
            $items[] = $item;
        }
        mysqli_free_result($this->executeRt);
        return $items;
    }


    /**
     * 将结果转换成json
     */
    public function toJson()
    {
        $rt = $this->toArray();
        return json_encode($rt, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 将结果赋值给生成器
     */
    public function toGenerator()
    {
        while ($item = mysqli_fetch_assoc($this->executeRt)) {
            yield $item;
        }
        mysqli_free_result($this->executeRt);
    }
}

;