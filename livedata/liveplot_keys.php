<form name="liveplot_keys" method="POST" action="index.php?option=com_jumi&fileid=7">

<?php

require($_SERVER['DOCUMENT_ROOT'].'/iot/dbinfo.php');
// Create connection (new)
$con = new mysqli($host, $dbuser, $dbpasswd, $db);
// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

   $dataid = $_POST['dataid'];

   echo '<center><h3> ' . $dataid . ' </h3></center>';

   $sqldesc = "SELECT StationID, SensorID, DataSamplingRate, ColumnContents, ColumnUnits, DataTerms FROM DATAINFO WHERE DataID = '" . $dataid . " '";
   $result = mysqli_query($con, $sqldesc);
   while($row = mysqli_fetch_array($result)) {
       $stationid = $row[StationID];
       $sensorid = $row[SensorID];
       //echo "<p>1. List: " . $row[StationID] . " " . $row[SensorID] . "</p>";
       $units = $row[ColumnUnits];
       $cont = $row[ColumnContents];
       $sr = $row[DataSamplingRate];
       $terms = $row[DataTerms];
   }
   $sqlfulldesc = "SELECT SensorGroup, SensorType, SensorDescription, SensorElements, SensorKeys FROM SENSORS WHERE SensorID = '" . $sensorid . " '";
   $result2 = mysqli_query($con, $sqlfulldesc);
   while($line = mysqli_fetch_array($result2)) {
       //echo "<p>2. List: " . $line[SensorGroup] . " " . $line[SensorDescription] . " " . $line[SensorElements] . "</p>";
       $group = $line[SensorGroup];
       $type = $line[SensorType];
       $desc = $line[SensorDescription];
       $elem = $line[SensorElements];
       $keys = $line[SensorKeys];
   }

   echo '<h4>Übersicht: </h4>';
   echo '<div class="table-responsive">';
   echo '<table class="table table-bordered">';
   echo '<tr>';
   echo '<th>Sensor ID</th>';
   echo '<th>Gruppe</th>';
   echo '<th>Typ</th>';
   echo '<th>Erfassungsrate</th>';
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
   echo 'Beschreibung:';
   echo '</td></tr>';
   echo '<tr><td>';
   echo $desc;
   echo '</td></tr>';
   echo '<tr><td>';
   echo 'Nutzungsbedingungen (english):';
   echo '</td></tr>';
   echo '<tr><td>';
   echo $terms;
   echo '</td></tr>';
   echo '</table>';
   echo '</div>';
   //echo '<p>Select data: <br> ' . $keys . '</p>';

   echo '<h4>Komponenten auswählen: </h4>';
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
   echo '<td>Update Interval (Sek):</td><td>1</td><td><input name="update" id="update" type="range" min="1" max="10" step="1" value="4"></td><td>10</td>';
   echo '</tr>';
   echo '<tr>';
   echo '<td>Dargestellte Zeit (Min):</td><td>1</td><td><input name="duration" id="duration" type="range" min="1" max="1440" step="1" value="1"></td><td>1440</td>';
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

<div align="left"><input type="submit" name="submit" value="Diagramm"></div>
</form>

