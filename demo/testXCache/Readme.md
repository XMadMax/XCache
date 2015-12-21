## Examples
To view how works XCache, you can execute directlly from the command line inside this directory:

Check the config, the cache_hosts->file->path directory must to exists and have 'write' permissions.
By default, 'file' will be used, and all directory structure will be created.

### testDriver.php
Test how to 'auto-cache' a method inside a class.

```php
php testDriver.php
```

### testXCache.php
Use XCacheDriver to access xCacheValue and xCacheMethod within same class.

```php
php testXCache.php
```

### testPageCache.php
Use XCacheDriver to access a method that cache an external page. This is the most understable example that takes a long time getting 'file_get_contents', but once cached, get and immediate response.

```php
php testPageCache.php
```

### testAllPageCache.php
Use XCache to cache a whole process, used to cache an URL, inside a app.php, bootstrap.php or index.php of your framework.
This is preferable to be executed in a browser...   http://mycachetest.local/XCache/demo/testAllPageCache.php

```php
php testAllPageCache.php
```



