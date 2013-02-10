<?php

// logging system
require_once '../KLogger.php';





class simpleDatabase
{
	// database connection settings
	var $host="localhost"; 			// Host name 
	var $username="none"; 		// Mysql username 
	var $password="none"; 	// Mysql password 
	var $db_name="none";    		// Database name

	public function init($new_host, $new_username, $new_password, $new_db_name, $log_level = KLogger::ERR)
	{
		// sets the logging level.
		$log = KLogger::instance('../logs', $log_level);
		
		$log->logInfo('Initialize simpleDatabase');
		$log->logDebug('Received values: host=' . $new_host . 
						', username=' . $new_username . 
						', password=' . $new_password . 
						', db_name='  . $new_db_name);		
		
		$this->host = $new_host;
		$this->username = $new_username;
		$this->password = $new_password;
		$this->db_name = $new_db_name;
		
		$log->logInfo('Set values: host=' . $this->host . 
						', username=' . $this->username . 
						', password=' . $this->password . 
						', db_name='  . $this->db_name);
	}

	
	
	public function getColumns($tableName, $log_level = KLogger::ERR)
	{
		// sets the logging level.
		$log = KLogger::instance('../logs', $log_level);
		$log->logInfo('Getting column names from ' . $tableName);
	
		// connect to the database
		$conn = mysql_connect("$this->host", "$this->username", "$this->password")or die($log->logError('Cannot connect to database: ' . mysql_error())); 
		mysql_select_db("$this->db_name")or die($log->logError('Could not select database.'));
		
		$sql='SELECT * FROM ' . $tableName;
		$result=mysql_query($sql);
		
		if (!$result) 
		{
			echo 'Could not run query: ' . mysql_error();
			echo '<br />';
			echo 'sql=' . $sql;
			exit;
		}
		
		$field = mysql_num_fields($result);
		
		for ($i = 0; $i < $field; $i++)
		{
			$columns[] = mysql_field_name($result, $i);
		}
		
		return $columns;
	}
	
	public function getRows($tableName, $log_level = KLogger::ERR)
	{
		// sets the logging level at DEBUG.
		$log = KLogger::instance('../logs', $log_level);
		$log->logInfo('Getting rows from ' . $tableName);
		
		// connect to the database
		$conn = mysql_connect("$this->host", "$this->username", "$this->password")or die($log->logError('Cannot connect to database: ' . mysql_error())); 
		mysql_select_db("$this->db_name")or die($log->logError('Could not select database.'));
		
		$sql='SELECT * FROM ' . $tableName;
		$result=mysql_query($sql);
		
		$rows = array();

		while(($row = mysql_fetch_array($result))) 
		{
			$rows[] = $row;
		}
		
		return $rows;
	}




}