# XCache
XCache allows to cache everything, html, json, views, objects, results from a Class->method, also can put a Cache-Control headers to be used for CDN's or proxies.

Three methods allowed:
  - As a trait, with inheritance of all methods and automatic method cache.
  - As a trait, using XCache methods within same class.
  - Independent, using XCache as a separate class.

XCache comes with this drivers:

  - Files
  - Memcache
  - Memcached
  - MongoDB
  - Apc
  - Redis

## New: Added XCACHE_BASEID to allow separated cache on same source code
  
## Install

### Composer
Edit your base composer.json, and add: 
```php
{
    "require": {
        "xmadmax/xcache": "3.*"
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
            "path": "/var/tmp/xcache/",
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

##### Apc
    Configure xcacheconf.json with:
```php
        "cache_driver": "apc",
```
And, in the cache_hosts group:
```php
        "apc" : {
            "options": false,
            "compress": false
        },
```    
The compress is available, takes more time to compress, but less time reading and less space on shared memory.
If php_apc is not avaliable, then file driver is used.

#### Other options
##### Cache even when $_GET is set
If you want to cache neither when is recieved GET parameters (example: http://www.myweb.com?param=111), cache_get must to set to 'true'
Even when a page is called with GET paremeters, they will be part of the uniqueID to compose de cache key, allowing to have a diferent cache if GET parameters change.

##### Cache even when $_POST is set
If you want to cache neither when is recieved POST parameters, cache_post must to set to 'true'
Even when a page is called with POST paremeters, they will be part of the uniqueID to compose de cache key, allowing to have a diferent cache if POST parameters change.

##### Cache only when a user is not logged
If you want to cache ONLY when no user is logged, your app must to write a cookie in the browser. The cookie name can be configured in 'cache_logged_cookie' option.
When XCache detect this cookie, and 'cache_only_not_logged_pages' is set to 'true', the cache is deactivated.

### XCache usage & examples
XCache can cache :
  - Automatic cache for any method in your class (can call any method in the class, uses a trait)
  - Cache a whole PHP code (en entire index.php, bootstrap, etc) by URL. Each URI can have it's own cache TTL 
  - Cache an specific method of a class
  - Cache any single value (a key/value pair)
  - Cache only those values that are defined
  - Cache the TTL defined for each value
  - Set Cache-control headers for any page, to be understood by a CDN.

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
require_once '../../vendor/autoload.php'; // Wherever is the composer autoload file
define("XCACHE_CONFPATH",'/var/www/myApp/conf'); // Wherever is the xcacheconf.json file

class myClass
{
    use XCacheDriver;  // Include this line after class definition
    public function __construct()
    {
        // This makes the magic happen
        $this->xCachePass();
    }

    public function _myMethod($params)   // Add a single '_' before the name of the method
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
$params = 'a string, an array, an object ...';
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
            "^myClass_": 300
        }
   },
```

Regexp expressions are evaluated with preg_match. You can use any combination that preg_match accepts.

#### Cache a whole PHP code
You can cache all the output of a block of code, the cacheID will be the REQUEST_URI:
If you have a php with bootstrap or any opther code launching the base core class:
```php
<?php
.
.
$app = new Bootstrap();
$app->init();
.
.
```

Modify the code as described:
```php
<?php
require_once '../../vendor/autoload.php'; // Wherever is the composer autoload file
define("XCACHE_CONFPATH",'/var/www/myApp/conf'); // Wherever is the xcacheconf.json file
$XCache = new XCache();
if ($XCache->enableCache()) {
    die();
}
.
.
$app = new Bootstrap();
$app->init();
.
.
$XCache->writeAndFlushCache();
```
The writeAndFlushCache method must to be the last line where the output of the php file ends.

To make it works, you need to configure the 'cache_pages' group in the xcacheconffile.json, to detect the REQUEST_URI.
For example, if the bootstrap file is loaded by htaccess as default, it will capture any url, as '/' or '/mypage', then this parameter must to be defined in the xcacheconffile.json:
```php
    "cache_pages": {
        "default": 30,
        "regexp": {
            "^/$": 3600,
            "^/mypage$": 600
        }
    },
```
The 'home' will be cached 1 hour, 'mypage' will be cached 5 minutes, any other page will be cached 30 seconds (default)


#### Cache an specific method of a class
XCache can work alone or as a trait of any class. To work alone, use the previous example (Cache a whole PHP code), to work as a trait, use the example (Automatic cache for any method)
This is usefull if you want to include XCache in any PHP code.

The XCacheDriver have two byPass methods to XCache:
  - xCacheMethod
  - xCacheValue

Both can be called inside any class that have the same structure as described in (Automatic cache for any method), for example:
```php
<?php
require_once '../../vendor/autoload.php'; // Wherever is the composer autoload file
define("XCACHE_CONFPATH",'/var/www/myApp/conf'); // Wherever is the xcacheconf.json file

class myClass
{
    use XCacheDriver;  // Include this line after class definition
    public function myMethod($params)
    {
        .
        .
        .
        return $something
    }
}
```
The 'myMethod' is not underscored, but can also be called with 'xcacheMethod':
```php
$myClass = new myClass();
$params = array('value1','value2');
$result = $myClass->xCacheMethod("cache_methods","myClass_myMethod",md5('myClass_myMethod'.json_encode($params)),$myClass,'myMethod',$params);
```
The result is retrived from cache if it exists, if not, the $myClass->myMethod($params) is called, stored in cache and returned.
This allow to have the call and cache retrieving in only one line.
As described before, you need to add "myClass_myMethod" to the 'cache_method' group in xcacheconf.json file:
```php
    "cache_methods": {
        "default": 0,
        "myClass_myMethod": 300
   },
```

The xCacheMethod takes 6 arguments:
  1. Cache group
  2. Cache name
  3. Unique ID, must to contain also the params values
  4. The object class
  5. The name of the method
  6. The params

#### Cache a key/value pair
As described before, "xCacheValue" is also available inside any class that inherits the XCacheDriver trait.
Using the same example described before:
```php
<?php
require_once '../../vendor/autoload.php'; // Wherever is the composer autoload file
define("XCACHE_CONFPATH",'/var/www/myApp/conf'); // Wherever is the xcacheconf.json file

class myClass
{
    use XCacheDriver;  // Include this line after class definition
    public function myMethod($params)
    {
        .
        .
        .
        return $something
    }
}
```
Use this to save a result value:
```php
$myClass = new myClass();
$result = $myClass->myMethod($params);
$myClass->xCacheValue("cache_values","myResult",md5('myResult'),$result);
```
To have it working, the "myResult" key, must to be configured in the 'cache_values' group in xcacheconf.json, if not, will take the 'default' TTL:
    "cache_values": {
        "default": 15,
        "regexp": {
            "^myResult$": 300
        }
    }

To retrieve this result in any other php or line of code:
```php
$result = $myClass->xCacheValue("cache_values","myResult",md5('myResult'));
```


#### Set Cache-control headers 
XCache can 'only' put a 'Cache-control' HTML headers to be understood by a CDN.
Using the same example as for whole page cache, in the index.php or bootsrap.php :
```php
<?php
.
.
$app = new Bootstrap();
$app->init();
.
.
```

Modify the code as described:
```php
<?php
require_once '../../vendor/autoload.php'; // Wherever is the composer autoload file
define("XCACHE_CONFPATH",'/var/www/myApp/conf'); // Wherever is the xcacheconf.json file
$XCache = new XCache();
$XCache->setCacheHeaders();
.
.
$app = new Bootstrap();
$app->init();
.
.
```


### Use of XCache without XCacheDriver

Include the composer autoload in your php file.
```php
<?php
require_once '../../vendor/autoload.php'; // Wherever is the composer autoload file
define("XCACHE_CONFPATH",'/var/www/myApp/conf'); // Wherever is the xcacheconf.json file
```
#### Cache an specific method of a class
```php
$XCache = new XCache();
$myClass = new myClass();
$params = array('value1','value2');
$result = $XCache->cache("cache_methods","myClass_myMethod",md5('myClass_myMethod'.json_encode($params)),$myClass,'myMethod',$params);
```
As described before, you need to add "myClass_myMethod" to the 'cache_method' group in xcacheconf.json file:
```php
    "cache_methods": {
        "default": 0,
        "myClass_myMethod": 300
   },
```
#### Cache a key/value pair
```php
$XCache = new XCache();
$myClass = new myClass();
$result = $myClass->myMethod($params);
$cached = $XCache->cache("cache_values","myResult",md5('myResult'),$result);
```
To have it working, the "myResult" key, must to be configured in the 'cache_values' group in xcacheconf.json, if not, will take the 'default' TTL:
    "cache_values": {
        "default": 15,
        "regexp": {
            "^myResult$": 300
        }
    }

To retrieve this result in any other php or line of code:
```php
$result = $XCache->cache("cache_values","myResult",md5('myResult'));
```

