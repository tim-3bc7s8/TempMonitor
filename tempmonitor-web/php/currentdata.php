<?php

// logging system
require_once 'KLogger.php';

// sets the logging level.
$log = KLogger::instance('../logs', KLogger::ERR);

// connect to the database
require_once 'dbconfig.php';

// Use for JSON data???
class currentData {
    public $ts = "";
    public $sensor1 = "";
    public $sensor2 = "";
    public $sensor3 = "";
    public $average = "";   
}

// used for timing the script execution
$log->logDebug("Getting current data.");
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$starttime = $mtime;
$row_count = 0; // use to count the number of rows created

// connect to the database
$conn = mysql_connect("$host", "$username", "$password");
if (!$conn)
{
	// log any errors
	$log->logCrit('Cannot connect to database: ' . mysql_error()); 
	exit('Database Error.');
}		
// select the database
$db_select = @mysql_select_db("$db_name");
if (!$db_select) // log any errors
{
	$log->logCrit('Could not select database. ' . mysql_error());
	exit('Database Error.');
}

// Create the currentData object that will be encoded to JSON
$curData = new currentData();

// get the most recent timestamp
$sql = "SELECT id, ts FROM data_period GROUP BY id HAVING ts = MAX(ts)";
$result = mysql_query($sql);
if (!$result)
{
	// log any errors
	$log->logError(mysql_error());
	exit('Database error.');
}

while($row = mysql_fetch_row($result))
{
    $data_period_id = $row[0];
    $currentTs = $row[1];
    $curData->ts = $currentTs;
    $row_count++;
}

// Get the current sensor data.
$sql = "SELECT sensor_id, data FROM data_points WHERE data_period=$data_period_id";
$result = mysql_query($sql);
if (!$result)
{
	// log any errors
	$log->logError(mysql_error());
	exit('Database error.');
}

while($row = mysql_fetch_row($result))
{
    $sensor_id = $row[0];
    $sensor_data = $row[1];
    if ($sensor_id == "1") $curData->sensor1 = $row[1];
    if ($sensor_id == "2") $curData->sensor2 = $row[1];
    if ($sensor_id == "3") $curData->sensor3 = $row[1];
    $row_count++;
}

// Get the average of the three sensors
$curData->average = number_format((($curData->sensor1 + $curData->sensor2 + $curData->sensor3) / 3), 1, '.', '');

echo json_encode($curData);

// Use to time how long it takes to run this script.
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$endtime = $mtime; 
$totaltime = ($endtime - $starttime); 
$log->logInfo("Current Data was collected. " . $row_count . " rows in " . number_format($totaltime, 3, '.', '') . " seconds. Avg/row: " . number_format($totaltime/$row_count, 3, '.', ''));