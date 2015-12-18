<?php

// Define the xcacheconf configuration location
define("XCACHE_CONFPATH",__DIR__);
define("HTMLCODE_BR",php_sapi_name()=='cli'?"\n":"<br>");
define("HTMLCODE_HR",php_sapi_name()=='cli'?"\n----------------------------------------------------------\n":"<hr>");

// Include composer autoload
include_once "../../../../../vendor/autoload.php";

class TestPageCache
{	
// include trait XCache Driver to allow method be called withour starting '_'
	use XCacheDriver;


// Define yuor method with '_' to be available to xcache
	public function _getPage()
	{
		$page = file_get_contents('http://www.php.net/');
		return $page;
	}

}

$test = new TestPageCache();

echo HTMLCODE_HR;
echo "PAGE CACHE ".HTMLCODE_BR;
echo HTMLCODE_HR;
echo $test->getPage();

