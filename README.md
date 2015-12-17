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
<?php
// First of all, define the xcacheconf configuration dir location (the name of this file will be ever xcacheconf.json)
define("XCACHE_CONFPATH",__DIR__)
// Include composer autoload
include_once "../../vendor/autoload.php";

```

### Configuration
#### XCache drivers
##### File
Configure xcacheconf.json with:
```php
        "cache_driver": "file",
```
In the cache_host group, define the absolute path to the 'cache' directory to be created:
```php
        "file" : {
            "path": "/var/xcache/",
            "options": false,
            "compress": true
        },
```    
In Windows, the path is relative to the drive where the php program is located.
The compress option is available, takes more time to compress, but less time reading and less space on disk.

##### Memcache
    Configure xcacheconf.json with:
```php
        "cache_driver": "memcache",
```
And, in the cache_hosts group:
```php
        "memcache" : {
            "host": "127.0.0.1:11211",
            "options": false,
            "compress": true
        },
```    
Define host as "host:port".
The compress is available, takes more time to compress, but less time reading and less space on shared memory.
If php_memcache is not avaliable, then file driver is used.

##### Memcached
Configure xcacheconf.json with:
```php
        "cache_driver": "memcached",
```
And, in the cache_hosts group:
```php
        "memcached" : {
            "host": "127.0.0.1:11211",
            "options": false,
            "compress": true
        },
```    
Define hosts as "host:port", or "host:port,host:port,host:port" for a group of servers.
The OPT_PREFIX will compose the key of any item saved in the collection, allowing to xcache/memcached to be used with other apps at same time.
The compress option is available, takes more time to compress, but less time reading and less space on shared memory.
If php_memcached is not avaliable, then file driver is used.

##### MongoDB
Configure xcacheconf.json with:
```php
        "cache_driver": "mongodb",
```
And, in the cache_hosts group:
```php
        "mongodb" : {
            "host": "127.0.0.1:27017:::xcachedb:xcachecollection",
            "options": {
                "OPT_PREFIX" : "mytest"
            },
            "compress": false
        },
```    
Define hosts as "host:port:user:passwd:database:collection".
The OPT_PREFIX will compose the key of any item saved in the collection, allowing to xcache/mongodb to be used with other apps at same time.
The compress option is available, takes more time to compress, but less time reading and less space on ram/disk.
If php_mongodb is not avaliable, then file driver is used.

##### Redis
Configure xcacheconf.json with:
```php
        "cache_driver": "redis",
```
And, in the cache_hosts group:
```php
        "mongodb" : {
            "host": "127.0.0.1:6379",
            "options": {
                "OPT_PREFIX" : "mytest"
            },
            "compress": false
        },
```    
Define host as "host:port".
The OPT_PREFIX will compose the key of any item saved in the collection, allowing to xcache/redis to be used with other apps at same time.
The compress option is available, takes more time to compress, but less time reading and less space on ram/disk.
If php_redis is not avaliable, then file driver is used.

### How it works ?
XCache can cache :
  - Automatic cache for any method in your class (can call any method in the class, uses a trait)
  - Any single value (a key/value pair)
  - Cache a whole PHP code (en entire index.php, bootstrap, etc) by URL. Each URI can have it's own cache TTL 
  - Cache only those values that are defined
  - Cache the TTL defined for each value
  - 

#### Automatic cache for any method

If you have a class like this:
```php

<?php

class myClass extends myMasterClass
{
    public function myMethod($params)
    {
        .
        .
        .
        return $something
    }
}
```

Modifiy as described:
```php

<?php
require_once '../../vendor/autoload.php'; // Wherever is the composer autoload
define("XCACHE_CONFPATH",'/var/www/myApp/conf'); // Wherever is the xcacheconf.json file

class myClass
{
    use XCacheDriver;  //   <= Include this line after class definition
    public function _myMethod($params)   //   Add a single '_' before the name of the method
    {
        .
        .
        .
        return $something
    }
}
```

Now, you can call:
```php

$params = 'a string, an array, a class ...';

$myClass = new myClass();
$result = $myClass->myMethod($params);

```

The XCacheDriver will not find 'myMethod' in 'myClass', but will find '_myMethod'. Then will look in xcacheconf.json, in the 'cache_method' group, with the name of 'myClass_myMethod', and will retrieve the TTL for this cache.
The xcacheconf.json can look like this:
```php
    "cache_methods": {
        "default": 0,
        "myClass_myMethod": 300
    },
```
Then, before the call of $myClass->myMethod($params), XCache will look for a cache for this method&params, if found, will return the value, if not, the method will be called and the value returned will be cached.

You can configure regular expressions to cache a group of methods, for example, to cache all methods in myClass:
```php
    "cache_methods": {
        "default": 0,
         "regexp": {
            "^myClass_": 30
   },
```

Regexp expressions are evaluated with preg_match. You can use any combination that preg_match accepts.













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
