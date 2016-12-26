Running tests
=============

Create file ```config.php``` and write
```php
<?php

define('RedisHost', 'tcp://127.0.0.1:6379');

```

Run

```
phpunit --colors=always --bootstrap=tests/autoload.php tests/
```

