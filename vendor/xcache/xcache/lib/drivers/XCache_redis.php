<?php
require_once __DIR__.'/XCache_interface.php';

/**
 * XCache REDIS Caching Class
 *
 * @package	XCache
 * @subpackage	Libraries
 * @category	Redis
 * @author	XPerez
 * @link
 */
class XCache_redis extends XCache implements XCache_interface
{

    private $_instance;
    protected $compress = true;

    public function __construct()
    {
        $this->compress = $this->getCacheConfigItem('compress','redis','cache_hosts');
    }

    /**
     * Get
     *
     * 
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

        self::logMessage('debug', "Reading redis $type - $name - $ID.");

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

        try {
            if ($this->compress == TRUE)
                $output = @unserialize(gzinflate($cache));
            else
                $output = @unserialize($cache);
        } catch (Exception $e) {
            return false;
        }

        if (function_exists('profiler_log'))
            profiler_log('CACHE', 'Redis Read OK: ' . $type . '/' . $name . '/' . $ID);

        if ($output && $onlyCheck)
            return TRUE;

        return ($output);
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

        if (function_exists('profiler_log')) profiler_log('CACHE','Redis Write init : '.$type.'/'.$name.'/'.$ID);

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

        if ($this->compress == TRUE)
            $this->getInstance()->set($type . '-' . $name . '-' . $ID, gzdeflate(serialize($output)), $item_expiration);
        else
            $this->getInstance()->set($type . '-' . $name . '-' . $ID, serialize($output), $item_expiration);
        
        if (function_exists('profiler_log'))
            profiler_log('CACHE', 'Redis Write OK: ' . $type . '/' . $name . '/' . $ID);

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

        return $this->getInstance()->delete($type . '-' . $name . '-' . $ID);
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the cache
     *
     * @return 	boolean		TRUE, simulating success
     */
    public function cleanCache()
    {
        $this->getInstance()->flushAll();
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
        if (!extension_loaded('redis')) {
            self::logMessage('error', 'The REDIS PHP extension must be loaded to use Redis Cache.','exception','DRIVER');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * getInstance
     * Reuse Redis class
     */
    public function getInstance()
    {
        $cache_path = explode(':', $this->getCacheConfigItem('host','redis','cache_hosts'));

        $host = $cache_path[0];
        $port = $cache_path[1];

        // Check exists current instance
        if (!isset($this->_instance))
            $this->_instance = NULL;

        // Create new instance or return current
        if ($this->_instance == NULL) {
            $this->_instance = new Redis;
            $this->_instance->connect($host, $port);
        }

        return $this->_instance;
    }
    
    public function setOptions($options)
    {
        foreach ($options as $key => $val) {
            switch ($key)
            {
                case "OPT_PREFIX":
                    $this->getInstance()->setOption(Redis::OPT_PREFIX, $val.':');
                break;
                case "OPT_SERIALIZER_NONE":
                    $this->getInstance()->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
                break;
                case "OPT_SERIALIZER_PHP":
                    $this->getInstance()->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
                break;
                case "OPT_SERIALIZER_IGBINARY":
                    $this->getInstance()->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);
                break;
                case "OPT_SCAN_RETRY":
                    $this->getInstance()->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
                break;
                case "OPT_SCAN_NORETRY":
                    $this->getInstance()->setOption(Redis::OPT_SCAN, Redis::SCAN_NORETRY);
                break;
            }
        }
        return true;
    }
}

// End Class

/* End of file Cache_redis.php */
/* Location: ./system/libraries/Cache/drivers/Cache_dummy.php */