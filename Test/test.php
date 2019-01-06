<?php

class t
{
    public function test()
    {
        $tt = [$this, 'tt'];
        $tt();
    }

    protected function tt()
    {
        echo 'this is test';
    }
}

$a    = new t();
$test = [$a, 'test'];
//echo is_callable($a) ? 'true' : 'false';
//echo is_callable($test) ? 'true' : 'false';
$test();

//
//function t($a = 1, $b = 2)
//{
//    $t = compact('a', 'b');
//    var_dump($t);
//}
//
//t();
//echo 'ss';

echo PHP_EOL;
if (($tt = 1) && $tt) {
    echo 'ssss';
}
echo 'xxxs';
var_dump($tt);