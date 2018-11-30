<form name="dataportal_live_keys_en" method="POST" action="index.php?option=com_jumi&fileid=6">

<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/data/dbinfo.php');

   $con = mysql_connect($host, $dbuser, $dbpasswd) or die(mysql_error());

   mysql_select_db($db, $con) or die("Unable to select database");

   $dataid = $_POST['dataid'];

   echo '<center><h3> ' . $dataid . ' </h3></center>';

   $sqldesc = "SELECT StationID, SensorID, DataSamplingRate, ColumnContents, ColumnUnits, DataTerms FROM DATAINFO WHERE DataID = '" . $dataid . " '";
   $result = mysql_query($sqldesc);
   while($row = mysql_fetch_array($result)) {
       $stationid = $row[StationID];
       $sensorid = $row[SensorID];
       //echo "<p>1. List: " . $row[StationID] . " " . $row[SensorID] . "</p>";
       $units = $row[ColumnUnits];
       $cont = $row[ColumnContents];
       $sr = $row[DataSamplingRate];
       $terms = $row[DataTerms];
   }
   $sqlfulldesc = "SELECT SensorGroup, SensorType, SensorDescription, SensorElements, SensorKeys FROM SENSORS WHERE SensorID = '" . $sensorid . " '";
   $result2 = mysql_query($sqlfulldesc);
   while($line = mysql_fetch_array($result2)) {
       //echo "<p>2. List: " . $line[SensorGroup] . " " . $line[SensorDescription] . " " . $line[SensorElements] . "</p>";
       $group = $line[SensorGroup];
       $type = $line[SensorType];
       $desc = $line[SensorDescription];
       $elem = $line[SensorElements];
       $keys = $line[SensorKeys];
   }

   echo '<h4>Basic Information: </h4>';
   echo '<div class="table-responsive">';
   echo '<table class="table table-bordered">';
   echo '<tr>';
   echo '<th>SensorID</th>';
   echo '<th>Group</th>';
   echo '<th>Type</th>';
   echo '<th>SamplingRate</th>';
   echo '</tr>';
   echo '<tr>';
   echo '<td>' . $sensorid . '</td>';
   echo '<td>' . $group . '</td>';
   echo '<td>' . $type . '</td>';
   echo '<td>' . $sr . '</td>';
   echo '</tr>';
   echo '</table>';
   echo '</div>';

   echo '<h4>Details: </h4>';
   echo '<div class="table-responsive">';
   echo '<table class="table table-borderd">';
   echo '<tr><td>';
   echo 'Description:';
   echo '</td></tr>';
   echo '<tr><td>';
   echo $desc;
   echo '</td></tr>';
   echo '<tr><td>';
   echo 'Data Terms:';
   echo '</td></tr>';
   echo '<tr><td>';
   echo $terms;
   echo '</td></tr>';
   echo '</table>';
   echo '</div>';
   //echo '<p>Select data: <br> ' . $keys . '</p>';

   echo '<h4>Select components: </h4>';
   echo '<div class="table-responsive">';
   echo '<table class="table table-striped">';
   echo '<tr>';
   $keylst = explode(",", $keys);
   $keynamelst = explode(",", $elem);
   foreach ($keylst as &$key) { 
       $ind = array_search($key, $keylst); // $key = 2;
       echo '<td><input type="checkbox" checked="checked" name="' . $key . '" value="' . $key . '"/> ' . $keynamelst[$ind] . ' (' . $key . ')</td>';
   }
   unset($key);
   echo '</tr>';
   echo '</table>';
   echo '</div>';
   // define updateinterval and window duration
   echo '<div class="table-responsive">';
   echo '<table class="table table-striped">';
   echo '<tr>';
   echo '<td>Update interval (sec):</td><td>1</td><td><input name="update" id="update" type="range" min="1" max="10" step="1" value="4"></td><td>10</td>';
   echo '</tr>';
   echo '<tr>';
   echo '<td>Displayed time period (min):</td><td>1</td><td><input name="duration" id="duration" type="range" min="1" max="1440" step="1" value="1"></td><td>1440</td>';
   echo '</tr>';
   echo '</table>';
   echo '</div>';

   echo '<input name="dataid" class="form" type="hidden" value="' . $dataid . '" />';
   echo '<input name="keys" class="form" type="hidden" value="' . $keys . '" />';
   echo '<input name="sr" class="form" type="hidden" value="' . $sr . '" />';
   echo '<input name="cont" class="form" type="hidden" value="' . $cont . '" />';
   echo '<input name="units" class="form" type="hidden" value="' . $units . '" />';
   echo '<input name="elem" class="form" type="hidden" value="' . $elem . '" />';
   echo '<input name="type" class="form" type="hidden" value="' . $type . '" />';

?>

<div align="left"><input type="submit" name="submit" value="plot data"></div>
</form>

