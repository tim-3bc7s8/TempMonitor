<?php

/***************************************************************
    signout.php
	Part of the TempMonitor project
	
	This script logs the user out of the current session.
    
****************************************************************/


session_start();
session_destroy();
header("Location: ../login.php");