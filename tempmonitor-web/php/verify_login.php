<?php

session_start();

// database settings (hostname, username, pw, dbname)
include("dbconfig.php");
include("opendb.php");

// get the username and password from the login page
if (isset($_POST['username'])){
    $username = $_POST['username'];
} else {
    $username = "";    
}
if (isset($_POST['password'])){
$password = sha1($_POST['password']);
} else {
    $password = "";
}

// To protect against MySQL injection
$username = stripslashes($username);
$username = mysql_real_escape_string($username);

$sql = "SELECT * FROM user_info WHERE user_name='$username' AND password='$password'";
$result = mysql_query($sql);

$count = mysql_numrows($result);
include("closedb.php");

// If result matched is 1 (and only 1), then log in.
if ($count==1) {
    // Register $username
    $_SESSION['username'] = $username;
    // redirect to index page
    header("Location: ../main.php");
} else {
    header("Location: ../login.php?faillogin=true");
}


