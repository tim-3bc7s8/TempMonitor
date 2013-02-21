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