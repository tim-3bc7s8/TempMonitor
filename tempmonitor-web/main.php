<?php


/***************************************************************
    Check if the user is logged in. If not, 
    redirect to login page.
****************************************************************/
session_start();
if( !isset($_SESSION['username']) ) {
    header("Location: login.php");
} else {
    header("Connection: Keep-Alive");
}
?>


<!--  main page -->
    

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Temp Monitor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="ico/favicon.png">
                                    
    <script src="js/dygraph-combined.js"></script>
    <script src="js/jquery.js"></script>
    <script src="js/tempmonitor.js"></script>
  </head>

  <body>

  <!-- Top Nav Bar -->
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="main.php">Temp Monitor</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="#">Manager</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="nav-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
            </ul>
              <!-- Pause/Play, Settings and Signout buttons -->     
            <div class="btn-toolbar btn-group pull-right">
              <button class="btn" id="pausePlayButton"><i id="pausePlayIcon" class="icon-pause"></i></button>
              <a class="btn" href="settings.php"><i class="icon-wrench"></i></button>
              <a class="btn" href="php/signout.php">Sign Out</a>
            </div>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
  
      <!-- First row - Links -->
      <div class="row">
        <div class="span2">
          <ul class="nav nav-pills nav-stacked">
            <!-- Dropdown Menu for Sensor Selection -->
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">Sensor Selection<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Sensor 1</a></li>
                <li><a href="#">Sensor 2</a></li>
                <li><a href="#">Sensor 3</a></li>
                <li><a href="#">All Sensors</a></li>
                <li><a href="#">Average</a></li>
              </ul>
            </li>
            <!-- Dropdown Menu for Average -->
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">Average<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#" class="avg-graph" data-avg="1">No Average</a></li>
                <li><a href="#" class="avg-graph" data-avg="2">Average 2</a></li>
                <li><a href="#" class="avg-graph" data-avg="3">Average 3</a></li>
                <li><a href="#" class="avg-graph" data-avg="5">Average 5</a></li>
                <li><a href="#" class="avg-graph" data-avg="10">Average 10</a></li>
                <li><a href="#" class="avg-graph" data-avg="15">Average 15</a></li>
              </ul>
            </li>
            <!-- Dropdown Menu for Time Period-->
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">Time Period<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#" class="time-graph" data-time="3">Past 3 Hours</a></li>
                <li><a href="#" class="time-graph" data-time="12">Past 12 Hours</a></li>
                <li><a href="#" class="time-graph" data-time="24">Past 24 Hours</a></li>
                <li><a href="#" class="time-graph" data-time="168">Past 7 Days</a></li>
                <li><a href="#" class="time-graph" data-time="720">Past 30 Days</a></li>
                <li><a href="#" class="time-graph" data-time="2160">Past 90 Days</a></li>
                <li><a href="#" class="time-graph" data-time="0">All Time</a></li>
              </ul>
            </li>
            <!-- end dropdown -->
          </ul>
        </div>
        <div class="span10">
          <!-- Graph -->
          <div id="graphdiv" style="width:800px; height:320px;"></div>
          <script type="text/javascript">
            g = new Dygraph(document.getElementById("graphdiv"),
                // data source
                "php/getTempCsv.php?t=1", // All data from database   
                // options
                {
                  title: 'Temperature',
                  ylabel: 'Temperature (F)',
                  drawPoints: false,
                  rollPeriod: 1,
                  showRoller: false,
                  fillGraph: false,
                  strokeWidth: 1,
                  highlightCircleSize: 4,
                  showRangeSelector: true,
                  labels: [ "Date", "Temp1", "Temp2", "Temp3" ]
                }                
              );
          </script>
        </div>
      </div>

      <!-- Second Row -->
      <div class="row">
        <!-- Current Data section -->
        <div class="span4">
          <h2>Current Data</h2>
          <table class="table table-striped">
            <tr>
              <td>Timestamp</td>
              <td id="currentTs"> </td>
            </tr>
            <tr>
              <td>Sensor 1</td>
              <td id="currentS1"> </td>
            </tr>
            <tr>
              <td>Sensor 2</td>
              <td id="currentS2"> </td>
            </tr>
            <tr>
              <td>Sensor 3</td>
              <td id="currentS3"> </td>
            </tr>
            <tr>
              <td>Average</td>
              <td id="currentAverage"> </td>
            </tr>
            <tr>
              <td>Target</td>
              <td>65.0</td>
            </tr>
          </table>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
        <!-- Historic/Statistical Data section -->
        <div class="span4">            
          <h2>Historic Data</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
        <!-- Sensor Info section -->
        <div class="span4">            
            <h2>Sensor Info</h2>
            <table class="table table-striped">
            <tr>
              <td>Wifi Uptime</td>
              <td>76:24:03</td>
            </tr>
            <tr>
              <td>Wifi IP</td>
              <td>192.168.1.140</td>
            </tr>
            <tr>
              <td>Wifi MAC</td>
              <td>00:26:51:77:82:FA</td>
            </tr>
            <tr>
              <td>Free Memory</td>
              <td>38</td>
            </tr>
            <tr>
              <td>RSSI</td>
              <td>18</td>
            </tr>
          </table>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
      </div>

      <hr>

      <footer>
        <p>&copy; Tim Morgan 2013</p>
      </footer>

    </div> <!-- /container -->

    <!-- Javascript -->    
    <script src="js/bootstrap.js"></script>

  </body>
</html>