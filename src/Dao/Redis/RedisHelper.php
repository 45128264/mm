<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/10
 * Time: 17:37
 */

namespace Qyk\Mm\Dao\Redis;


use Exception;
use Qyk\Mm\Stage;
use Qyk\Mm\Traits\ConnectServiceTrait;
use Qyk\Mm\Traits\SingletonTrait;
use Redis;

/**
 * redis服务
 * Class RedisHelper
 * @package Qyk\Mm\Dao\Redis
 * @method  bool set($key, $val, array $params = ['ex' => 0]);
 */
class RedisHelper
{
    use SingletonTrait, ConnectServiceTrait;
    /**
     * redis对应得库
     * @var string
     */
    protected $db = 'default';
    /**
     * @var Redis
     */
    private $dbLink;


    /**
     * 魔法函数
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getRedisClient(), $name], $arguments);
    }

    /**
     * 建立连接
     * @return Redis
     * @throws Exception
     */
    protected function getRedisClient()
    {
        if ($this->dbLink) {
            return $this->dbLink;
        }
        $conf = Stage::app()->config->get('app.redis.' . $this->db);
        if (!isset($conf['host'])) {
            throw new Exception('missing redis conf');
        }
        $this->dbLink = new Redis();
        $rt           = $this->dbLink->connect($conf['host'], $conf['port']);
        if (!$rt) {
            throw new Exception('cant connect');
        }
        if (isset($conf['auth'])) {
            $this->dbLink->auth($conf['auth']);
        }
        if (isset($conf['db_index'])) {
            $this->dbLink->select($conf['db_index']);
        }
        return $this->dbLink;
    }

    /**
     * 关闭链接
     */
    public function close()
    {
        if ($this->dbLink) {
            $this->dbLink->close();
        }
    }
}