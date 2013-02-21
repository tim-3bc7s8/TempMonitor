<?php


// connect to the frms database
include 'includes/dbconfig.php';
include 'includes/opendb.php';

$errorFound=false;

// -- Validate received data --

// sets debug mode. If debug is set to anything, it will be enabled
if (!isset($_GET['debug']))
{
	$debug=false;
} else {
	$debug=true;
}
// validate rssi was provided
if (!isset($_GET['rssi']) || empty($_GET['rssi']))
{
	if ($debug)
		echo "<p>Error: rssi was not set.</p>";
	$errorFound=true;
} else {
	$rssi=$_GET['rssi'];
}
// validate uptime was provided
if (!isset($_GET['uptime']) || empty($_GET['uptime']))
{
	if ($debug)
		echo "<p>Error: uptime was not sent.</p>";
	$errorFound=true;
} else {
	$uptime=$_GET['uptime'];
}
// validate free_memory was provided
if (!isset($_GET['free']) || empty($_GET['free']))
{
	if ($debug)
		echo "<p>Error: free_memory was not sent.</p>";
	$errorFound=true;
} else {
	$free_memory=$_GET['free'];
}


// Check if the device is in the database
if (!$errorFound)
{	 
	$sql="INSERT INTO wifi_stats (rssi, uptime, free_memory) values ('$rssi', '$uptime', '$free_memory')";
	mysql_query($sql);
} else {
	$sql="INSERT INTO wifi_stats (rssi) values (0)";
	mysql_query($sql);
}



// close the connection to the database
mysql_close($conn);



// Displays if script ran ok or if errors were found.
if (!$errorFound)
{
	echo "<p>OK!</p>";
} else {
	echo "<p>Found errors!</p>";
}