## Examples
To view how works XCache, you can execute directlly from the command line inside this directory:

```php
php testDriver.php
```

### testDriver.php
Test how 'auto-cache' a method inside a class.

```php
php testXCache.php
```

### testXCache.php
Use XCacheDriver to access xCacheValue and xCacheMethod within same class.

```php
php testPageCache.php
```

### testPageCache.php
Use XCacheDriver to access a method that cache an external page. This is the most understable example that takes a long time getting 'file_get_contents', but once cached, get and immediate response.

```php
php testAllPageCache.php
```

### testAllPageCache.php
Use XCache to cache a whole process, used to cache an URL, inside a app.php, bootstrap.php or index.php of your framework.
This is preferable to be executed in a browser...   http://mycachetest.local/XCache/demo/testAllPageCache.php


