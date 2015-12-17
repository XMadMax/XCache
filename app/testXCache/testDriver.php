<?php

// Define the xcacheconf configuration location
define("XCACHE_CONFPATH",__DIR__);
define("HTMLCODE_BR",php_sapi_name()=='cli'?"\n":"<br>");
define("HTMLCODE_HR",php_sapi_name()=='cli'?"\n----------------------------------------------------------\n":"<hr>");


// Define your own logMessage function to propagate debugging info to your app
function logMessage($type,$msg)
{
    echo "<!-- $type :  $msg -->\n";
}

// Include composer autoload
include_once "../../vendor/autoload.php";

class TestDriver
{	
// include trait XCache Driver to allow method be called withour starting '_'
	use XCacheDriver;


// Define yuor method with '_' to be available to xcache
	public function _currentTime()
	{
		return date('Y-m-d H:i:s');
	}


	public function _testOne($param='')
	{
		return "Param=$param , date=".$this->currentTime();
	}
}

$test = new TestDriver();

echo HTMLCODE_HR;
echo "Cache started : ".$test->currentTime().HTMLCODE_BR;
echo HTMLCODE_HR;
echo "TestOne (1): ".$test->testOne('Example').HTMLCODE_BR;
echo HTMLCODE_HR;
echo "TestOne (2): ".$test->testOne(date('Y-m-d H:i')).HTMLCODE_BR;
echo HTMLCODE_HR;
echo "TestOne (3): ".$test->testOne(date('Y-m-d H:i:s')).HTMLCODE_BR;
echo HTMLCODE_HR;
echo "CurrentTime (nocache): ".$test->_currentTime().HTMLCODE_BR;
echo HTMLCODE_HR;

