<?php

class singleton
{
    /**
     * 容器
     * @var []
     */
    private static $instance;

    /**
     * 防止被new
     * Singleton constructor.
     * @param array $params
     */
    protected function __construct(array $params)
    {

    }

    /**
     * 单态实例化
     * @param null  $name
     * @param array $constructParams
     * @return array|SingletonTrait
     */
    public static function instance($name = null, array $constructParams = [])
    {
        $class = get_called_class();
        if (!isset(self::$instance[$class]) || ($name && !isset(self::$instance[$class][$name]))) {
            $obj = new static($constructParams);
            if ($name) {
                self::$instance[$class][$name] = $obj;
            } else {
                self::$instance[$class] = $obj;
            }
        }
        if ($name) {
            return self::$instance[$class][$name];
        }
        return self::$instance[$class];
    }
}

class t1 extends singleton
{
    public function say()
    {
        echo 't1';
        echo PHP_EOL;
    }
}

class t2 extends singleton
{
    private $params;

    protected function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function say()
    {
        echo json_encode($this->params);
        echo PHP_EOL;

    }
}

t1::instance()->say();
t2::instance('t21', ['name' => 't21'])->say();
t2::instance('t22', ['name' => 't22'])->say();
t1::instance()->say();
t2::instance('t22')->say();
