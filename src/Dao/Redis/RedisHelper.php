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
 *
 * @method bool set(string $key, $val, array $params = []) $params => ['xx', 'nx', 'px' => millSeconds, 'ex' => seconds]
 * @method bool setEx(string $key, int $ttl, $val)
 * @method int setRange(string $key, int $offset, $val) 这个命令的作用是覆盖key对应的string的一部分，从指定的offset处开始，覆盖value的长度。如果offset比当前key对应string还要长，那这个string后面就补0以达到offset
 * @method int mSet(array $array) 原子性，批量set <p>$array => ['key' => 'val', ...]</p>
 * @method int mSetNx(array $array) 原子性，批量setNx <p>$array => ['key' => 'val', ...]</p>
 * @method int incrBy(string $key, int $increment) 将key对应的数字加decrement
 * @method float incrByFloat(string $key, float $increment)
 * @method int incr(string $key) 对存储在指定key的数值执行原子的加1操作
 * @method int decrBy(string $key, int $increment) 将key对应的数字减decrement
 * @method int decr(string $key) 对存储在指定key的数值执行原子的减1操作
 * @method string append(string $key, string $value) 追加到原来值（value）的结尾。 如果 key 不存在，那么它将首先创建一个空字符串的key，再执行追加操作
 * @method string getRange(string $key, int $start, int $end) Return a substring of a larger string
 * @method int strlen(string $key) 返回key的string类型value的长度。如果key对应的非string类型，就返回错误
 * @method bool exists(string $key) Verify if the specified key exists
 * @method int del(string... $key) Remove specified keys.
 *
 *
 * @method int|bool hDel(string $key, string ...$hashKey) 从 key 指定的哈希集中移除指定的域。在哈希集中不存在的域将被忽略。
 * @method bool hExists(string $key, string $hashKey) Verify if the specified member exists in a key
 * @method string hGet(string $key, string $hashKey) Gets a value from the hash stored at key.
 * @method array hGetAll(string $key) Returns the values in a hash
 * @method int hIncrBy(string $key, string $hashKey, int $value) Increments the value of a member from a hash by a given amount
 * @method float hIncrByFloat(string $key, string $hashKey, float $value) Increment the float value of a hash field by the given amount
 * @method array hKeys(string $key) Returns the keys in a hash, as an array of strings.
 * @method int hLen(string $key) the number of items in a hash, FALSE if the key
 * @method array hMGet(string $key, array $hashKeys) Retirieve the values associated to the specified fields in the hash.
 * @method bool hMSet(string $key, array $hashKeys) $hashKeys key → value array <p>hMSet('user:1', array('name' => 'Joe', 'salary' => 2000))</p>
 * @method bool hSet(string $key, string $hashKey, string $value) Adds a value to the hash stored at key
 * @method bool hSetNx(string $key, string $hashKey, string $value) Adds a value to the hash stored at key only if this field isn't already in the hash.
 * @method array hVals(string $key) Returns the values in a hash, as an array of strings.
 *
 *
 * @method string lIndex(string $key, int $index) 返回列表里的元素的索引 index 存储在 key 里面。 下标是从0开始索引的，所以 0 是表示第一个元素
 * @method int lInsert(string $key, int $position, string $pivot, string $value) 把 value 插入存于 key 的列表中在基准值 pivot 的前面或后面。 当 key 不存在时，这个list会被看作是空list，任何操作都不会发生。 当 key 存在，但保存的不是一个list的时候，会返回error<p>$position Redis::BEFORE | Redis::AFTER</p>
 * @method int lLen(string $key) 返回存储在 key 里的list的长度。 如果 key 不存在，那么就被看作是空list，并且返回长度为 0。 当存储在 key 里的值不是一个list的话，会返回error
 * @method string lPop(string $key) 移除并且返回 key 对应的 list 的第一个元素
 * @method int|bool lPush(string $key, string ...$value) Adds the string values to the head (left) of the list, 将所有指定的值插入到存于 key 的列表的头部。如果 key 不存在，那么在进行 push 操作前会创建一个空列表。 如果 key 对应的值不是一个 list 的话，那么会返回一个错误。
 * @method int|bool lPushx(string $key, string ...$value) Adds the string values to the head (left) of the list, 只有当 key 已经存在并且存着一个 list 的时候，在这个 key 下面的 list 的头部插入 value。 与 LPUSH 相反，当 key 不存在的时候不会进行任何操作
 * @method array lRange(string $key, int $start, int $end) 返回存储在 key 的列表里指定范围内的元素。 start 和 end 偏移量都是基于0的下标，即list的第一个元素下标是0（list的表头），第二个元素下标是1，以此类推。 偏移量也可以是负数，表示偏移量是从list尾部开始计数。 例如， -1 表示列表的最后一个元素，-2 是倒数第二个，以此类推
 * @method int lRem(string $key, string $value, int $count) 从存于 key 的列表里移除前 count 次出现的值为 value 的元素。 <p>这个 count 参数通过下面几种方式影响这个操作：</p><p> count > 0: 从头往尾移除值为 value 的元素。</p><p> count lt 0: 从尾往头移除值为 value 的元素。</p><p> count = 0: 移除所有值为 value 的元素。</p></p><p> 比如， LREM list -2 “hello” 会从存于 list 的列表里移除最后两个出现的 “hello”。 需要注意的是，如果list里没有存在key就会被当作空list处理，所以当 key 不存在的时候，这个命令会返回 0</p>
 * @method bool lSet(string $key, int $index, string $value) 设置 index 位置的list元素的值为 value, 当index超出范围时会返回一个error。
 * @method bool lTrim(string $key, int $start, int $stop) 修剪(trim)一个已存在的 list，这样 list 就会只包含指定范围的指定元素。start 和 stop 都是由0开始计数的， 这里的 0 是列表里的第一个元素（表头），1 是第二个元素，以此类推。果 start 超过列表尾部，或者 start > end，结果会是列表变成空表（即该 key 会被移除）
 * @method bool rPop(string $key) 移除并返回存于 key 的 list 的最后一个元素。
 * @method string rPopLPush(string $srcKey, string $dstKey) 原子性地返回并移除存储在 source 的列表的最后一个元素（列表尾部元素）， 并把该元素放入存储在 destination 的列表的第一个元素位置（列表头部）, 返回被移除和放入的元素
 * @method int|bool rPush(string $key, string ...$value) 向存于 key 的列表的尾部插入所有指定的值。如果 key 不存在，那么会创建一个空的列表然后再进行 push 操作。 当 key 保存的不是一个列表，那么会返回一个错误。
 * @method int rPushX(string $key, string ...$value) 将值 value 插入到列表 key 的表尾, 当且仅当 key 存在并且是一个列表。 和 RPUSH 命令相反, 当 key 不存在时，RPUSHX 命令什么也不做。
 *
 *
 * @method int sAdd(string $key, string ...$value) 添加一个或多个指定的member元素到集合的 key中.指定的一个或者多个元素member 如果已经在集合key中存在则忽略.如果集合key 不存在，则新建集合key, 并添加member元素到集合key中. 如果key 的类型不是集合则返回错误.<p>返回新成功添加到集合里元素的数量，不包括已经存在于集合中的元素.</p>
 * @method int sCard(string $key) 返回集合存储的key的基数 (集合元素的数量)，如果key不存在, 则返回 0.
 * @method array sDiff(string $key1, string ...$keyN) 返回一个集合与给定集合的差集的元素.
 * @method int sDiffStore(string $dstKey, string $key1, string ...$keyN) 该命令类似于 SDIFF, 不同之处在于该命令不返回结果集，而是将结果存放在destination集合中. 如果destination已经存在, 则将其覆盖重写, 结果集元素的个数
 * @method array sInter(string $key1, string ...$keyN) 返回指定所有的集合的成员的交集.
 * @method int sInterStore(string $dstKey, string $key1, string ...$keyN) 这个命令与SINTER命令类似, 但是它并不是直接返回结果集, 而是将结果保存在 destination集合中. 如果destination 集合存在, 则会被重写，返回结果集中成员的个数.
 * @method bool sisMember(string $key, string $value) 返回成员 member 是否是存储的集合 key的成员.
 * @method array sMembers(string $key) 返回key集合所有的元素, 该命令的作用与使用一个参数的SINTER 命令作用相同.
 * @method bool sMove(string $srcKey, string $dstKey, string $member) 将member从source集合移动到destination集合中. 对于其他的客户端, 在特定的时间元素将会作为source或者destination集合的成员出现. 如果source 集合不存在或者不包含指定的元素, 这smove命令不执行任何操作并且返回0.否则对象将会从source集合中移除，并添加到destination集合中去，如果destination集合已经存在该元素，则smove命令仅将该元素充source集合中移除. 如果source 和destination不是集合类型, 则返回错误.
 * @method string sPop(string $key, int $count = 1) 从存储在key的集合中移除并返回一个或多个随机元素。返回 被删除的元素，或者当key不存在时返回nil。
 * @method string|array sRandMember(string $key, int $count = 1)仅提供key参数，那么随机返回key集合中的一个元素. Redis 2.6开始，可以接受 count 参数，如果count是整数且小于元素的个数，返回含有 count 个不同的元素的数组，如果count是个整数且大于集合中元素的个数时，仅返回整个集合的所有元素，当count是负数，则会返回一个包含count的绝对值的个数元素的数组，如果count的绝对值大于元素的个数，则返回的结果集里会出现一个元素出现多次的情况. 仅提供key参数时，该命令作用类似于SPOP命令，不同的是SPOP命令会将被选择的随机元素从集合中移除，而SRANDMEMBER仅仅是返回该随记元素，而不做任何操作.
 * @method int sRem(string $key, string... $member) 在key集合中移除指定的元素. 如果指定的元素不是key集合中的元素则忽略 如果key集合不存在则被视为一个空的集合，该命令返回0. 如果key的类型不是一个集合, 则返回错误.
 * @method array sUnion(string $key, string... $keyN)  返回给定的多个集合的并集中的所有成员.
 * @method int sUnionStore(string $dstKey, string $key, string... $keyN)  该命令作用类似于SUNION命令, 不同的是它并不返回结果集, 而是将结果存储在destination集合中.
 *
 *
 * @method int zAdd(string $key, float $score1, string $value1, float $score2 = null, string $value2 = null, float $scoreN = null, string $valueN = null) 将所有指定成员添加到键为key有序集合（sorted set）里面。 添加时可以指定多个分数/成员（score/member）对。 如果指定添加的成员已经是有序集合里面的成员，则会更新改成员的分数（scrore）并更新到正确的排序位置。 如果key不存在，将会创建一个新的有序集合（sorted set）并将分数/成员（score/member）对添加到有序集合，就像原来存在一个空的有序集合一样。如果key存在，但是类型不是有序集合，将会返回一个错误应答。 分数值是一个双精度的浮点型数字字符串。+inf和-inf都是有效值
 * @method int zCard(string $key) Returns the cardinality of an ordered set.
 * @method int zCount(string $key, float $min, float $max) 返回有序集key中，score值在min和max之间(默认包括score值等于min或max)的成员个数
 * @method float zIncrBy(string $key, float $value, string $member)  为有序集key的成员member的score值加上增量increment。如果key中不存在member，就在key中添加一个member，score是increment（就好像它之前的score是0.0）。如果key不存在，就创建一个只含有指定member成员的有序集合。 当key不是有序集类型时，返回一个错误。return the new value
 * @method array zRange(string $key, int $start, int $end, bool $isWithScores = false) 返回存储在有序集合key中的指定范围的元素。 返回的元素可以认为是按得分从最低到最高排列。 如果得分相同，将按字典排序
 * @method array zRangeByLex(string $key, string $min, string $max, int $offset = null, int $limit = null) 返回指定成员区间内的成员，按成员字典正序排序, 分数必须相同。 在某些业务场景中, 需要对一个字符串数组按名称的字典顺序进行排序时, 可以使用Redis中SortSet这种数据结构来处理。
 * @method array zRangeByScore(string $key, string $min, string $max, array $options = []) 如果M是常量（比如，用limit总是请求前10个元素），你可以认为是O(log(N))。 返回key的有序集合中的分数在min和max之间的所有元素（包括分数等于max或者min的元素）。元素被认为是从低分到高分排序的。 具有相同分数的元素按字典序排列（这个根据redis对有序集合实现的情况而定，并不需要进一步计算）<p>$options Two options are available: - withscores => TRUE, - and limit => array($offset, $count)</p>
 * @method int zRank(string $key, string $member)  返回有序集key中成员member的排名。其中有序集成员按score值递增(从小到大)顺序排列。排名以0为底，也就是说，score值最小的成员排名为0。 使用ZREVRANK命令可以获得成员按score值递减(从大到小)排列的排名
 * @method int zRem(string $key, string ...$member) 返回的是从有序集合中删除的成员个数，不包括不存在的成员
 * @method int zRemRangeByRank(string $key, int $start, int $end) 移除有序集key中，指定排名(rank)区间内的所有成员。下标参数start和stop都以0为底，0处是分数最小的那个元素。这些索引也可是负数，表示位移从最高分处开始数。例如，-1是分数最高的元素，-2是分数第二高的，依次类推
 * @method int zRemRangeByScore(string $key, string $start, string $end) 移除有序集key中，默认所有score值介于min和max之间(包括等于min或max)的成员, <p>也可以只在区间内的数据，ZRANGEBYSCORE zset (1 5  返回所有符合条件1 < score <= 5的成员</p>
 * @method int zRevRange(string $key, int $start, int $end, bool $isWithScores = false) 返回有序集key中，指定区间内的成员。其中成员的位置按score值递减(从大到小)来排列。具有相同score值的成员按字典序的反序排列。 除了成员按score值递减的次序排列这一点外
 * @method int zRevRankByScore(string $key, string $min, string $max, array $options = []) 如果M是常量（比如，用limit总是请求前10个元素），你可以认为是O(log(N))。 返回key的有序集合中的分数在min和max之间的所有元素（包括分数等于max或者min的元素）。元素被认为是从高分到低分排序的。 具有相同分数的元素按字典序排列（这个根据redis对有序集合实现的情况而定，并不需要进一步计算）<p>$options Two options are available: - withscores => TRUE, - and limit => array($offset, $count)</p>
 * @method int zRevRank(string $key, string $member) 返回有序集key中，指定区间内的成员。其中成员的位置按score值递减(从大到小)来排列
 * @method int zScore(string $key, string $member) 返回有序集key中，成员member的score值。 如果member元素不是有序集key的成员，或key不存在，返回nil
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
     * @param bool  $isRollback 如果批量操作失败，执行回滚（非原子，只有程序执行中没有异常中断才会进行回滚）
     * @return mixed
     * @throws Exception
     */
    public function luaMSet(array $array, array $params = [], $isRollback = true)
    {
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
        return $this->getRedisClient()->eval($this->getMsetLua(), [$kvStr, $exType, $exNums, $otherParamsStr, $isRollback]);

    }

    /**
     * 计算给定的numkeys个有序集合的交集，并且把结果放到destination中。
     * 在给定要计算的key和其它参数之前，必须先给定key个数(numberkeys)。
     * 默认情况下，结果中一个元素的分数是有序集合中该元素分数之和，前提是该元素在这些有序集合中都存在。
     * 因为交集要求其成员必须是给定的每个有序集合中的成员，结果集中的每个元素的分数和输入的有序集合个数相等
     * @param string     $dstKey
     * @param array      $zSetKeys
     * @param array|null $weight
     * 使用WEIGHTS选项，你可以为每个给定的有序集指定一个乘法因子，意思就是，每个给定有序集的所有成员的score值在传递给聚合函数之前都要先乘以该因子。
     * 如果WEIGHTS没有给定，默认就是1
     *
     * @param string     $aggregateFunction Either "SUM", "MIN", or "MAX":
     * defines the behaviour to use on duplicate entries during the zInter.
     *
     * @return int The number of values in the new sorted set
     */
    public function zInterStore(string $dstKey, array $zSetKeys, array $weight = null, string $aggregateFunction = 'SUM')
    {
        return $this->zInter($dstKey, $zSetKeys, $weight, $aggregateFunction);
    }

    /**
     * 计算给定的numkeys个有序集合的并集，并且把结果放到destination中。
     * 在给定要计算的key和其它参数之前，必须先给定key个数(numberkeys)。
     * 默认情况下，结果集中某个成员的score值是所有给定集下该成员score值之和。
     * 使用WEIGHTS选项，你可以为每个给定的有序集指定一个乘法因子，意思就是，每个给定有序集的所有成员的score值在传递给聚合函数之前都要先乘以该因子。
     * 如果WEIGHTS没有给定，默认就是1。
     * 使用AGGREGATE选项，你可以指定并集的结果集的聚合方式。默认使用的参数SUM，可以将所有集合中某个成员的score值之和作为结果集中该成员的score值。
     * 如果使用参数MIN或者MAX，结果集就是所有集合中元素最小或最大的元素。
     * 如果key destination存在，就被覆盖。
     * @param string     $dstKey
     * @param array      $zSetKeys
     * @param array|null $weight
     * 使用WEIGHTS选项，你可以为每个给定的有序集指定一个乘法因子，意思就是，每个给定有序集的所有成员的score值在传递给聚合函数之前都要先乘以该因子。
     * 如果WEIGHTS没有给定，默认就是1
     *
     * @param string     $aggregateFunction Either "SUM", "MIN", or "MAX":
     * defines the behaviour to use on duplicate entries during the zInter.
     *
     * @return int The number of values in the new sorted set
     */
    public function zUnionStore(string $dstKey, array $zSetKeys, array $weight = null, string $aggregateFunction = 'SUM')
    {
        return $this->zUnion($dstKey, $zSetKeys, $weight, $aggregateFunction);
    }

    /**
     * 命令用于计算有序集合中指定成员之间的成员数量
     * @param string $key
     * @param float  $min 在有序集合中分数排名较小的成员
     * @param float  $max 在有序集合中分数排名较大的成员
     * @return int 有序集合中成员名称 min 和 max 之间的成员数量
     * @throws Exception
     */
    public function zLexCount(string $key, float $min, float $max)
    {
        $lua = 'return redis.call("zlexcount",ARGV[1],ARGV[2],ARGV[3])';
        return $this->getRedisClient()->eval($lua, [$key, $min, $max]);
    }

    /**
     * 删除并返回有序集合key中的最多count个具有最高得分的成员。
     * 指定一个大于有序集合的基数的count不会产生错误
     * @since 5.0.0
     * @param string $key
     * @param float  $count
     * @return array 当返回多个元素时候，得分最高的元素将是第一个元素，然后是分数较低的元素
     * @throws Exception
     */
    public function zPopMax(string $key, float $count = 1)
    {
        $lua = 'return redis.call("zpopmax",ARGV[1],ARGV[2])';
        return $this->getRedisClient()->eval($lua, [$key, $count]);
    }

    /**
     * 删除并返回有序集合key中的最多count个具有最低得分的成员。
     * 指定一个大于有序集合的基数的count不会产生错误
     * @since 5.0.0
     * @param string $key
     * @param float  $count
     * @return array 当返回多个元素时候，得分最高的元素将是第一个元素，然后是分数较低的元素
     * @throws Exception
     */
    public function zPopMin(string $key, float $count = 1)
    {
        $lua = 'return redis.call("zpopmin",ARGV[1],ARGV[2])';
        return $this->getRedisClient()->eval($lua, [$key, $count]);
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

    /**
     * lua 批量模仿set
     * @return string
     */
    protected function getMsetLua(): string
    {
        return <<<eof
       local info = {};       
       local successKeys ={};
       
       -- 记录keyValue              
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
       
       -- 解析keyValue
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
       
       -- 模仿redis set操作
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
       
       local rt = callRedis(info);
       
       -- 进行回滚
       if not rt and ARGV[5] == '1' then
           for k,v in pairs(successKeys) do
                redis.call('del',v);
           end
       end
      return rt;
eof;
    }
}