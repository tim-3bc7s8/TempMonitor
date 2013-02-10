<?php

/***************************************************************
    updateData.php
	Part of the frmsProject
	
	This script takes data passed to it and inserts it into the
	database by way of the tempMonitorDatabase class. 
	A minimum of two variables must be passed, 'id' and 'data'.
	id and data can be arrays, but must be of equal size.
	
	example:
	updateData.php?id[]=6&data[]=32.3&id[]=7&data[]=37.8
	
	This example shows the usage of two sensors with data.
		sensor id: 6	data: 32.3
		sensor id: 7	data: 37.8
****************************************************************/





/***************************************************************
    Included files
****************************************************************/

// database connection settings
require_once 'php/dbconfig.php';

// logging system
require_once 'php/KLogger.php';


/***************************************************************
    Setup the logging system.
	This connects to the database and gets the current
	logging level. Table: system_settings	
	There should only be one row in this table, 
	id=1, so it'll always select id=1. The column is called
	log_level. The viable settings are listed below. If the 
	database value falls outside of this range, it will default
	to Error.
	
	0 = Emergency
	1 = Alert
	2 = Critical
	3 = Error
	4 = Warning
	5 = Notice
	6 = Info
	7 = Debug
	
	RECOMMENDED SETTING IS 3-Error !!!
****************************************************************/

// connect to the database and get the logging level
$conn = mysql_connect("$host", "$username", "$password")or die('Error: Cannot connect to database: ' . mysql_error()); 
mysql_select_db("$db_name")or die("Error: Cannot select database.");
$sql="SELECT log_level FROM system_settings WHERE id=1";
$result=mysql_query($sql) or die();
$row = mysql_fetch_array($result);
$log_level = $row['log_level'];
// if the log level is outside of the set range, set it to default (error).
if ($log_level < KLogger::EMERG || $log_level > KLogger::DEBUG)
	$log_level = KLogger::ERR;

// sets the logging level.
$log = KLogger::instance('logs', $log_level);


// tracks errors. when an error is found, it will prevent certain pieces of code to run
$errorFound=false;

// send the URL string to the log file
$urlString = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$queryString = $_SERVER['QUERY_STRING'];
$log->logDebug($urlString . "?" . $queryString);



/***************************************************************
    Data Validation
****************************************************************/

// sets debug mode. If debug is set to anything, it will be enabled
if (!isset($_GET['debug']))
{
	$debug=false;
} else {
	$debug=true;
}
// validate that the device address was sent and not empty
if (!isset($_GET['id']) || empty($_GET['id']))
{
	$log->logError('Device address was not received in URL.');
	$errorFound=true;
} else {
	$device_address=$_GET['id'];
}
// validate that data was sent and not empty
if (!isset($_GET['data']) || empty($_GET['data']))
{
	$log->logError('Data was not received in URL.');
	$errorFound=true;
} else {
	$data=$_GET['data'];
}

/***************************************************************
    Process the Data
****************************************************************/

if (!$errorFound)
{
	$log->logNotice('Begin updating data process.');
	
	// Initiate the database connection object.
	require_once "php/tempMonitorDatabase.php";	
	$tempDB = new tempMonitorDatabase();
	$tempDB->init($host, $username, $password, $db_name, $log_level);
	
	// this section is for debugging. Displays the sensor id's and temps.
	if ($log_level >= KLogger::INFO)
		{
		$log->logInfo('Received data:');
		$i = 0;
		foreach ($device_address as $sensor)
		{
			$log->logInfo('sensor id: ' . $sensor . ' temperature: ' . $data[$i]);
			$i++;
		}
	}
	
	// Put the data into the database. $device_address and $data variables
	// can be arrays.
	$tempDB->updateData($device_address, $data, $log_level);
	
	$log->logInfo('End updating data.');
}

if (!$errorFound && $debug)
{
	echo "OK!";
}