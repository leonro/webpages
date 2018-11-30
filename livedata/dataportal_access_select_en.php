<form name="dataportal_access_select_en" method="POST" action="data_portal_access_form" >

<h4>Select readily available data for download:</h4>
<p>(Please note that only limited time ranges are available here. Furthermore completeness and permanent access are not guaranteed. This is a )</p>

<?php
// table
// 1. Group
// DataID sr type Mintime maxtime components
// DataID sr type Mintime maxtime components
// DataID sr type Mintime maxtime components
// DataID sr type Mintime maxtime components

// 2. Group
// Sensor type Mintime maxtime components
//

// button_get -> opens form with contact details (e-mail) and captcha

   require($_SERVER['DOCUMENT_ROOT'].'/data/dbinfo.php');

   $con = mysql_connect($host, $dbuser, $dbpasswd) or die(mysql_error());

   mysql_select_db($db, $con) or die("Unable to select database");

   // select sensorids from SENSORS
   $sql = "SELECT DataID, DataSamplingRate, SensorID FROM DATAINFO";
   $result = mysql_query($sql);
   while($row = mysql_fetch_array($result))
       $dataid = $row[DataID];
       $sensorid = $row[SensorID];
       $sr = $row[DataSamplingRate];
       $sqltab = 'SHOW TABLES LIKE "' . $dataid . '"';
       $resulttab = mysql_query($sqltab);
       $found = mysql_num_rows($resulttab);
       if ($found > 0) {
           // DATAID and DataTable existing
           // Get SensorData
           $sqlsr = 'SELECT SensorGroup, SensorType, SensorKeys, SensorElements FROM SENSORS WHERE SensorID = "' . $sensorid . '"';
           $resultsr = mysql_query($sqlsr);
           $srline = mysql_fetch_array($resultsr);
           $group = $srline[SensorGroup];
           $type = $srline[SensorType];
           $keys = $srline[SensorKeys];
           $elements = $srline[SensorElements];
           // Get TimeRange
           $sqlactual = 'SELECT time FROM ' . $dataid . ' ORDER BY time DESC LIMIT 1';
           $resultactual = mysql_query($sqlactual);
           $line = mysql_fetch_array($resultactual);
           $endtime = $line[time];
           $sqlactual = 'SELECT time FROM ' . $dataid . ' ORDER BY time LIMIT 1';
           $resultactual = mysql_query($sqlactual);
           $line = mysql_fetch_array($resultactual);
           $starttime = $line[time];
           // append data to dict
           // append group to grouplst if not yet contained
           };

   mysql_close();

   echo '<div class="table-responsive">';
   echo '<table class="table table-bordered">';
   for ($grouplst in &$elem) {
       echo '<tr>';
       echo '<th>' . $elem . '</th>';
       echo '<th></th>';
       echo '<th></th>';
       echo '<th></th>';
       echo '<th></th>';
       echo '<th></th>';
       echo '</tr>';
       // DataID sr type Mintime maxtime components
       echo '<tr>';
       echo '<th>DataID</th>';
       echo '<th>Type</th>';
       echo '<th>SamplingRate</th>';
       echo '<th>Available since</th>';
       echo '<th>Available until</th>';
       echo '<th>Components</th>';
       echo '</tr>';
       for () {
           if ($group == $elem) {
               echo '<tr>';
               echo '<td><input type="checkbox" name="' . $dataid . '" value="' . $dataid . '"/> ' . $dataid . '</td>';
               echo '<td>' . $type . '</td>';
               echo '<td>' . $sr . '</td>';
               echo '<td>' . $mintime . '</td>';
               echo '<td>' . $maxtime . '</td>';
               echo '<td>';
               $keylst = explode(",", $keys);
               $keynamelst = explode(",", $elem);
               foreach ($keylst as &$key) { 
                   $ind = array_search($key, $keylst); // $key = 2;
                   echo '<input type="checkbox" checked="checked" name="' . $key . '" value="' . $key . '"/> ' . $keynamelst[$ind] . ' (' . $key . ')';
               echo '</td>'
               echo '</tr>';
               }
           }
       }
   echo '</table>';
   echo '</div>';
?>

<div align="left"><input type="submit" name="submit" value="submit query"></div
</form>

