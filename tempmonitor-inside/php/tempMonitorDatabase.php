<?php

/* 
	This class is used to interact with the FRMS database.
	
*/

// logging system
require_once 'php/KLogger.php';




class tempMonitorDatabase
{
	// database connection settings
	var $host="localhost"; 			// Host name 
	var $username="none"; 		// Mysql username 
	var $password="none"; 	// Mysql password 
	var $db_name="none";    		// Database name 

	// table names 
	var $tbl_info="sensor_info"; 	// contains data about the devices
	var $tbl_data_period="data_period"; // all data points are associated with a data period (time stamp)
	var $tbl_data_point="data_points"; // data collected from the sensors
	var $tbl_wifi_stats="wifi_stats"; // continuous polling data about the wifi connection
	var $tbl_wifi_info="wifi_info"; // information about the wifi device (ip, mask, gateway, ect)
	var $tbl_system_settings="system_settings"; // general setting about how the frms system works (logging level, ect)
        
        // location of log file
	var $log_file="logs";
	
	// initializes database connection info
	public function init($new_host, $new_username, $new_password, $new_db_name, $debug = KLogger::ERR)
	{
		// sets the logging level at DEBUG.
		$log = KLogger::instance($this->log_file, $debug);
		
		$log->logInfo('Initialize tempMonitorDatabase');
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
	
	
	// Check if a device is already present in the database. 
	// This does not add the device to the database, only checks for
	// its existance.
	public function deviceExists($device_address, $debug = KLogger::ERR)
	{
		// sets the logging level at DEBUG.
		$log = KLogger::instance($this->log_file, $debug);
		$log->logInfo('Checking if the device exists : ' . $device_address);
		
		// connect to the database
		$conn = mysql_connect("$this->host", "$this->username", "$this->password");
		if (!$conn)
		{
			// log any errors
			$log->logCrit('Cannot connect to database: ' . mysql_error()); 
			exit('Database Error.');
		}		
		// select the database
		$db_select = @mysql_select_db("$this->db_name");
		if (!$db_select) // log any errors
		{
			$log->logCrit('Could not select database. ' . mysql_error());
			exit('Database Error.');
		}
		
		// $num will be > 0 if anything is found in the database.
		$sql="SELECT * FROM $this->tbl_info WHERE address='$device_address'";
			$log->logDebug('Check if device is in the database...');
			$log->logDebug('sql=' . $sql);
		$result=mysql_query($sql);
		if (!$result)
		{	// log any errors
			$log->Error('Checking if device exists in database. ' . mysql_error());
			exit('Database Error.');
		}
		$num=mysql_numrows($result);
			$log->logDebug('Number of rows returned = ' . $num);
		
		// close the connection to the database
		mysql_close($conn);
		
		if ($num >= 1)
		{
			$log->logInfo('Device was found in the database.');
			return true;
		}
		else
		{
			$log->logInfo('Device was NOT found in the database.');
			return false;
		}
	}
	
	
	// Check if the device is in the database and if it is not,
	// then add it to the database.
	public function checkDevice($device_address, $debug = KLogger::ERR)
	{
		// sets the logging level at DEBUG.
		$log = KLogger::instance($this->log_file, $debug);
		$log->logDebug('tempMonitorDatabase.checkDevice(device_address=' . $device_address . ')');
		
		if ($this->deviceExists($device_address, $debug))
			return true;		
		
	
		// connect to the database
		$conn = @mysql_connect("$this->host", "$this->username", "$this->password");
		if (!$conn) // log any errors
		{
			$log->logCrit('Cannot connect to database: ' . mysql_error()); 
			exit('Database Error.');
		}
		// select the database
		$db_select = @mysql_select_db("$this->db_name");
		if (!$db_select) // log any errors
		{
			$log->logCrit('Could not select database. ' . mysql_error());
			exit('Database Error.');
		}
		
		// Add the device to the database
		$sql="INSERT INTO $this->tbl_info (address, active) values ('$device_address', true)";
			$log->logInfo('Adding the device to the database.');
			$log->logDebug('sql=' . $sql);
		$result = mysql_query($sql);
		if (!result)
		{
			$log->logError('Attempting to insert device into database. ' . mysql_error());
			exit('Database Error.');
		}
		mysql_close($conn);
	}

	
	// Inserts data into the database.
	// device_address and data are arrays
	public function updateData($device_address, $data, $debug = KLogger::ERR)
	{
		// sets the logging level..
		$log = KLogger::instance($this->log_file, $debug);
		$log->logInfo('Starting tempMonitorDatabase updateData process.');
	
		// connect to the database
		$conn = mysql_connect("$this->host", "$this->username", "$this->password"); 
		if (!$conn) // log any errors
		{
			$log->logCrit('Cannot connect to database: ' . mysql_error()); 
			exit('Database Error.');
		}
		// select the database
		$db_select = @mysql_select_db("$this->db_name");
		if (!$db_select) // log any errors
		{
			$log->logCrit('Could not select database. ' . mysql_error());
			exit('Database Error.');
		}
		
		// creates the time stamp (data period)
		$sql="INSERT INTO $this->tbl_data_period (ts) values ( now() )";
			$log->logDebug('sql=' . $sql);
		$result = mysql_query($sql);
		if (!$result)
		{
			// log any errors
			$log->logError(mysql_error());
		}
		$data_period_id = mysql_insert_id();
			$log->logDebug('data_period_id=' . $data_period_id);
		
		// inserts the data into the DB
		$i = 0;
		foreach ($device_address as $sensor)
		{
			$this->checkDevice($sensor);
		
			// connect to the database
			$conn = mysql_connect("$this->host", "$this->username", "$this->password"); 
			if (!$conn) // log any errors
			{
				$log->logCrit('Cannot connect to database: ' . mysql_error()); 
				exit('Database Error.');
			}
			// select the database
			$db_select = @mysql_select_db("$this->db_name");
			if (!$db_select) // log any errors
			{
				$log->logCrit('Could not select database. ' . mysql_error());
				exit('Database Error.');
			}
			
			$sql="INSERT INTO $this->tbl_data_point (data_period, sensor_id, data) SELECT '$data_period_id', sensor_info.id, '$data[$i]' FROM sensor_info WHERE sensor_info.address = '$sensor'"; 
				$log->logDebug('Inserting data into database...');
				$log->logDebug('sql=' . $sql);
			$result = mysql_query($sql);
			if (!$result)
			{
				$log->logError(mysql_error());
			}
			$i++;
		}
		mysql_close($conn);
	}


	// Inserts the wifi stats into the database.
	public function updateWifiStats($wifi_id, $rssi, $uptime, $free_memory, $debug = KLogger::ERR)
	{
		// sets the logging level.
		$log = KLogger::instance($this->log_file, $debug);
		$log->logInfo('Starting tempMonitorDatabase updateWifiStats process.');
		
		// connect to the database
		$conn = mysql_connect("$this->host", "$this->username", "$this->password"); 
		if (!$conn) // log any errors
		{
			$log->logCrit('Cannot connect to database: ' . mysql_error()); 
			exit('Database Error.');
		}
		// select the database
		$db_select = @mysql_select_db("$this->db_name");
		if (!$db_select) // log any errors
		{
			$log->logCrit('Could not select database. ' . mysql_error());
			exit('Database Error.');
		}
		
		$sql="INSERT INTO $tbl_wifi_stats (wifi_id, rssi, uptime, free_memory) VALUES ('$wifi_id', '$rssi', '$uptime', '$free_memory')";
			$log->logDebug('Inserting wifi stats into database...');
			$log->logDebug('sql=' . $sql);
		$result = mysql_query($sql);
		if (!$result)
		{
			$log->logError(mysql_error());
		}
		mysql_close($conn);
	}
	
	

}

