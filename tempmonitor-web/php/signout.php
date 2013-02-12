<?php

/***************************************************************
    signout.php
	Part of the TempMonitor project
	
	This script logs the user out of the current session.
    
****************************************************************/


session_start();
if( !isset($_SESSION['username']) ) {
    // if a user is not logged in, direct to the login page
    header("Location: ../login.php");
}

// logs the user out by destroying the session
session_destroy();

// redirect to the login page
header("Location: ../login.php");

