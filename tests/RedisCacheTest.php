<?php

namespace suffi\RedisCache\Tests;

use \suffi\RedisCache\CacheItemPool;
use \suffi\RedisCache\CacheItem;

class RedisCacheTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CacheItemPool
     */
    protected $cache = null;

    protected function setUp()
    {
        parent::setUp();

        $this->cache = $this->getCache();
        $this->cache->clear();
    }

    protected function getItem($key, $value)
    {
        $item = new CacheItem($key);
        $item->set($value);
        return $item;
    }


    public function getCache()
    {
        $redis = new \Redis();
        if (defined('REDIS_HOST')) {
            $host = REDIS_HOST;
        } else {
            $host = '127.0.0.1:6379';
        }
        $redis->connect($host);
        $redis->select(0);
        return new CacheItemPool($redis);
    }

    public function testCache()
    {

        $this->assertFalse($this->cache->hasItem('key'));
        $this->assertFalse($this->cache->getItem('key'));
        $this->assertEquals($this->cache->getItems(['key']), ['key' => false]);

        $item = $this->getItem('key', 'value');

        $this->assertEquals($this->cache->save($item), 1);

        $this->assertTrue($this->cache->hasItem('key'));

        $itemCache = $this->cache->getItem('key');

        $this->assertEquals($item, $itemCache);

        $this->assertEquals($this->cache->getItems(['key']), ['key' => $item]);

        $this->assertEquals($this->cache->deleteItem('key'), 1);

        $this->assertFalse($this->cache->hasItem('key'));
        $this->assertFalse($this->cache->getItem('key'));
        $this->assertEquals($this->cache->getItems(['key']), ['key' => false]);
    }

    public function testCacheKeys()
    {
        $this->assertFalse($this->cache->hasItem('key1'));
        $this->assertFalse($this->cache->getItem('key1'));
        $this->assertEquals($this->cache->getItems(['key1']), ['key1' => false]);

        $items = [];
        for ($i = 0; $i < 100; $i++) {
            $items[$i] = $this->getItem('key' . $i, 'value' . $i);
        }

        foreach ($items as $k => $item) {
            $this->assertEquals($this->cache->save($item), 1);
        }

        foreach ($items as $k => $item) {
            $this->assertTrue($this->cache->hasItem('key' . $k));
        }

        $items3 = $this->cache->getItems(['key4', 'key9', 'key45']);

        $this->assertEquals(count($items3), 3);
        $this->assertEquals($items3['key4'], $items[4]);
        $this->assertEquals($items3['key9'], $items[9]);
        $this->assertEquals($items3['key45'], $items[45]);

        $this->cache->deleteItems(['key4', 'key9', 'key45']);

        $items3 = $this->cache->getItems(['key4', 'key9', 'key45']);

        $this->assertEquals(count($items3), 3);
        $this->assertFalse($items3['key4']);
        $this->assertFalse($items3['key9']);
        $this->assertFalse($items3['key45']);

        foreach ($items as $k => $item) {
            if (in_array($k, [4, 9, 45])) {
                $this->assertFalse($this->cache->hasItem('key' . $k));
            } else {
                $this->assertTrue($this->cache->hasItem('key' . $k));
            }
        }

        $this->assertEquals($this->cache->clear(), 1);

        foreach ($items as $k => $item) {
            $this->assertFalse($this->cache->hasItem('key' . $k));
        }
    }
}
