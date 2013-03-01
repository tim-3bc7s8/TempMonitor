<?php


/***************************************************************
    Check if the user is logged in. If not, 
    redirect to login page.
****************************************************************/
session_start();
if( !isset($_SESSION['username']) ) {
    header("Location: login.php");
}


/***************************************************************
    Check for passed variables.
****************************************************************/
if (isset(graphAverage)) {
    // update graph average in user_settings table
}


if (isset(graphTimePeriod)) {
    // update graph time period in user_settings table
}

