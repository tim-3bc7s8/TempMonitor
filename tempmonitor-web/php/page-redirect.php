<?php

session_start();
if( !isset($_SESSION['username']) ) {
    // if a user is not logged in, direct to the login page
    header("Location: login.php");
}



// get the username and password from the login page
if (isset($_POST['settings'])) {
    $option = $_POST['settings'];
} elseif (isset($_POST['signout'])) {
    $option = $_POST['signout'];
}

// --- Sign Out ---
if ($option == "signout") {
    // logs the user out by destroying the session
    session_destroy();

    // redirect to the login page
    header("Location: ../login.php");
}

// --- Settings ---
if ($option == "settings") {
    // redirect to settings page
    header("Location: ../settings.php");
}

echo "Invalid option: ".$option;