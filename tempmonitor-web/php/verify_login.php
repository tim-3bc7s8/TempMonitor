<?php

/***************************************************************
    verify_login.php
	Part of the TempMonitor project
	
	This script checks the login information against the
    database to verify that the user can login.
	
	Necessary Paramaters:
        POST['username']
        POST['password']
    
    If login is successful, the user is redirected to the main
    page (main.php). If unsuccessful, the user will be redirected
    to the login page with an error message.
    
****************************************************************/

session_start();

// database settings (hostname, username, pw, dbname)
include("dbconfig.php");
include("opendb.php");


/***************************************************************
    Get the username and password from the login page
****************************************************************/

if (isset($_POST['username'])) {
    $username = $_POST['username'];
} else {
    $username = "";    
}
if (isset($_POST['password'])) {
$password = sha1($_POST['password']);
} else {
    $password = "";
}

// Protect against MySQL injection
$username = stripslashes($username);
$username = mysql_real_escape_string($username);


/***************************************************************
    Verify login information against the database
****************************************************************/

$sql = "SELECT * FROM user_info WHERE user_name='$username' AND password='$password'";
$result = mysql_query($sql);

$count = mysql_numrows($result);
include("closedb.php");

// If result matched is 1 (and only 1), then log in.
if ($count==1) {
    // Register $username in the session.
    $_SESSION['username'] = $username;
    // Redirect the user to the main page.
    header("Location: ../main.php");
} else {
    // Send the user back to the login page with an error message.
    header("Location: ../login.php?faillogin=true");
}