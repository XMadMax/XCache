# XCache
XCache allows to cache everything, html, json, views, objects, results from a Class->method, also can put a Cache-Control headers to be used for CDN's or proxies.

XCache comes with this drivers:

  - Files
  - Memcache
  - Memcached
  - APC
  - XCache
  - Redis
  
## Install

### Composer
Edit your base composer.json, and add: 
```php
    "require": {
        "xcache/xcache": "dev-master"
    },
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "xcache/xcache",
                "version": "dev-master",
                "dist": {
                    "url": "https://github.com/XMadMax/XCache.git",
                    "type": "git"
                }
            },
            "autoload": {
                "classmap": [""],
                "psr-4": {
                    "XCache\\XCache\\": ""
                },
                "psr-0": {
                    "XCache\\XCache\\": ""
                }
            }        
        }
    ]    
```
Update composer:
```sh
$ composer update
```

### Include manually
Copy all the files to a local dir (in this example, externalLibs/XCache).

Include the XCacheDriver.php in your main php file.
```php
require_once __DIR__.'/externalLibs/XCache/XCacheDriver.php';
```

