<form name="dataportal_live_dataids_en" method="POST" action="index.php?option=com_jumi&fileid=5" >

<h4>Select data source for live view:</h4>
<p>(Details on the available instruments can be found here (coming soon) and also are displayed on the next page)</p>

<?php

require($_SERVER['DOCUMENT_ROOT'].'/data/dbinfo.php');

$con = mysql_connect($host, $dbuser, $dbpasswd) or die(mysql_error());

   mysql_select_db($db, $con) or die("Unable to select database");

   // select sensorids from SENSORS
   $sql = "SELECT DISTINCT SensorID FROM DATAINFO";
   $result = mysql_query($sql);
   // for each sensorid check 0001 table for current data (less then 1 min old)
   echo '<p>Available for live view ...</p>';
   echo '<p><select name="dataid">';
   while($row = mysql_fetch_array($result)) {
       $dataid = $row[SensorID] . '_0001';
       // Check if a table of name dataid is existing
       $sqltab = 'SHOW TABLES LIKE "' . $dataid . '"';
       $resulttab = mysql_query($sqltab);
       $found = mysql_num_rows($resulttab);
       if ($found > 0) {
           // Check Sampling rate - only select data with sr < 120 sec
           $sqlsr = 'SELECT DataSamplingRate FROM DATAINFO WHERE DataID = "' . $dataid . '"';
           $resultsr = mysql_query($sqlsr);
           $srline = mysql_fetch_array($resultsr);
           $sr = $srline[DataSamplingRate];
           // Check last time stamp in data table
           $sqlactual = 'SELECT time FROM ' . $dataid . ' ORDER BY time DESC LIMIT 1';
           //echo $sqlactual;
           $resultactual = mysql_query($sqlactual);
           $line = mysql_fetch_array($resultactual);
           echo $line[time] . " " . $sr;
           if ($sr < 120 and $sr > 0) {
               // if time == current and not $dataid startswith BLV_ then
               echo '<option value="' . $dataid . '">' . $dataid . '</option>';
           }
       }
   }
   echo '<p>data and instrument details will be shown in the next page </p>';
   mysql_close();
   echo '</select></p>';
?>

<div align="left"><input type="submit" name="submit" value="submit query"></div
</form>

