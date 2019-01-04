<?php

class t
{

}

$a = new t();
echo is_callable($a) ? 'true' : 'false';


function t($a = 1, $b = 2)
{
    $t = compact('a', 'b');
    var_dump($t);
}

t();
echo 'ss';