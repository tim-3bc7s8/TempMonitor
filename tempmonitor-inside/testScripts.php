<?php

echo date('F d, Y G:m:s', time()) . " Begin testScripts <br />";

// used to time how long the script takes to run
function getTime() 
{ 
    $a = explode (' ',microtime()); 
    return(double) $a[0] + $a[1]; 
} 
$Start = getTime();


include 'includes/dbconfig.php';
echo "included dbconfig.php <br />";

// get which test is to be performed
if (!isset($_GET['test']) || empty($_GET['test']))
{
	echo "Error: test was not specified.<br />";
	$errorFound=true;
} else {
	$test=$_GET['test'];
	echo "Test=" . $test . "<br />";
}


if ($test == "checkDevice")
{
	echo date('F d, Y G:m:s', time()) . " Begin checkDevice test.<br />";
	include "tempMonitorDatabase.php";

	$tempDB = new tempMonitorDatabase();
	$tempDB->init($host, $username, $password, $db_name, true);

	$device_address = $_GET['id'];
	
	$tempDB->checkDevice($device_address, true);
	$End = getTime();
	echo "End checkDevice test.<br />";
	echo "Time taken = ".number_format(($End - $Start),6)." secs";
}

if ($test == "updateData")
{
	echo date('F d, Y G:m:s', time()) . " Begin updateData test.<br />";
	include "tempMonitorDatabase.php";
	
	$tempDB = new tempMonitorDatabase();
	$tempDB->init($host, $username, $password, $db_name, true);
	
	// get the device address as an array
	$device_address=$_GET['id'];
	// get the data as an array
	$data=$_GET['data'];
	
	$i = 0;
	foreach ($device_address as $sensor)
	{
		$tempDB->checkDevice($sensor, true);
	}
		
	// this section is for debugging. Displays the sensor id's and temps.
	$i = 0;
	foreach ($device_address as $sensor)
	{
		echo "sensor id: " . $sensor . ". Temperature: " . $data[$i] . "<br />";
		$i++;
	}
	
	$tempDB->updateData($device_address, $data, true);
	
	
	$End = getTime();
	echo "End updateData test.<br />";
	echo "Time taken = ".number_format(($End - $Start),6)." secs";
}








