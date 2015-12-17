<?php
require_once '../../vendor/autoload.php';

define("XCACHE_CONFPATH",__DIR__);

define("HTMLCODE_BR",php_sapi_name()=='cli'?"\n":"<br>"); 
define("HTMLCODE_HR",php_sapi_name()=='cli'?"\n----------------------------------------------------------\n":"<hr>"); 

class testXCache {
    use XCacheDriver;
    public function __construct() {
    }
    
    public function _test1()
    {
        return time();
    }

    public function _test2()
    {
        return time();
    }
            
    public function _mytest()
    {
        return time();
    }

    public function _othertest()
    {
        return time();
    }
    
    public function myMethod($param1,$param2)
    {
        return "Param1: $param1 Param2: $param2 Date: ".date('Y-m-d H:i:s');
    }
}

function showresults($title,$testmethod)
{
    $test = new testXCache();
    $result = $test->xCachePass()->{$testmethod}();
    $now = time();

    if ($result <> $now)
        $content = "Current cache was generated ".($now - $result)." seconds ago".HTMLCODE_BR;
    else
        $content = "Starting CACHE".HTMLCODE_BR;  

    echo HTMLCODE_HR."$title".HTMLCODE_BR."
        $content";
}

/** Examples for xCachePass, apply cache if method start with _ **/
showresults('TEST1','test1');
showresults('TEST2','test2');
showresults('TEST3','mytest');
showresults('TEST4','othertest');

/** Examples for xCacheValue, set & get a value from cache **/
echo HTMLCODE_HR."Cache a value width default TTL".HTMLCODE_BR;
$testClass = new testXCache();
echo $testClass->xCacheValue("cache_values","myTestValue",md5('myTestValue'),'Current date is '.date('Y-m-d H:i:s'));

echo HTMLCODE_HR."Cache another value width regexp TTL".HTMLCODE_BR;
$testClass = new testXCache();
echo $testClass->xCacheValue("cache_values","varTest",md5('varTest'),'Current date is '.date('Y-m-d H:i:s'));

/** Examples for xCacheMethod, set & get a result from and existing method, passing parameters **/
echo HTMLCODE_HR."Cache a current method".HTMLCODE_BR;
$testClass = new testXCache();
$params = array('value1','value2');
echo $testClass->xCacheMethod("cache_methods","testXCache_myMethod",md5('testXCache_myMethod'.json_encode($params)),$testClass,'myMethod',$params);

echo HTMLCODE_HR;
echo "Retrieve a previous saved value.".HTMLCODE_BR;
echo $testClass->xCacheValue("cache_values","varTest",md5('varTest'));

echo HTMLCODE_HR;
echo "Change options in your app / [appname] /xcacheconf.php to see how it works.".HTMLCODE_BR;
echo "See app / [appname] / testXCache.php to understand how it works.";

