<?php

// Define the xcacheconf configuration location
define("XCACHE_CONFPATH",__DIR__);

define("HTMLCODE_BR",php_sapi_name()=='cli'?"\n":"<br>");
define("HTMLCODE_HR",php_sapi_name()=='cli'?"\n----------------------------------------------------------\n":"<hr>");


// Include composer autoload
include_once "../../vendor/autoload.php";

    $XCache = new XCache();
    $XCache->setCacheHeaders();
    if ($XCache->enableCache()) {
        echo HTMLCODE_HR."Max memory: ".number_format(memory_get_usage()/1000,0)."Kb".HTMLCODE_BR;
      	die();
    }


class TestPageCache
{	

// Define yuor method with '_' to be available to xcache
	public function getPage()
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


     $XCache->writeAndFlushCache();
    echo HTMLCODE_HR."PAGE CACHED NOW".HTMLCODE_BR;

