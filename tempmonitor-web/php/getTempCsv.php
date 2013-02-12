<?php

/***************************************************************
    getTempCsv.php
	Part of the TempMonitor project
	
	This script builds a CSV file from sensor data stored
    in the database.
	
****************************************************************/

/***************************************************************
    Set up the logging system and DB config settings
****************************************************************/

require_once 'KLogger.php';
// Sets the logging file location and the log level.
$log = KLogger::instance('../logs', KLogger::ERR);
// database connection settings.
require_once 'dbconfig.php';


$errorFound=false;

// -- Validate received data --

// sets debug mode. If debug is set to anything, it will be enabled
if (!isset($_GET['debug'])) {
	$debug=false;
} else {
	$debug=true;
}


/***************************************************************
    Debug script used to time the script execution 
****************************************************************/

$log->logDebug("Begin building temps.csv file.");
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$starttime = $mtime;
$row_count = 0; // use to count the number of rows created


/***************************************************************
    Make the connection to the database. 
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
    Headers for CSV file format
****************************************************************/

header("Content-type: application/csv");  
header("Cache-Control: no-store, no-cache");  
header('Content-Disposition: attachment; filename="temps.csv"'); 


/***************************************************************
    Header (title) line in the CSV file
****************************************************************/
// Begin CSV file
echo "Date,Temp1,Temp2,Temp3\n";

/***************************************************************
    Query the database and build the CSV data
****************************************************************/
$sql = "SELECT data_period.ts AS date, dp1.data AS temp1, dp2.data AS temp2, dp3.data AS temp3
		FROM data_period
		LEFT OUTER JOIN data_points AS dp1 ON dp1.data_period=data_period.id AND dp1.sensor_id=1
		LEFT OUTER JOIN data_points AS dp2 ON dp2.data_period=data_period.id AND dp2.sensor_id=2
		LEFT OUTER JOIN data_points AS dp3 ON dp3.data_period=data_period.id AND dp3.sensor_id=3";

$result = mysql_query($sql);
if (!$result) {
	$log->logError(mysql_error());
	exit('Database error.');
}
while($row = mysql_fetch_row($result)) {
    $date = $row[0];
	$temp1 = $row[1];
	$temp2 = $row[2];
	$temp3 = $row[3];    
	echo "$date,$temp1,$temp2,$temp3\n";
	$row_count++;
}
echo "\n";
// End CSV file


/***************************************************************
    Debug script used to time the script execution 
****************************************************************/
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$endtime = $mtime; 
$totaltime = ($endtime - $starttime); 
$log->logInfo("The temps.csv file was created. " . $row_count . " rows in " . number_format($totaltime, 3, '.', '') . " seconds. Avg/row: " . number_format($totaltime/$row_count, 3, '.', ''));