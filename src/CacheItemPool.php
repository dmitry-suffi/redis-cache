<?php

namespace suffi\RedisCache;


use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class RedisCache
 * @package suffi\RedisCache
 */
class CacheItemPool implements CacheItemPoolInterface
{
    /**
     * @var \Redis
     */
    protected $redis = null;

    /**
     * Key
     * @var string
     */
    protected static $cacheKey = 'cache';

    /**
     * @inheritdoc
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
        $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
    }

    /**
     * @inheritdoc
     */
    public function getItem($key)
    {
        return $this->redis->hGet(self::$cacheKey, $key);
    }

    /**
     * @inheritdoc
     */
    public function getItems(array $keys = array())
    {
        return $this->redis->hMGet(self::$cacheKey, $keys);
    }

    /**
     * @inheritdoc
     */
    public function hasItem($key)
    {
        return $this->redis->hExists(self::$cacheKey, $key);
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {

        $script = <<<LUA
        local hkeys = redis.call("HKEYS", KEYS[1])
        
        for k,v in ipairs(hkeys) do
            redis.call("HDEL", KEYS[1], v)
        end
        
        if redis.call("HLEN", KEYS[1]) > 0 then
            return 0
        else 
            return 1
        end
LUA;

        return $this->redis->eval($script, array(self::$cacheKey), 1);

    }

    /**
     * @inheritdoc
     */
    public function deleteItem($key)
    {
        return $this->redis->hDel(self::$cacheKey, $key);
    }

    /**
     * @inheritdoc
     */
    public function deleteItems(array $keys)
    {
        $script = <<<LUA
        local sum = 0
        
        for k,v in ipairs(KEYS) do
            if k > 1 then 
                if redis.call("HDEL", KEYS[1], v) then
                    sum = sum + 1
                end
            end
        end
        
        return sum
LUA;

        array_unshift($keys, self::$cacheKey);
        return $this->redis->eval($script, $keys, count($keys));
    }

    /**
     * @inheritdoc
     */
    public function save(CacheItemInterface $item)
    {
        return $this->redis->hSet(self::$cacheKey, $item->getKey(), $item);
    }

    /**
     * @inheritdoc
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        return $this->redis->hSet(self::$cacheKey, $item->getKey(), $item);
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {

    }
}
