Redis cache
===========

[![Build Status](https://api.travis-ci.org/dmitry-suffi/redis-cache.svg?branch=master)](https://travis-ci.org/dmitry-suffi/redis-cache)
[![Coveralls](https://coveralls.io/repos/github/dmitry-suffi/redis-cache/badge.svg?branch=master)](https://coveralls.io/github/dmitry-suffi/redis-cache?branch=master)

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