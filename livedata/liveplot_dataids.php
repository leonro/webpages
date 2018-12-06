<form name="liveplot_dataid" method="POST" action="index.php?option=com_jumi&fileid=9" >

<h4>Datenquelle für Live-Diagramm auswählen:</h4>
<?php

function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

require($_SERVER['DOCUMENT_ROOT'].'/iot/dbinfo.php');

// Create connection (new)
$con = new mysqli($host, $dbuser, $dbpasswd, $db);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

debug_to_console("Databank verbunden");

   // select sensorids from SENSORS
   $sql = "SELECT DISTINCT SensorID FROM DATAINFO";
   $result = mysqli_query($con, $sql);
   // for each sensorid check 0001 table for current data (less then 1 min old)
   echo '<p>Verfügbare Live Daten ...</p>';
   echo '<p><select name="dataid">';
   while($row = mysqli_fetch_array($result)) {
       $dataid = $row[SensorID] . '_0001';
       // Check if a table of name dataid is existing
       $sqltab = 'SHOW TABLES LIKE "' . $dataid . '"';
       $resulttab = mysqli_query($con, $sqltab);
       $found = mysqli_num_rows($resulttab);
       if ($found > 0) {
           // Check Sampling rate - only select data with sr < 120 sec
           $sqlsr = 'SELECT DataSamplingRate FROM DATAINFO WHERE DataID = "' . $dataid . '"';
           $resultsr = mysqli_query($con, $sqlsr);
           $srline = mysqli_fetch_array($resultsr);
           $sr = $srline[DataSamplingRate];
           if ($sr == "") { 
            $sr = 1;
           }
           // Check last time stamp in data table
           $sqlactual = 'SELECT time FROM ' . $dataid . ' ORDER BY time DESC LIMIT 1';
           $resultactual = mysqli_query($con, $sqlactual);
           $line = mysqli_fetch_array($resultactual);
           debug_to_console($dataid);
           $dtsplit = explode(".",$line[time]);
           $dt = $dtsplit[0];
           $lastdate = strtotime($dt);
           debug_to_console($dt);
           debug_to_console($lastdate);
           date_default_timezone_set('UTC');
           debug_to_console("AND NOW");
           debug_to_console(date("Y-m-d H:i:s"));
           $now = strtotime(date("Y-m-d H:i:s"));
           debug_to_console($now);

           $now2 = new DateTime();
           debug_to_console($now2->format('Y-m-d H:i:s'));
           $diff = round(abs($now - $lastdate));
           debug_to_console($diff);
           debug_to_console($sr);
           echo $line[time] . " " . $sr;
           if ($sr < 500 and $sr > 0  and $diff < 86400) {
               // if time == current and not $dataid startswith BLV_ then
               echo '<option value="' . $dataid . '">' . $dataid . '</option>';
           }
       }
   }
   echo '<p>Details finden sich auf der nächsten Seite</p>';
   mysqli_close($con);
   echo '</select></p>';
?>


<div align="left"><input type="submit" name="submit" value="Weiter"></div
</form>

