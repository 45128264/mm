<?php

namespace Qyk\Mm;

use Closure;

/**
 * 路由注册
 * Class RouterFactory
 * @package Qyk\Mm
 *
 * @method static Router post(string $uri, string | array $action = null)
 * @method static Router get(string $uri, string | array $action = null)
 * @method static Router head(string $uri, string | array $action = null)
 * @method static Router put(string $uri, string | array $action = null)
 * @method static Router patch(string $uri, string | array $action = null)
 * @method static Router delete(string $uri, string | array $action = null)
 */
class RouterRegister
{
    /**
     * The route group attribute stack.
     *
     * @var array[
     *  'namespace'=>'', // 对应的命名空间
     *  'middleware'=>'', // 中间件
     *  'prefix'=>'',  // 路由前缀
     * ]
     */
    protected static $groupStack = [];

    /**
     * 组合
     * @param array   $attributes
     * @param Closure $callback
     */
    public static function group(array $attributes, Closure $callback)
    {
        self::pushGroupStack($attributes);
        $callback();
        self::popGroupStack();

    }

    /**
     * 参数参数入栈
     * @param array $attributes
     */
    protected static function pushGroupStack(array $attributes)
    {
        if (!empty(self::$groupStack)) {
            $attributes = self::mergeGroup($attributes, end(self::$groupStack));
        }
        self::$groupStack[] = $attributes;
    }

    /**
     * 组合参数出栈
     */
    protected static function popGroupStack()
    {

    }

    /**
     * Get the prefix from the last group on the stack.
     *
     * @return string
     */
    protected static function getLastGroupPrefix()
    {
        if (!empty(self::$groupStack)) {
            $last = end(self::$groupStack);
            return $last['prefix'] ?? '';
        }

        return '';
    }

    /**
     * Merge the given group attributes.
     *
     * @param  array $new
     * @param  array $old
     * @return array
     */
    protected static function mergeGroup($new, $old)
    {
        $new['namespace'] = static::formateGroupColumn($new, $old, 'namespace', '\\');
        $new['prefix']    = static::formateGroupColumn($new, $old, 'prefix', '/');

        return array_merge_recursive($old, $new);
    }

    /**
     * 拼接命名空间
     * @param        $new
     * @param        $old
     * @param string $column
     * @param string $trimStr
     * @return null|string
     */
    protected static function formateGroupColumn($new, &$old, string $column, string $trimStr)
    {
        $str = null;
        if (isset($old[$column])) {
            $str = $old[$column];
            unset($old[$column]);
        }
        if (isset($new[$column])) {
            $str = trim($str, $trimStr) . $trimStr . trim($new[$column], $trimStr);
        }
        return $str;
    }

    /**
     * 不限请求类型
     * @param      $uri
     * @param null $action
     * @return Router
     */
    public static function any($uri, $action = null)
    {
        $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'];
        return self::addRoute($verbs, $uri, $action);
    }

    /**
     * 向路由容器添加路由
     * Add a route to the underlying route collection.
     *
     * @param  array|string              $methods
     * @param  string                    $uri
     * @param  Closure|array|string|null $action
     * @return Router
     */
    protected static function addRoute($methods, $uri, $action)
    {
        $router = new Router($methods, $uri, $action);
        if (!empty(self::$groupStack)) {
            $router->bindParameters(end(self::$groupStack));
        }
        $container = RouterContainer::instance();
        $container->add($router);
        $router->setContainer($container);

        return $router;
    }


    /**
     * 动态调用
     * @param $method
     * @param $parameters
     * @return Router
     */
    public static function __callStatic($method, $parameters)
    {
        $verbs  = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        $method = strtoupper($method);
        if (!in_array($method, $verbs)) {
            echo 'missing method =>' . $method;
            exit;
        }
        return self::addRoute($method, ...$parameters);
    }

}