/**************************************************************
    The autoUpdate variable stores the setInterval of 
    updateData(). With this being a variable, we can either 
    setInterval for a specific refresh time or we can 
    clearInterval to stop polling for new data.
****************************************************************/
var autoUpdate;


/***************************************************************
    Document Ready
****************************************************************/
$("document").ready(function() {        
  updateData();
  // This is the polling function. Periodically checks for new info from the database.
  autoUpdate = setInterval(updateData, 1000);
  
  $("#pausePlayButton").bind('click', toggleAutoPolling);
  
});


/***************************************************************
    Toggle automatic update polling
****************************************************************/
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


/***************************************************************
    Refresh data on the main screen
****************************************************************/
function updateData() {
  // grab the current CSV data
  var newData = "php/getTempCsv.php";
  
  // update the graph
  g.updateOptions( { 'file': newData } );
  
  // update current data section
  updateCurrentData();
}


/***************************************************************
    Refresh 'current data' section of the main screen
****************************************************************/
function updateCurrentData() {
  var url = "./php/currentdata.php";
  $.getJSON(url, function(data) {
    $("#currentTs").text(data.ts);
    $("#currentS1").text(data.sensor1);
    $("#currentS2").text(data.sensor2);
    $("#currentS3").text(data.sensor3);
    $("#currentAverage").text(data.average);
  });
}
