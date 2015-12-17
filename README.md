CODE IS STILL IN DEVELOPMENT, NO USABLE FILES HERE

# XCache
XCache allows to cache everything, html, json, views, objects, results from a Class->method, also can put a Cache-Control headers to be used for CDN's or proxies.

XCache comes with this drivers:

  - Files
  - Memcache
  - Memcached
  - apc
  - xcache
  - Redis
  
## Install

### Composer
Edit your base composer.json, and add: 
```php
{
  "autoload": {
        "psr-0": {
            "XCache": "vendor/xcache/xcache/lib/",
            "XCacheDriver": "vendor/xcache/xcache/XCacheDriver.php"
        }
  }
}
```

Update composer:
```sh
$ composer update
```

Now, you can include XCache with composer autoload in any of your php files:
```php

// First of all, define the xcacheconf configuration location
define("XCACHE_CONFPATH",__DIR__)
// Include composer autoload
include_once "../../vendor/autoload.php"

```

### How it works ?
XCache can cache :
  - Any single value (a key/value pair)
  - Automatic cache for any method (can call any method in the class, uses a trait)
  - Cache a whole PHP code (en entire index.php, bootstrap, etc) by URL. Each URI can have it's own cache TTL 
  - Cache only those values that are defined
  - Cache the TTL defined for each value
  - 

### Include manually
Copy all the files to a local dir (in this example, externalLibs/XCache).

Include the XCacheDriver.php in your main php file.
```php
require_once __DIR__.'/externalLibs/XCache/XCacheDriver.php';
```
## Use
### Cache output of a block of code
You can cache all the output of a block of code, the cacheID can be the REQUEST_URI by default, or you can assing your own cacheID:

Previous example code :
```php
$app = new Bootstrap();
$app->init();
```

Modified code:
```php
require_once __DIR__.'/../libs/XCache/libs/XCache.php';
$
$app = new Bootstrap();
$app->init();
