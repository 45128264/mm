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
 * @method bool set(string $key, $val, array $params = []) $params => ['xx', 'nx', 'px' => millSeconds, 'ex' => seconds]
 * @method bool setEx(string $key, int $ttl, $val)
 * @method int setRange(string $key, int $offset, $val) 这个命令的作用是覆盖key对应的string的一部分，从指定的offset处开始，覆盖value的长度。如果offset比当前key对应string还要长，那这个string后面就补0以达到offset
 * @method int mSet(array $array) 原子性，批量set <p>$array => ['key' => 'val', ...]</p>
 * @method int mSetNx(array $array) 原子性，批量setNx <p>$array => ['key' => 'val', ...]</p>
 *
 *
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

    /**
     * 原子性，lua批量执行set($key, $val, array $params)
     * @param array $array ['key' => 'val', ...]
     * @param array $params ['xx', 'nx', 'px' => millSeconds, 'ex' => seconds]
     * @return mixed
     * @throws Exception
     */
    public function luaMSet(array $array, array $params = [])
    {
        $lua    = <<<lof
       local info = {};
       
       local function insertInfo(str,index,lasIndex)
            local tmp,key,val,lastEqIndex;
            local glubEq = '=';
            tmp = string.sub(str,index,lasIndex);
            lastEqIndex = string.find(tmp,glubEq,1);
            if  lastEqIndex then
               key = string.sub(tmp,1,lastEqIndex-1);
               val = string.sub(tmp,lastEqIndex+1,-1);
               info[key]=val;  
            end   
       end  
       
       local function getKeyValues()
            local str = ARGV[1];
            local glubJoin = '|';
            local lastJoinIndex,joinIndex;
            joinIndex=1;
            
            while true do
                lastJoinIndex = string.find(str,glubJoin,joinIndex);
                if not lastJoinIndex then
                     insertInfo(str,joinIndex,-1);
                     break;
                end                  
                insertInfo(str,joinIndex,lastJoinIndex-1);
                joinIndex = lastJoinIndex+1;          
            end           
       end  
       
       local successKeys ={};
       local function callRedis(info)
           local exType=ARGV[2];
           local exNums=ARGV[3];
           local otherParams = ARGV[4];
           
           getKeyValues();                                 
           for k,v in pairs(info) do           
               if exType and tonumber(exNums) > 0 then
                   if string.len(otherParams) > 0 then
                        if not redis.call('set',k,v,otherParams,exType,exNums) then
                            return false;
                        end
                   elseif not redis.call('set',k,v,exType,exNums) then
                        return false;
                   end
               elseif string.len(otherParams) > 0 then
                   if not redis.call('set',k,v,otherParams) then
                       return false;
                   end
               else
                    if not redis.call('set',k,v) then
                      return false;
                    end
               end
               table.insert(successKeys,k);
           end
           return true;
       end 
       
       if not callRedis(info) then
           -- 进行回滚
           for k,v in pairs(successKeys) do
                redis.call('del',v);
           end
           return false;
       end
       return true;
lof;
        $exType = '';
        $exNums = 0;
        if (isset($params['ex'])) {
            $exType = 'ex';
            $exNums = $params['ex'];
            unset($params['ex']);
        }
        if (isset($params['px'])) {
            $exType = 'px';
            $exNums = $params['px'];
            unset($params['px']);
        }
        $kvStr          = $this->array2Str($array, '|', '=');
        $otherParamsStr = $this->array2Str($params, ' ', ' ');
        return $this->getRedisClient()->eval($lua, [$kvStr, $exType, $exNums, $otherParamsStr]);

    }

    /**
     * 将2维数组转成string
     * @param array  $array
     * @param string $glub
     * @param string $join
     * @param bool   $ignoreNumberKey
     * @return string
     */
    protected function array2Str(array $array, string $glub, string $join = '=', bool $ignoreNumberKey = true)
    {
        $str = [];
        foreach ($array as $key => $val) {
            if ($ignoreNumberKey && is_numeric($key)) {
                $str[] = $val;
            } else {
                $str[] = $key . $join . $val;
            }
        }
        return implode($glub, $str);
    }
}