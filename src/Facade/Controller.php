<?php

namespace Qyk\Mm\Facade;

use mm\frame\Exception;
use ReflectionMethod;

/**
 * 控制器
 * Class Cache
 * @package Qyk\Mm\Facade
 */
abstract class Controller extends Facade
{
    /**
     * 获取当前服务对应名称，方便识别当前服务的类型
     * @return string
     */
    protected function getFacadeAliasName(): string
    {
        return 'controller';
    }


    /**
     * 执行功能
     */
    public function run()
    {
        if (!$this->checkAccessPemission()) {
            //没有权限
            $this->app->request->redirect('home.index');
            return false;
        }
        $this->callAction();
        return true;
    }

    /**
     * 判断用户访问权限
     */
    protected function checkAccessPemission(): bool
    {
        //判断对应的method是否被禁用
        $forbiddenAccessMethods = $this->app->config->get('router.404');
        $currentMethod          = $this->app->router->currentMethod;
        $methold                = $this->app->router->currentController . '.' . $currentMethod;
        if (in_array($methold, $forbiddenAccessMethods)) {
            //todo
            echo 'this method is 404';
            exit;
        }
        $needAccessMethods = $this->getNeedLoginAccessMethods();
        $isNeedLogin       = in_array($currentMethod, $needAccessMethods) || in_array('*', $needAccessMethods);
        if ($isNeedLogin && !$this->app->user->userId) {
            throw new Exception('please login');
        }
        return true;
    }


    /**
     * 执行controller
     */
    private function callAction()
    {
        $method = $this->app->router->currentMethod;
        if (!method_exists($this, $method)) {
            //todo exception
            echo 'can find methold=>' . $method;
            exit;
        }
        $params = $this->app->router->routerArgs;
        $args   = [];
        //如果解析出来的参数不为空
        if (!empty($params)) {
            $action = new ReflectionMethod($this, $method);
            $args   = [];
            foreach ($action->getParameters() as $param) {
                $name   = $param->getName();
                $args[] = isset($params[$name]) ? $params[$name] : ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);
            }
        }
        $this->app->response->setControllerRt(call_user_func_array([$this, $method], $args));
    }

    /**
     * 获取需要登录后才可以访问的方法
     * @return array
     */
    protected function getNeedLoginAccessMethods(): array
    {
        return [];
    }

    protected function returnJson(array $rt)
    {
        $this->app->response->isJson = true;
        return $rt;
    }
}