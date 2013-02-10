<html>
<head>
<title>Temp Monitor - Test page</title>
</head>

<body>


<table border="1" cellpadding="10">
	<tr><td>
		<form name="checkDevice" action="testScripts.php">
			Check Device<br />
			Device Address: <input type="text" name="id" /><br />
			<input type="hidden" name="test" value="checkDevice">			
			<input type="submit" value="Submit">
		</form>
	</td></tr>	
	
	<tr><td>
		<form name="updateData" action="updateData.php">
			Update Data<br />				
			Device 1 Address: <input type="text" name="id[]" /><br />
			Device 1 data: <input type="text" name="data[]" /><br />
			Device 2 Address: <input type="text" name="id[]" /><br />
			Device 2 data: <input type="text" name="data[]" /><br />
			Device 3 Address: <input type="text" name="id[]" /><br />
			Device 3 data: <input type="text" name="data[]" /><br />
			<input type="checkbox" name="debug" value="true" />Debug<br />	
			<input type="submit" value="Submit">
		</form>
	</td></tr>
	<tr><td>
		<a href="php/debug/displayTable.php?table=data_period">Data Period Table</a><br />
		<a href="php/debug/displayTable.php?table=data_points">Data Points Table</a><br />
		<a href="php/debug/displayTable.php?table=sensor_info">Sensor Info Table</a><br />
		<a href="php/debug/displayTable.php?table=wifi_stats">Wifi Stats Table</a><br />
		<a href="php/debug/displayTable.php?table=wifi_info">Wifi Info Table</a><br />
		<a href="php/debug/displayTable.php?table=system_settings">System Settings Table</a><br />
                <a href="php/debug/displayTable.php?table=user_info">User Info Table</a><br />
	</td></tr>
	<tr><td>
		<a href="getTempCsv.php">Get temperature CSV file</a><br />
		<a href="displayGraph.php">View graph</a><br />
	</td></tr>
</table>






</body>
</html>



