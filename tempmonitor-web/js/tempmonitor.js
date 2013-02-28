/**************************************************************
    The autoUpdate variable stores the setInterval of 
    updateData(). With this being a variable, we can either 
    setInterval for a specific refresh time or we can 
    clearInterval to stop polling for new data.
****************************************************************/
var autoUpdate;

/**************************************************************
    The timeFilterHours variable stores how far back in time that
    the graph will display. A value of 0 will display all data.
****************************************************************/
var timeFilterHours;

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
  return dataProvider;

}


/***************************************************************
    Refresh all the data on the graph
****************************************************************/
function refreshGraph() {
  // update the graph
  g.updateOptions( { 'file': loadCSV() } );
}

/***************************************************************
    Refresh data on the main screen
             ...trying to get ride of this function
****************************************************************/
function updateData() {
  // grab the current CSV data
  //var newData = "php/getTempCsv.php?t=" + timeFilterHours;
  // update the graph
  //g.updateOptions( { 'file': loadCSV() } );
  
  // only do live updates if viewing the past 12 hours
  if (timeFilterHours <= 12) {
    refreshGraph();
  }
  
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


/***************************************************************
    
    Document Ready
    
****************************************************************/
$("document").ready(function() {
  timeFilterHours = "3";
  updateData();
  // This is the polling function. Periodically checks for new info from the database.
  autoUpdate = setInterval(updateData, 1000);
  
  $("#pausePlayButton").bind('click', toggleAutoPolling);
  
  /***************************************************************
    Set the 'average' value for the graph.
    This changes the rollPeriod option of the dygraph object.
  ****************************************************************/
  $('.avg-graph').bind('click', function() {
    var avg = $(this).data("avg");
    g.updateOptions( { 'rollPeriod': avg } );
  });
  
  /***************************************************************
    Set the 'time period' value for the graph.
    This changes the 't' parameter that is sent to the php
    script that generates the csv.
  ****************************************************************/
  $('.time-graph').bind('click', function() {
    timeFilterHours = $(this).data("time");
    refreshGraph();
  });
  
});