<?php

require_once __DIR__ . '/XCache_interface.php';

/**
 * XCache APC Caching Class
 *
 * @package	XCache
 * @subpackage	Libraries
 * @category	Apc
 * @author	XPerez
 * @link
 */
class XCache_apc extends XCache implements XCache_interface
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

        self::logMessage('cache', "Reading APC $type - $name - $ID.");

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

        $cache = apc_fetch($type . '-' . $name . '-' . $ID);

        if ($cache == FALSE)
            return FALSE;

        self::logMessage('cache', 'APC Read OK: ' . $type . '/' . $name . '/' . $ID);

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

        apc_store($type . '-' . $name . '-' . $ID, serialize($output), $item_expiration);

        self::logMessage('cache', 'APC Write OK: ' . $type . '/' . $name . '/' . $ID);

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

        return apc_delete($type . '-' . $name . '-' . $ID);
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the cache
     *
     * @return 	boolean		TRUE, simulating success
     */
    public function cleanCache()
    {
        return apc_clear_cache('user');
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
        if (!extension_loaded('apc') OR ini_get('apc.enabled') != "1") {
            self::logMessage('cache', 'The APC PHP extension must be loaded to use APC Cache.');
            return FALSE;
        }

        return TRUE;
    }

    // ------------------------------------------------------------------------

    /**
     * getInstance
     * 
     */
    public function getInstance()
    {
        return $this;
    }

}

// End Class

/* End of file Cache_memcache.php */

