<?php

session_start();

if( isset($_SESSION['username']) ) {
    // if user is already logged in, send to main page
    header("Location: main.php");
} else {
    // if a user is not logged in, direct to the login page
    header("Location: login.php");
}
