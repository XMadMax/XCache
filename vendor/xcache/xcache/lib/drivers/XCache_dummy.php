<?php
require_once __DIR__.'/XCache_interface.php';

/**
 * XCache DUMMY Caching Class
 *
 * @package	XCache
 * @subpackage	Libraries
 * @category	dummy
 * @author	XPerez
 * @link
 */
class XCache_dummy extends XCache implements XCache_interface
{

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
        self::logMessage('CACHE', "Dummy Read OK: $type / $name / $ID.");
        return FALSE;
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
        self::logMessage('CACHE', "Dummy Write OK: $type / $name / $ID.");
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
        self::logMessage('CACHE', "Cache dummy delete: " . $ID);
        return TRUE;
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the cache
     *
     * @return 	boolean		TRUE, simulating success
     */
    public function cleanCache()
    {
        self::logMessage('CACHE', "Cache dummy clean: $type / $name / $ID");
        return TRUE;
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
        return TRUE;
    }

    public function getInstance()
    {
        return TRUE;
    }

    // ------------------------------------------------------------------------
}

// End Class

/* End of file Cache_dummy.php */
/* Location: ./system/libraries/Cache/drivers/Cache_dummy.php */