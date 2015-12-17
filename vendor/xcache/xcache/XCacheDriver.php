<?php
require_once __DIR__ . '/lib/XCache.php';

/**
 * XCache base class
 *
 * @package        	XCache
 * @subpackage    	XCacheDriver
 * @category    	Cache
 * @author        	Xavier Perez
 * @license             MIT License (MIT) : http://opensource.org/licenses/MIT
 * @version		3.0
 */
trait XCacheDriver
{
    public $xcacheClass = null;

    public function xCachePass($confPath = '')
    {
        if (is_null($this->xcacheClass)) { 

            if ($confPath == '') {
                if (defined("XCACHE_CONFPATH")) {
                    $confPath = XCACHE_CONFPATH;
                }
                else {
                    $rc = new \ReflectionClass(get_class($this));
                    $confPath = dirname($rc->getFileName());
                }
            }
            $this->xcacheClass = new XCache($confPath);
        }
        return $this;
    }

    public function xCacheMethod($type, $name, $ID, $class, $method , $methodParams = '')
    {
        if (is_null($this->xcacheClass)) {
            $this->xcachePass();
        }
        return $this->xcacheClass->cache($type, $name, $ID, $class, $method, $methodParams);
    }

    public function xCacheValue($type, $name, $ID, $value='')
    {
        if (is_null($this->xcacheClass)) {
            $this->xcachePass();
        }
        return $this->xcacheClass->cache($type, $name, $ID, $value);
    }

    /**
     * Call any method inside common module, else call $APP method
     * 
     * @param type $name
     * @param array $arguments
     * @return type
     * @throws Exception
     */
    public function __call($name, array $arguments)
    {
        if (method_exists($this, '_' . $name)) {
            $ID = get_class($this) . '_' . $name . '|' . md5(json_encode(serialize($arguments)));

            if (is_null($this->xcacheClass)) {
                $this->xcachePass();
            }

            $methodName = '_' . $name;
            if (($result = $this->xcacheClass->readCache('cache_methods', get_class($this) . $methodName, $ID)) === FALSE) {
                $result = call_user_func_array(array(&$this, $methodName), $arguments);
                $this->xcacheClass->writeCache('cache_methods', get_class($this) . $methodName, $ID, $result);
            }
            return $result;
        }
        else {
            if (get_parent_class()) {
                return parent::__call($name, $arguments);
            } else {
                throw new \Exception('No such method: ' . $name);
            }
        }
    }

}

