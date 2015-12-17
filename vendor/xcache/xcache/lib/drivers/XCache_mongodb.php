<?php
require_once __DIR__.'/XCache_interface.php';

/**
 * XCache MONGODB Caching Class
 *
 * @package	XCache
 * @subpackage	Libraries
 * @category	MongoDB
 * @author	XPerez
 * @link
 */
class XCache_mongodb extends XCache implements XCache_interface
{

    private $_instance;
    protected $compress = true;
    private $keyPrefix = 'xcache';

    public function __construct()
    {
        $this->compress = $this->getCacheConfigItem('compress','redis','cache_hosts');
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

        self::logMessage('debug', "Reading mongodb $type - $name - $ID.");

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

        $cacheResult = $this->getInstance()->findOne(array('KEY' => $this->keyPrefix, 'ID' => $type . '-' . $name . '-' . $ID));

        if ($cacheResult == FALSE)
            return FALSE;

        $expires = $cacheResult['expires'];
        $cache = $cacheResult['content'];

        // Has the file expired? If so we'll delete it.
        if (time() >= $expires) {
            self::logMessage('debug', "MongoDB Read: $type . '-' . $name . '-' . $ID  has expired");
            $this->getInstance()->remove(array('KEY' => $this->keyPrefix, 'ID' => $type . '-' . $name . '-' . $ID));
            return false;
        }

        self::logMessage('debug', 'MongoDB Read OK: ' . $type . '/' . $name . '/' . $ID);

        if ($cache && $onlyCheck)
            return TRUE;
        if ($this->compress)

        try {
            if ($this->compress == TRUE)
                $output = @unserialize($cache);
            else
                $output = @unserialize($cache);
        } catch (Exception $e) {
            return false;
        }
        return $output;
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
        $expire = time() + ($item_expiration);
        if ($this->compress == TRUE)
            $output = serialize($output);
        else
            $output = serialize($output);
        
        $this->getInstance()->update(array('KEY' => $this->keyPrefix, 'ID' => $type . '-' . $name . '-' . $ID), array('KEY' => $this->keyPrefix, 'ID' => $type . '-' . $name . '-' . $ID, 'insert' => time(), 'expires' => $expire, 'content' => $output), array("upsert" => true));

        self::logMessage('debug', 'MongoDB Write OK: ' . $type . '/' . $name . '/' . $ID);

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

        $this->getInstance()->remove(array('KEY' => $this->keyPrefix, "ID" => $type . '-' . $name . '-' . $ID));
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the cache
     *
     * @return 	boolean		TRUE, simulating success
     */
    public function cleanCache()
    {
        $this->getInstance()->remove(array('KEY' => $this->keyPrefix));
        //$this->getInstance()->drop();
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
        if (!extension_loaded('mongo')) {
            self::logMessage('error', 'The MONGODB PHP extension must be loaded to use MomgoDB Cache.','exception','DRIVER');
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * getInstance
     * Reuse mongodb class
     */
    public function getInstance()
    {
        // cache_mongodb must to be:
        //     host:port:user:pass:db
        $cache_db = explode(':', $this->getCacheConfigItem('host','mongodb','cache_hosts'));

        $cache_db_host = trim($cache_db[0]);
        $cache_db_port = trim($cache_db[1]);
        $cache_db_user = trim($cache_db[2]);
        $cache_db_pass = trim($cache_db[3]);
        $cache_db_dbname = trim($cache_db[4]);
        $cache_db_collection = trim($cache_db[5]);

        $connection_string = "mongodb://";

        if (empty($cache_db_host)):
            self::logMessage('error', "The Host must be set to connect to MongoDB");
        endif;

        if (empty($cache_db_dbname)):
            self::logMessage('error', "The Database must be set to connect to MongoDB");
        endif;

        if (!empty($cache_db_user) && !empty($cache_db_pass)):
            $connection_string .= "{$cache_db_user}:{$cache_db_pass}@";
        endif;

        if (isset($cache_db_port) && !empty($cache_db_port)):
            $connection_string .= "{$cache_db_host}:{$cache_db_port}";
        else:
            $connection_string .= "{$cache_db_host}";
        endif;

        $connection_string = trim($connection_string . "/" . $cache_db_dbname);

        // Check exists current instance
        if (!isset($this->_instance))
            $this->_instance = NULL;

        // Create new instance or return current
        if ($this->_instance == NULL) {
            $options = array();
            try {
                $client = new MongoClient($connection_string); 
                $dbconnection = new MongoDB($client, $cache_db_dbname);
                $this->_instance = new MongoCollection($dbconnection, $cache_db_collection);
                // Create base collection for xcache
                $this->_instance->update(array('KEY' => $this->keyPrefix, 'ID' => 'xcache', 'expires' => 0, 'content' => 'basecollection'), array("upsert" => true));
                $this->_instance->ensureIndex(array('ID' => 1,'KEY' => 1), array('unique' => true));
                $this->_instance->ensureIndex(array('KEY' => 1), array('unique' => false));
            } catch (MongoConnectionException $e) {
                self::logMessage('error', "Unable to connect to MongoDB: {$e->getMessage()}",'exception','CONNECT');
            }
        }

        return $this->_instance;
    }
    
    public function setOptions($options)
    {
        $this->keyPrefix = $options->OPT_PREFIX;
    }

}

// End Class

/* End of file Cache_mongodb.php */
