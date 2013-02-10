<?php

// Connect to server and select database.

$conn = mysql_connect("$host", "$username", "$password")or die("Error: Cannot connect to database."); 
mysql_select_db("$db_name")or die("Error: Cannot select database.");