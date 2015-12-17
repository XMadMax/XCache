<?php
require_once __DIR__.'/../../../../../vendor/autoload.php';

define("XCACHE_CONFPATH",__DIR__);

class xcachetest {
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
    $test = new xcachetest();
    $result = $test->xCachePass(XCACHE_CONFPATH)->{$testmethod}();

    $now = time();

    if ($result <> $now)
        $content = "Current cache was generated ".($now - $result)." seconds ago<br>";
    else
        $content = "Starting CACHE <br>";  

    echo "<hr>$title<br>
        $content";
}

/** Examples for xCachePass, apply cache if method start with _ **/
showresults('TEST1','test1');
showresults('TEST2','test2');
showresults('TEST3','mytest');
showresults('TEST4','othertest');

echo "<hr>";
echo "Change options in your app / [appname] /xcacheconf.php to see how it works.<br>";
echo "See app / [appname] / xcachetest.php to understand how it works.";

/** Examples for xCacheValue, set & get a value from cache **/
echo "<hr>Cache a value width default TTL<br>";
$testClass = new xcachetest();
echo $testClass->xCacheValue("cache_values","myTestValue",md5('myTestValue'),'Current date is '.date('Y-m-d H:i:s'));

echo "<hr>Cache another value width regexp TTL<br>";
$testClass = new xcachetest();
echo $testClass->xCacheValue("cache_values","varTest",md5('varTest'),'Current date is '.date('Y-m-d H:i:s'));

/** Examples for xCacheMethod, set & get a result from and existing method, passing parameters **/
echo "<hr>Cache a current method<br>";
$testClass = new xcachetest();
$params = array('myParam1','myParam2');
echo $testClass->xCacheMethod("cache_methods","xcachetest_myMethod",md5('xcachetest_myMethod'.json_encode($params)),$testClass,'myMethod',$params);