/**************************************************************
    The autoUpdate variable stores the setInterval of 
    updateData(). With this being a variable, we can either 
    setInterval for a specific refresh time or we can 
    clearInterval to stop polling for new data.
****************************************************************/
var autoUpdate;
var updateInterval = 1000;

/**************************************************************
    The timeFilterHours variable stores how far back in time that
    the graph will display. A value of 0 will display all data.
****************************************************************/
var timeFilterHours;
var defaultTimeFilterHours = 3;

/**************************************************************
    The currentTimeStamp holds the most recent timestamp
    that was received from the server. Whenever this value is
    updated, it updates the graph.
****************************************************************/
var currentTimeStamp;

/**************************************************************
    The dataArray holds the data for the graph. Whenever new
    data is received, it is appended to this array and then
    this array is used to update the graph.
****************************************************************/
var dataArray;



/***************************************************************
    Toggle automatic update polling
****************************************************************/
function toggleAutoPolling() {
  // toggle the icon
  $("#pausePlayIcon").toggleClass("icon-pause");
  $("#pausePlayIcon").toggleClass("icon-play");
  // determine whether to start or stop auto-update
  if ($('#pausePlayIcon').hasClass('icon-pause')) {
    autoUpdate = setInterval(updateData, updateInterval);
  } else {
    clearInterval(autoUpdate);
  }
}


function loadCSV() {  
  // grab the current CSV data
  var url = "php/getTempCsv.php?t=" + timeFilterHours;
  
  var request = new XMLHttpRequest();
  request.open('GET', url, false);
  request.send();
  data = request.responseText;
  
  //replace UNIX new lines
  data = data.replace (/\r\n/g, "\n");
  //replace MAC new lines
  data = data.replace (/\r/g, "\n");
  //split into rows
  var rows = data.split("\n");
  // create array which will hold our data:
  dataProvider = [];
  
  // loop through all rows
  for (var i = 0; i < rows.length; i++){
    // this line helps to skip empty rows
    if (rows[i]) {
      // our columns are separated by comma
      var column = rows[i].split(",");  
       
      // column is array now 
      // first item is date
      var date = new Date(column[0]);
      // second item is value of the second column
      var temp1 = parseFloat(column[1]);
      // third item is value of the third column 
      var temp2 = parseFloat(column[2]);
      // fourth item is value of the fourth column
      var temp3 = parseFloat(column[3]);
      // create object which contains all these items:
      dataObject = [];
      dataObject = [date, temp1, temp2, temp3];      
      // add object to dataProvider array
      dataProvider.push(dataObject);
    }
  }
  currentTimeStamp = dataProvider[dataProvider.length - 1][0];
  return dataProvider;

}


/***************************************************************
    Refresh all the data on the graph
****************************************************************/
function refreshGraph() {
  // update the graph with fresh data
  dataArray = loadCSV();
  g.updateOptions( { 'file': dataArray } );
}

/***************************************************************
    Update the graph data with the newest data point.
****************************************************************/
function updateGraph(newTimestamp, newDataPoints) {
  var row = [];
  // the timestamp must be a date object to work properly
  if (newTimestamp instanceof Date) {
    row.push(newTimestamp);
  } else {
    row.push(new Date(newTimestamp));
  }
  // Cycle through the data points if it is an array..
  if (newDataPoints instanceof Array){
    for (var i=0; i < newDataPoints.length; i++) {
      row.push(newDataPoints[i]);
    }
  } else {
  // ..otherwise, just the single data point.
  row.push(newDataPoints);
  }
  /*
    Remove the first element in the array and
    then apppend the new data.
    If the time period is 0 'All Time' then
    we don't remove the first element.
  */
  if (timeFilterHours > 0) {
    dataArray.splice(0, 1);
  }
  dataArray.push(row);
  g.updateOptions( { 'file': dataArray } );
}

/***************************************************************
    Refresh data on the main screen
             ...trying to get ride of this function
****************************************************************/
function updateData() {  
  // update current data section
  updateCurrentData();
}


/***************************************************************
    Refresh 'current data' section of the main screen
****************************************************************/
function updateCurrentData() {
  var url = "./php/currentdata.php";
  $.getJSON(url, function(data) {
    // Update the 'Current Data' section on the page
    $("#currentTs").text(data.ts);
    $("#currentS1").text(data.sensor1);
    $("#currentS2").text(data.sensor2);
    $("#currentS3").text(data.sensor3);
    $("#currentAverage").text(data.average);
    
    /* Check if the new timestamp is newer than the current
       timestamp value. If it is, then update the graph data. */
    ts = new Date(data.ts);
    if (ts > currentTimeStamp) {
      currentTimeStamp = ts;
      var temp1 = parseFloat(data.sensor1);
      var temp2 = parseFloat(data.sensor2);
      var temp3 = parseFloat(data.sensor3);
      var tempArray = [temp1, temp2, temp3];
      updateGraph(ts, tempArray);
    }
    
  });
}


/***************************************************************
    
    Document Ready
    
****************************************************************/
$("document").ready(function() {
  timeFilterHours = defaultTimeFilterHours;
  currentTimeStamp = 0;
  refreshGraph();
  updateData();
  
  // This is the polling function. Periodically checks for new info from the database.
  autoUpdate = setInterval(updateData, updateInterval);
  
  // Bind the toggle update button.
  $("#pausePlayButton").bind('click', toggleAutoPolling);
  
  /***************************************************************
    Set the 'average' value for the graph.
    This changes the rollPeriod option of the dygraph object.
  ****************************************************************/
  $('.avg-graph').bind('click', function() {
    var avg = $(this).data("avg");
    g.updateOptions( { 'rollPeriod': avg } );
    // Save the new value in the database.
    $.post("php/updateUserSettings.php", { graphAverage: avg } );
  });
  
  /***************************************************************
    Set the 'time period' value for the graph.
    This changes the 't' parameter that is sent to the php
    script that generates the csv.
  ****************************************************************/
  $('.time-graph').bind('click', function() {
    timeFilterHours = $(this).data("time");
    refreshGraph();
    $.post("php/updateUserSettings.php", { graphTimePeriod: timeFilterHours } );
  });
  
});

