<?php

/***************************************************************
    currentdata.php
	Part of the TempMonitor project
	
	This script connects to the database and gets information
    for the 'current data' section on the main page.
	
****************************************************************/


/***************************************************************
    Set up the logging system and DB config settings
****************************************************************/

require_once 'KLogger.php';
// Sets the logging file location and the log level.
$log = KLogger::instance('../logs', KLogger::ERR);
// database connection settings.
require_once 'dbconfig.php';


/***************************************************************
    This is the data that is gathered from the database
    and sent back to the client as JSON.
****************************************************************/

class currentData {
    public $ts = "";
    public $sensor1 = "";
    public $sensor2 = "";
    public $sensor3 = "";
    public $average = "";   
}
$curData = new currentData();

// used for timing the script execution.
// Is this needed?
$log->logDebug("Getting current data.");
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$starttime = $mtime;
$row_count = 0; // used to count the number of rows created


/***************************************************************
    Make the connection to the database. 
    Use opendb.php instead??
****************************************************************/
 
$conn = mysql_connect("$host", "$username", "$password");
if (!$conn) {
	$log->logCrit('Cannot connect to database: ' . mysql_error()); 
	exit('Database Error.');
}
$db_select = @mysql_select_db("$db_name");
if (!$db_select) {
	$log->logCrit('Could not select database. ' . mysql_error());
	exit('Database Error.');
}


/***************************************************************
    Get the most recent timestamp from the data_period table.
****************************************************************/

$sql = "SELECT id, ts FROM data_period GROUP BY id HAVING ts = MAX(ts)";
$result = mysql_query($sql);
if (!$result) {
	$log->logError(mysql_error());
	exit('Database error.');
}
while($row = mysql_fetch_row($result)) {
    $data_period_id = $row[0];
    $currentTs = $row[1];
    $curData->ts = $currentTs;
    $row_count++; // not needed if we stop using the timing script
}


/***************************************************************
    Get the current sensor data - based on the data_period
    id from the last section.
****************************************************************/

$sql = "SELECT sensor_id, data FROM data_points WHERE data_period=$data_period_id";
$result = mysql_query($sql);
if (!$result) {
	$log->logError(mysql_error());
	exit('Database error.');
}

while($row = mysql_fetch_row($result)) {
    $sensor_id = $row[0];
    $sensor_data = $row[1];
    if ($sensor_id == "1") $curData->sensor1 = $row[1];
    if ($sensor_id == "2") $curData->sensor2 = $row[1];
    if ($sensor_id == "3") $curData->sensor3 = $row[1];
    $row_count++; // not needed if we stop using the timing script
}


/***************************************************************
    Calculate the average of the three sensors.
****************************************************************/
$curData->average = number_format((($curData->sensor1 + $curData->sensor2 + $curData->sensor3) / 3), 1, '.', '');


/***************************************************************
    Encode the data into JSON format that will be picked
    up by the client.
****************************************************************/
echo json_encode($curData);

// Used to time how long it takes to run this script.
// Is this needed??
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$endtime = $mtime; 
$totaltime = ($endtime - $starttime); 
$log->logInfo("Current Data was collected. " . $row_count . " rows in " . number_format($totaltime, 3, '.', '') . " seconds. Avg/row: " . number_format($totaltime/$row_count, 3, '.', ''));