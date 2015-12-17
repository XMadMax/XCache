<?php
require_once __DIR__.'/XCache_interface.php';

/**
 * XCache MEMCACHED Caching Class
 *
 * @package	XCache
 * @subpackage	Libraries
 * @category	Memcached
 * @author	XPerez
 * @link
 */
class XCache_memcached extends XCache implements XCache_interface
{

    private $_instance;

    public function __construct()
    {
        
    }

    /**
     * Get
     *
     * Since this is the dummy class, it's always going to return FALSE.
     *
     * @param 	string
     * @return 	Boolean		FALSE
     */
    public function readCache($type, $name, $ID, $onlyCheck = FALSE)
    {
        $this->getInstance();
        $originalID = $ID;

        if (isset($_POST) && count($_POST) > 0)
            $ID = $ID . md5(serialize($_POST));

        self::logMessage('debug', "Reading memcache $type - $name - $ID.");

        $item_expiration = $this->getCacheItemExpiration($type, $name, $originalID);

        if (is_array($item_expiration)) {
            $item_properties = $item_expiration;
            $name .= '-' . $item_properties[0];
            $item_expiration = $item_properties[1];
        }

        if ($item_expiration == FALSE) {
            $item_expiration = $this->getCacheConfigItem('default', $type);
            if ($item_expiration == FALSE)
                return FALSE;
        }

        $cache = $this->getInstance()->get($type . '-' . $name . '-' . $ID);

        if ($cache == FALSE)
            return FALSE;

        if (function_exists('profiler_log'))
            profiler_log('CACHE', 'Memcache Read OK: ' . $type . '/' . $name . '/' . $ID);

        if ($cache && $onlyCheck)
            return TRUE;

        return unserialize($cache);
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Save
     *
     * @param 	string		Unique Key
     * @param 	mixed		Data to store
     * @param 	int			Length of time (in seconds) to cache the data
     *
     * @return 	boolean		TRUE, Simulating success
     */
    public function writeCache($type, $name, $ID, $output, $depID = "")
    {
        $originalID = $ID;

        if (isset($_POST) && count($_POST) > 0)
            $ID = $ID . md5(serialize($_POST));
        //if (function_exists('profiler_log')) profiler_log('CACHE','Memcache Write init : '.$type.'/'.$name.'/'.$ID);

        $item_expiration = $this->getCacheItemExpiration($type, $name, $originalID);

        if (is_array($item_expiration)) {
            $item_properties = $item_expiration;
            $name .= '-' . $item_properties[0];
            $item_expiration = $item_properties[1];
        }

        if ($item_expiration == FALSE) {
            $item_expiration = $this->getCacheConfigItem('default', $type);
            if ($item_expiration == FALSE)
                return FALSE;
        }

        $this->getInstance()->set($type . '-' . $name . '-' . $ID, serialize($output), MEMCACHE_COMPRESSED, $item_expiration);

        if (function_exists('profiler_log'))
            profiler_log('CACHE', 'Memcache Write OK: ' . $type . '/' . $name . '/' . $ID);

        return TRUE;
    }

    // ------------------------------------------------------------------------

    /**
     * Delete from Cache
     *
     * @param 	mixed		unique identifier of the item in the cache
     * @param 	boolean		TRUE, simulating success
     */
    public function deleteCache($type, $name = '', $ID = '')
    {
        $originalID = $ID;

        if (isset($_POST) && count($_POST) > 0)
            $ID = $ID . md5(serialize($_POST));

        $this->getInstance()->delete($type . '-' . $name . '-' . $ID);
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the cache
     *
     * @return 	boolean		TRUE, simulating success
     */
    public function cleanCache()
    {
        $this->getInstance()->flush();
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Info
     *
     * @param 	string		user/filehits
     * @return 	boolean		FALSE
     */
    public function getCacheInfo($type = NULL)
    {
        return FALSE;
    }

    // ------------------------------------------------------------------------

    /**
     * Get Cache Metadata
     *
     * @param 	mixed		key to get cache metadata on
     * @return 	boolean		FALSE
     */
    public function getCacheMetadata($id)
    {
        return FALSE;
    }

    // ------------------------------------------------------------------------

    /**
     * Is this caching driver supported on the system?
     * Of course this one is.
     *
     * @return TRUE;
     */
    public function isSupported($driver)
    {
        if (!extension_loaded('memcached')) {
            self::logMessage('error', 'The MEMCACHED PHP extension must be loaded to use Memcached Cache.','exception','DRIVER');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * getInstance
     * Reuse memcache class
     */
    public function getInstance()
    {
        // Check exists current instance
        if (!isset($this->_instance))
            $this->_instance = NULL;

        // Create new instance or return current
        if ($this->_instance == NULL) {
            $cache_hosts = explode(',', $this->getCacheConfigItem('host','memcached','cache_hosts'));
            $this->_instance = new Memcached;
            foreach ($cache_hosts as $server){
                $path = explode(':',$server);
                $host = $path[0];
                $port = $path[1];
                $this->_instance->addServer($host, $port);
            }
        }

        return $this->_instance;
    }
    
    public function setOptions($options)
    {
        foreach ($options as $key => $val) {
            switch ($key)
            {
                case "OPT_PREFIX_KEY":
                    $this->getInstance()->setOption(Memcached::OPT_PREFIX_KEY, $val.':');
                break;
                case "OPT_SERIALIZER_PHP":
                    $this->getInstance()->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_PHP);
                break;
                case "OPT_SERIALIZER_IGBINARY":
                    $this->getInstance()->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
                break;
                case "OPT_DISTRIBUTION_CONSISTENT":
                    $this->getInstance()->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
                break;
                case "OPT_SCAN_NORETRY":
                    $this->getInstance()->setOption(Memcached::OPT_SCAN, Memcached::SCAN_NORETRY);
                break;
            }
        }
        return true;
    }
}

// End Class

/* End of file Cache_memcache.php */
/* Location: ./system/libraries/Cache/drivers/Cache_dummy.php */