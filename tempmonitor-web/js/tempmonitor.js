// function that will either have setInterval active or clearInterval
var autoUpdate;

// --- Document Ready ---
$("document").ready(function() {        
  updateData();
  // This is the polling function. Periodically checks for new info from the database.
  autoUpdate = setInterval(updateData, 1000);
  
  $("#pausePlayButton").bind('click', toggleAutoPolling);
  
});

// Function that start/stop the automatic update polling.
function toggleAutoPolling() {
  // toggle the icon
  $("#pausePlayIcon").toggleClass("icon-pause");
  $("#pausePlayIcon").toggleClass("icon-play");
  // determine whether to start or stop auto-update
  if ($('#pausePlayIcon').hasClass('icon-pause')) {
    autoUpdate = setInterval(updateData, 1000);
  } else {
    clearInterval(autoUpdate);
  }
}

function updateData() {
  // grab the current CSV data
  var newData = "php/getTempCsv.php";

  
  // update the graph
  g.updateOptions( { 'file': newData } );
  
  // update current data section
  updateCurrentData();
}

// update current data section
function updateCurrentData() {
  updateCurrentTimeStamp();
}

function updateCurrentTimeStamp() {
  var url = "./php/currentdata.php";
  $.getJSON(url, function(data) {
    $("#currentTs").text(data.ts);
    $("#currentS1").text(data.sensor1);
    $("#currentS2").text(data.sensor2);
    $("#currentS3").text(data.sensor3);
    $("#currentAverage").text(data.average);
  });
  
  
}




