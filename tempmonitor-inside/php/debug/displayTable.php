<?php


/***************************************************************
    Included files
****************************************************************/

// database connection settings
require_once '../dbconfig.php';

require_once 'simpleDatabase.php';

require_once 'displayTableTemplate.php';


/***************************************************************
    Validate data
****************************************************************/
$errorFound = false;
if (!isset($_GET['table']) || empty($_GET['table']))
{
	//$log->logError('Table name was not received in URL.');
	$errorFound=true;
} else {
	$tableName=$_GET['table'];
}


if (!$errorFound)
{
	// create a connection to the database
	$db = new simpleDatabase();
	$db->init($host, $username, $password, $db_name);

	// get the column names as an array
	$columns = $db->getColumns($tableName);

	// get the rows from as an array
	$rows = $db->getRows($tableName);

	// create the template and pass the paramaters to it
	$displayTable = new Table_Template();
	$displayTable->setTableName($tableName);
	$displayTable->setColumns($columns);
	$displayTable->setRows($rows);

	// creates the view
	$template = 'table.php';
	echo $displayTable->render($template);
}

