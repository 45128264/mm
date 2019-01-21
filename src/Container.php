<?php

namespace Qyk\Mm;

use Closure;
use ReflectionClass;

class Container
{
    /**
     * 服务
     * @var array
     */
    protected $provider = [];
    /**
     * 注入的任务
     * @var array
     */
    protected $bindings = [];

    /**
     * 绑定服务
     * @param                      $abstract
     * @param Closure|string|null  $concrete
     */
    public function bind(string $abstract, $concrete = null)
    {
        $abstract                  = $this->normalize($abstract);
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * 提取实例服务
     * @param       $abstract
     * @return mixed
     * @throws \ReflectionException
     */
    public function make($abstract)
    {
        if (!isset($this->provider[$abstract])) {
            $concrete = $this->bindings[$abstract];
            if (is_string($concrete)) {
                $reflector = new ReflectionClass($concrete);
                if (!$isCanCallAble = $reflector->isInstantiable()) {

                    $msg = 'abstract is could not callable=>' . $abstract;
                } else {
                    $concrete = $reflector->newInstance();
                }
            } elseif ($isCanCallAble = $concrete instanceof Closure) {
                $msg = 'abstract is could not callable=>' . $abstract;
            }
            if (!$isCanCallAble) {
                echo $msg;
                exit;
            }
            $this->provider[$abstract] = $concrete;
        }
        return $this->provider[$abstract];
    }

    /**
     * Normalize the given class name by removing leading slashes.
     *
     * @param  mixed $service
     * @return mixed
     */
    protected
    function normalize($service)
    {
        return ltrim($service, '\\');
    }
}