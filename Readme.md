Redis cache
===========

Description
-----------
Simple caching radis. Complies psr-6.

It requires expansion [phpredis](https://github.com/phpredis/phpredis).


Installation
------------

```
composer require dmitry-suffi/redis-cache
```

Using
-----

```php


$redis = new Redis();
if ($redis->connect('11.111.111.11', 6379) && $redis->select(0)) {
    $cache = new CacheItemPool($redis);
}

```