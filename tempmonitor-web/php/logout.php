<?php
// logs the user out by destroying the session
session_start();
session_destroy();

// redirect to the login page
header("Location: ../login.php");