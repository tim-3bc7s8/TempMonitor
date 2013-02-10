<?php

session_start();
if( !isset($_SESSION['username']) ) {
    // if a user is not logged in, direct to the login page
    header("Location: login.php");
}

// logs the user out by destroying the session
session_destroy();

// redirect to the login page
header("Location: ../login.php");

