<?php

// logging system
require_once 'KLogger.php';

// sets the logging level.
$log = KLogger::instance('../logs', KLogger::DEBUG);

// connect to the database
require_once 'dbconfig.php';


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

// get the most recent timestamp
$sql = "SELECT id, MAX(ts) from data_period";

$result = mysql_query($sql);
if (!$result)
{
	// log any errors
	$log->logError(mysql_error());
	exit('Database error.');
}

while($row = mysql_fetch_row($result))
{
    $currentTs = $row[1];
    echo $currentTs;
    $row_count++;
}


// Use to time how long it takes to run this script.
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$endtime = $mtime; 
$totaltime = ($endtime - $starttime); 
$log->logInfo("The temps.csv file was created. " . $row_count . " rows in " . number_format($totaltime, 3, '.', '') . " seconds. Avg/row: " . number_format($totaltime/$row_count, 3, '.', ''));